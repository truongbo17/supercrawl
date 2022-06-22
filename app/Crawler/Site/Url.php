<?php

namespace App\Crawler\Site;

use App\Enum\UrlStatus;
use App\Models\Url as UrlModel;

class Url extends AbstractUrl
{
    public function __construct(string $site)
    {
        $url = UrlModel::where('site', $site)->first();

        $this->id = $url->id;
        $this->site = $url->site;
        $this->driver_browser = $url->driver_browser;
        $this->url_start = $url->url_start;
        $this->should_crawl = $url->should_crawl;
        $this->should_get_data = $url->should_get_data;
        $this->should_get_info = $url->should_get_info;
        $this->config_root_url = $url->config_root_url;
        $this->skip_url = $url->skip_url;
        $this->ignore_page_child = $url->ignore_page_child;
        $this->status = array_search($url->status, UrlStatus::asArray());
    }
}
