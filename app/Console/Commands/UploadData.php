<?php

namespace App\Console\Commands;

use App\Upload\UploadDocumentQueue;
use Illuminate\Console\Command;

class UploadData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:auto
    {--host= : Host upload}
    {--token= : Token auth}
    {--limit= : Limit document upload}
    {--reload : Re-upload already uploaded documents }
    ';


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
        $host = $this->option('host') ?? config('upload.host');
        $token = $this->option('token') ?? config('upload.token'); //Currently not using token
        $limit = $this->option('limit') ?? 60; // limit request to one minute
        $reload = $this->option('reload');
        $data_in_request = 1;

        $this->info("Start upload to : $host !!!");

        $uploadDocument = new \App\Upload\UploadData($host, $token, $limit, new UploadDocumentQueue($reload), $data_in_request);
        $countUpload = $uploadDocument->run();

        $this->info("Upload " . $countUpload * $data_in_request . " document success !!!");

        return self::SUCCESS;

    }
}
