<?php

namespace App\Crawler\Dom;

use App\Crawler\Site\AbstractUrl;
use App\Lib\Helper\AbsoluteUrl;
use App\Lib\PhpUri;
use App\Models\CrawlUrl;
use Symfony\Component\DomCrawler\Crawler;

class DomCrawler
{
    public Crawler $dom_crawler;

    public AbstractUrl $url;

    public string $current_url;

    public function __construct(AbstractUrl $url, string $current_url, string $html)
    {
        $this->dom_crawler = new Crawler($html);
        $this->url = $url;
        $this->current_url = $current_url;
    }

    public static function create(AbstractUrl $url, string $current_url, string $html)
    {
        return new self($url, $current_url, $html);
    }

    public function getData(): array
    {
        $dom = $this->dom_crawler;
        $infos = json_decode($this->url->should_get_info);

        $array = [];

        foreach ($infos as $key => $info) {
            $info = explode("|", $info);

            $type_of_data = $info[0];
            $filter = $info[1];

            switch ($type_of_data) {
                case 'array':
                    $array[$key] = $dom->filter($filter)->each(function (Crawler $node, $i) {
                        return $node->text();
                    });
                    $dom = $this->dom_crawler;
                    break;

                case 'text':
                    $array[$key] = $dom->filter($filter)->text();
                    $dom = $this->dom_crawler;
                    break;

                case 'href':
                    $download_link = $dom->filter($filter)->attr('href');
                    $array[$key] = PhpUri::parse($this->url->site)->join($download_link);
                    $dom = $this->dom_crawler;
                    break;

                case 'img':
                    $image_link = $dom->filter($filter)->attr('src');
                    $array[$key] = PhpUri::parse($this->url->site)->join($image_link);
                    $dom = $this->dom_crawler;
                    break;
            }
        }

        return $array;
    }

    public function getUrlForMultiCrawl(bool $multi_crawl = true): array
    {
        $urls_selector = $this->dom_crawler->filter('a');
        $urls = [];

        foreach ($urls_selector as $key => $item) {
            $item = $item->getAttribute('href');

            $item = AbsoluteUrl::url_to_absolute($this->url->site, $this->current_url, $item);

            if (is_null($item)) continue;

            if ($this->url->ignore_page_child && $this->current_url != $this->url->site && $this->current_url != $this->url->startUrls()) {
                if ($this->ignorePageChild($item)) continue;
            }

            if ($this->exists($item)) continue;

            if ((!$this->url->shouldCrawl($item) && !$this->url->shouldGetData($item)) || $this->skipUrl($item)) continue;

            $urls[] = $item;
        }

        $urls = array_unique(array_filter($urls));

        if ($multi_crawl && count($urls) > 0) {
            list($array_save_to_database, $array_for_multi_crawl) = array_chunk($urls, ceil(count($urls) / 2));

            foreach ($array_save_to_database as $url) {
                CrawlUrl::create([
                    'site' => $this->url->site,
                    'parent' => $this->current_url,
                    'url' => $url,
                    'url_hash' => hashUrl($url)
                ]);
            }
            return $array_for_multi_crawl ?? [];
        } else {
            foreach ($urls as $url) {
                CrawlUrl::create([
                    'site' => $this->url->site,
                    'parent' => $this->current_url,
                    'url' => $url,
                    'url_hash' => hashUrl($url)
                ]);
            }
            return $urls;
        }
    }

    public function skipUrl(string $url): bool
    {
        $list_parent_skip_url = explode("|", $this->url->skip_url);

        foreach ($list_parent_skip_url as $skip_url) {
            if (preg_match($skip_url, $url)) return true;
        }

        return false;
    }

    public function ignorePageChild(string $url): bool
    {
        if (str_starts_with($url, $this->current_url)) return true;
        return false;
    }

    public function exists(string $url): bool
    {
        return CrawlUrl::where('url_hash', hashUrl($url))->exists();
    }
}
