<?php

namespace App\Console\Commands;

use App\Service\HttpService;
use Illuminate\Console\Command;

class TestHttpService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:http';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $time_start = microtime(true);
        $html = HttpService::get("https://slideplayer.com/");
        echo "\n" . 'Total get html time in seconds: ' . (microtime(true) - $time_start);
    }
}
