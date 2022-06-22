<?php

namespace App\Jobs;

use App\Crawler\Site\Url;
use App\Service\HttpService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MultipleHttpCrawl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $urls;

    protected string $parent_url;

    protected Url $site;

    public int $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Url $site, array $urls, string $parent_url)
    {
        $this->onQueue('multi_crawl');
        $this->urls = $urls;
        $this->parent_url = $parent_url;
        $this->site = $site;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            HttpService::multiRequest($this->site, $this->urls, $this->parent_url);
        } catch
        (\Exception $e) {
            \Log::error($e);
        }
    }
}
