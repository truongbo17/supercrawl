<?php

namespace App\Crawler;

use App\Crawler\Site\AbstractUrl;
use Psr\Http\Message\UriInterface;
use App\Enum\DataStatus;
use App\Enum\CrawlStatus;

class CrawlUrl
{
    public int $data_status = DataStatus::INIT;

    public int $status = CrawlStatus::INIT;

    public int $visited = 0;

    public function __construct(public AbstractUrl $site, public UriInterface $url, public ?int $id = null, public ?UriInterface $parent_url = null)
    {

    }

    public function updateStatus()
    {
        \App\Models\CrawlUrl::where('id', $this->id)
            ->first()
            ->update([
                'status' => $this->status,
                'data_status' => $this->data_status,
            ]);
    }
}
