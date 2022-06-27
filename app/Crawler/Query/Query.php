<?php

namespace App\Crawler\Query;

use App\Crawler\Site\AbstractUrl;
use App\Crawler\Site\Url;
use App\Enum\CrawlStatus;
use App\Enum\DataStatus;
use App\Models\CrawlUrl;
use App\Crawler\CrawlUrl as UrlCrawler;
use GuzzleHttp\Psr7\Uri;
use PDO;
use Vuh\CliEcho\CliEcho;

class Query
{
    public function __construct(protected AbstractUrl $site, protected bool $reset, protected bool $multithreading)
    {
        if ($reset) $this->reset();

        foreach ($site->startUrls() as $url) {
            $url_crawler = new UrlCrawler($site, new Uri($url));

            if ($this->push($url_crawler)) {
                CliEcho::successnl("Site : " . $site->site . " Added " . $url_crawler->url);
            }
        }
    }

    public function hasPendingUrls()
    {
        return CrawlUrl::where('site', $this->site->site)
            ->where('status', CrawlStatus::INIT)
            ->exists();
    }

    public function first()
    {
        return \DB::transaction(function () {
            $first = CrawlUrl::where('site', $this->site->site)
                ->where('status', CrawlStatus::INIT)
                ->when($this->multithreading, function ($q) {
                    $q->lock($this->getLockForPopping());
                })
                ->first();

            if ($first) {
                $first->status = CrawlStatus::VISITING;
                $first->visited = $first->visited + 1;
                $first->save();

                return new UrlCrawler($this->site, new Uri($first->url), $first->id);
            } else {
                return null;
            }
        });
    }

    protected function getLockForPopping(): bool|string
    {
        $databaseEngine = \DB::getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME);
        $databaseVersion = \DB::getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION);

        if ($databaseEngine == 'mysql' && !strpos($databaseVersion, 'MariaDB') && !strpos($databaseVersion, 'TiDB') && version_compare($databaseVersion, '8.0.1', '>=') ||
            $databaseEngine == 'pgsql' && version_compare($databaseVersion, '9.5', '>=')) {
            return 'FOR UPDATE SKIP LOCKED';
        }

        return true;
    }


    private function push(UrlCrawler $url_crawler): \Illuminate\Database\Eloquent\Model|bool|CrawlUrl
    {
        if (self::exists($url_crawler->url)) {
            return false;
        }

        return CrawlUrl::create([
            'site' => $url_crawler->site->rootUrl(),
            'url' => $url_crawler->url,
            'url_hash' => hashUrl($url_crawler->url),
            'parent' => $url_crawler->parent_url,
            'status' => $url_crawler->status,
            'data_status' => $url_crawler->data_status,
            'visited' => $url_crawler->visited,
        ]);
    }

    private function reset(bool $keep_data = false): void
    {
        CrawlUrl::where('site	', $this->site->site)
            ->when($keep_data, function ($query) {
                $query->where('data_status', '<>', DataStatus::HAS_DATA);
            })
            ->delete();
    }

    public function exists(string $url): bool
    {
        return CrawlUrl::where('url_hash', hashUrl($url))->exists();
    }
}
