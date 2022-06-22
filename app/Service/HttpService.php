<?php

namespace App\Service;

use App\Crawler\Site\AbstractUrl;
use GuzzleHttp\Client;

class HttpService
{
    private static Client|null $client = null;

    public static function getClient(): ?Client
    {
        if (self::$client == null) {
            self::$client = new Client(config('crawl.browsers.guzzle'));
        }
        return self::$client;
    }

    public static function get(string $url)
    {
        $client = self::getClient();
        $html = $client->get($url)->getBody()->getContents();
        if (mb_stripos($html, "</a>") === false && mb_stripos($html, "<body") === false) {
            $html = mb_convert_encoding($html, "UTF-8", "UTF-16LE");
        } elseif (mb_stripos($html, "charset=Shift_JIS")) {
            $html = mb_convert_encoding($html, "UTF-8", "SJIS");
            $html = str_replace("charset=Shift_JIS", "charset=UTF-8", $html);
        }
        return $html;
    }

    public static function multiRequest(AbstractUrl $site, array $urls, string $parent_url)
    {
        $client = self::getClient();
        MultiRequestService::newRequest($client)
            ->site($site)
            ->parentUrl($parent_url)
            ->urls($urls)
            ->get();
    }
}
