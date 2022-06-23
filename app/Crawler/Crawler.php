<?php

namespace App\Crawler;

use App\Crawler\Dom\DomCrawler;
use App\Crawler\Query\Query;
use App\Crawler\Site\Url;
use App\Crawler\Store\StoreData;
use App\Enum\CrawlStatus;
use App\Enum\DataStatus;
use App\Jobs\MultipleHttpCrawl;
use App\Service\Http\GHttp;
use App\Service\HttpService;
use App\Service\MultiRequestService;
use Carbon\Carbon;
use phpDocumentor\Reflection\Types\Self_;
use Vuh\CliEcho\CliEcho;

class Crawler
{
    public const MAX_CHECK_URL = 100;

    public static function run(string $site, bool $reset = false): void
    {
        $url = new Url($site);

        if (!$url->getStatus()) self::messageStatus();

        $check_status_url = 0;

        $query_crawl = new Query($url, $reset);
        while ($query_crawl->hasPendingUrls()) {
            $time_start = microtime(true);
            $check_status_url++;

            $time_first = microtime(true);
            $url_crawler = $query_crawl->first();
            echo "\n" . 'Total get first in seconds: ' . (microtime(true) - $time_first) . "\n";

            if (is_null($url_crawler)) continue;

            try {
                CliEcho::infonl("Goto: [$url_crawler->url] - Time : " . Carbon::now()->toDateTimeString());
                $time_html = microtime(true);
                $html = HttpService::get($url_crawler->url);
                echo "\n" . 'Total get html in seconds: ' . (microtime(true) - $time_html) . "\n";

                $url_crawler->status = CrawlStatus::DONE;
            } catch (\Exception $exception) {
                CliEcho::errornl($exception->getMessage());
                if (in_array($exception->getCode(), config('crawl.should_retry_status_codes'))) {
                    $url_crawler->status = CrawlStatus::INIT;
                } else {
                    $url_crawler->status = CrawlStatus::FAIL;
                }
                continue;
            }
            $time_dom_url = microtime(true);
            $dom_crawler = DomCrawler::create($url, $url_crawler->url, $html);

            $urls = $dom_crawler->getUrlForMultiCrawl();
            echo "\n" . 'Total get dom url in seconds: ' . (microtime(true) - $time_dom_url) . "\n";

            HttpService::multiRequest($url, $urls, $url_crawler->url);
//            MultipleHttpCrawl::dispatch($url, $urls, $url_crawler->url);

            $time_get_data = microtime(true);
            if ($url->shouldGetData($url_crawler->url)) {
                $data = $dom_crawler->getData();
                if (StoreData::store($url_crawler->url, $data)) {
                    $url_crawler->data_status = DataStatus::HAS_DATA;
                } else {
                    $url_crawler->data_status = DataStatus::GET_DATA_ERROR;
                }
            }
            $url_crawler->updateStatus();
            echo "\n" . 'Total get data url in seconds: ' . (microtime(true) - $time_get_data) . "\n";


            echo "\n" . 'Total crawl time in seconds: ' . (microtime(true) - $time_start) . "\n";

            if ($check_status_url == self::MAX_CHECK_URL) self::run($site, $reset);
        }

        self::messageSuccess();
    }

    public static function messageStatus()
    {
        CliEcho::successnl("Crawl is turn off...");
    }

    public static function messageSuccess()
    {
        CliEcho::successnl("Crawl is success...");
    }
}
