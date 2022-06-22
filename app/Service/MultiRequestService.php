<?php

namespace App\Service;

use App\Crawler\CrawlUrl as UrlCrawler;
use App\Crawler\Dom\DomCrawler;
use App\Crawler\Site\AbstractUrl;
use App\Crawler\Store\StoreData;
use App\Enum\CrawlStatus;
use App\Enum\DataStatus;
use App\Jobs\MultipleHttpCrawl;
use App\Models\CrawlUrl;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Self_;
use Vuh\CliEcho\CliEcho;

class MultiRequestService
{
    private int $concurrency = 5;
    private Client $client;
    private array $headers;
    protected array $options = [];
    protected array $urls = [];
    protected string $method;
    protected static AbstractUrl $site;
    protected string $parent_url;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36'
        ];
    }

    public static function newRequest(Client $client)
    {
        return new self($client);
    }

    public function get()
    {
        $this->method = 'GET';
        $this->send();
    }

    public function post()
    {
        $this->method = 'POST';
        $this->send();
    }

    protected function send()
    {
        $client = $this->client;

        $requests = function ($urls) use ($client) {
            foreach ($urls as $url) {
                if (is_string($url)) {
                    yield new Request($this->method, $url, $this->headers);
                } else {
                    yield $url;
                }
            }
        };

        $pool = new Pool($client, $requests($this->urls), [
            'concurrency' => $this->concurrency,
            'fulfilled' => function (Response $response, $index) {
                $current_url = $this->urls[$index];
                if (!CrawlUrl::where('url_hash', hashUrl($current_url))->exists()) {
                    CliEcho::infonl("Goto: [$current_url] - Time : " . Carbon::now()->toDateTimeString());
                    $url_crawler = CrawlUrl::create([
                        'site' => self::$site->site,
                        'parent' => $this->parent_url,
                        'url' => $current_url,
                        'url_hash' => hashUrl($current_url),
                        'status' => CrawlStatus::INIT,
                    ]);

                    $html = $response->getBody()->getContents();
                    $dom_crawler = DomCrawler::create(self::$site, $this->parent_url, $html);

                    if (self::$site->shouldGetData($current_url)) {
                        $data = $dom_crawler->getData();
                        if (StoreData::store($this->urls[$index], $data)) {
                            $url_crawler->data_status = DataStatus::HAS_DATA;
                        } else {
                            $url_crawler->data_status = DataStatus::GET_DATA_ERROR;
                        }
                    }
                    $url_crawler->status = CrawlStatus::DONE;
                    $url_crawler->save();

                    $dom_crawler->getUrlForMultiCrawl(false);
                }
            },
            'rejected' => function (RequestException $reason, $index) {
                Log::error($reason->getMessage());
            },
            'options' => $this->options
        ]);

        $promise = $pool->promise();
        $promise->wait();
    }

    public function urls(array $urls): static
    {
        $this->urls = $urls;
        return $this;
    }

    public function site(AbstractUrl $site): static
    {
        self::$site = $site;
        return $this;
    }

    public function parentUrl(string $parent_url): static
    {
        $this->parent_url = $parent_url;
        return $this;
    }
}
