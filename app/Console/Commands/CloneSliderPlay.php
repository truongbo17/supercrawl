<?php

namespace App\Console\Commands;

use App\Models\Url;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class CloneSliderPlay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clone:slideplayer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone crawl url from slideplayer to multiple slideplayers';

    private array $clone_to_countries = [
        '.es',
        '.com.br',
        '.fr',
        '.it',
        '.info',
        '.pl',
        '.biz.tr',
        '.cz',
        '.org',
        '.hu',
        '.gr',
        '.in.th',
        '.nl',
        '.se',
        '.no',
        '.dk',
        '.fi',
        '.ae',
        '.rs',
        '.sk',
        '.bg',
        '.lt',
        '.co.il',
        '.si',
        '.vn',
        '.ro',
        '.lv',
        '.ee'
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $url_slider_play = Url::where('site', 'https://slideplayer.com')
            ->first()
            ->toArray();

        foreach ($this->clone_to_countries as $key => $country) {
            $this->info("Clone to country $country");

            $to_country = str_replace('.com', $this->clone_to_countries[$key], Arr::only($url_slider_play, [
                'site', 'url_start', 'should_crawl', 'should_get_data', 'config_root_url', 'should_get_info', 'skip_url', 'status', 'ignore_page'
            ]));

            try {
                Url::create($to_country);

                $this->line('Success');
            } catch (\Exception $e) {
                $this->warn($e->getMessage());
            }

        }
        return self::SUCCESS;
    }
}
