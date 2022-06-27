<?php

namespace App\Console\Commands;

use App\Crawler\Crawler;
use App\Models\Url;
use App\Service\HttpService;
use Illuminate\Console\Command;

class AutoCrawl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:crawl
    {--site= : Site must has in database}
    {--reset : reset crawl}
    {--multithreading : Multithreading crawl}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl data from all website';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $site = $this->option('site');
        $reset = $this->option('reset');
        $multithreading = $this->option('multithreading');

        if (!Url::where('site', $site)->first() || is_null($site)) {
            $this->error('No site match in database....');
            return self::FAILURE;
        }

        Crawler::run($site, $reset, $multithreading);

        return self::SUCCESS;
    }
}
