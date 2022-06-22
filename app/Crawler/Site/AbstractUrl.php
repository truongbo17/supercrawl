<?php

namespace App\Crawler\Site;

abstract class AbstractUrl
{
    public int $id;
    public string $site;
    public string|null $driver_browser;
    public string $url_start;
    public string $should_crawl;
    public string $should_get_data;
    public string $should_get_info;
    public bool $config_root_url;
    public string|null $skip_url;
    public bool $ignore_page_child;
    public string $status;

    public function getStatus(): bool
    {
        if ($this->status === "RUNNING") return true;
        return false;
    }

    public function rootUrl(): string
    {
        return $this->site;
    }

    public function startUrls(): array
    {
        return explode(",", $this->url_start);
    }

    public function shouldCrawl(string $url)
    {
        return preg_match($this->should_crawl, $url);
    }

    public function shouldGetData(string $url)
    {
        return preg_match($this->should_get_data, $url);
    }
}
