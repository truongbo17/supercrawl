<?php

namespace App\Upload;

use App\Enum\UploadStatus;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Vuh\CliEcho\CliEcho;
use GuzzleHttp;

class UploadData
{
    protected static int $limit = 0;
    protected static int $successUpload = 0;
    protected static int $timeStart = 0; //time start upload
    protected int $timeLimit = 60; //per second router rate limit
    protected int $waitTime = 60; //wait time upload then upload max request in per minute

    public function __construct(protected string $host, protected string $token, protected int $limitInput, protected UploadDocumentQueue $queueUpload, protected int $data_in_request)
    {
        self::$timeStart = Carbon::now()->timestamp;
    }

    public function run()
    {
        while ($this->queueUpload->hasPendingData()) {

            if (!self::checkTime()) {
                CliEcho::warningnl("Waiting $this->waitTime second ...");
                //wait one minute after continues request to host
                $this->waitUpload();
            }

            $data['keywords'] = $this->queueUpload->getLimitData($this->data_in_request);

            if (is_null($data)) continue;

            try {
                CliEcho::infonl("Upload data to Host : [$this->host] - Time : " . Carbon::now()->toDateTimeString());

                $response = (new GuzzleHttp\Client())->post($this->host, [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Basic ' . $this->token
                    ],
                    GuzzleHttp\RequestOptions::JSON => $data
                ]);

                $res = $response->getBody()->getContents();
                dump($res);
                $code = $response->getStatusCode();

                if ($code == 200) {
                    $this->queueUpload->setStatus(UploadStatus::SUCCESS);
                    self::$successUpload++;
                } else {
                    $this->queueUpload->setStatus(UploadStatus::FAIL);
                }
            } catch (GuzzleException $exception) {
                CliEcho::errornl($exception->getMessage());
                $this->queueUpload->setStatus(UploadStatus::ERROR);
            }

            self::$limit++;
        }

        return self::$successUpload++;
    }

    public function checkTime()
    {
        if (Carbon::now()->timestamp - self::$timeStart < $this->timeLimit && self::$limit < $this->limitInput) {
            return true;
        }

        //wait time
        $this->waitTime = $this->timeLimit - (Carbon::now()->timestamp - self::$timeStart) + 1;
        return false;
    }

    //tối ưu thời gian chờ
    public function waitUpload()
    {
        //sleep time
        if ($this->waitTime > 0) sleep($this->waitTime);

        //reset time and limit
        self::$timeStart = Carbon::now()->timestamp;
        self::$limit = 0;

        //refresh
        self::run();
    }
}
