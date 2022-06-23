<?php

namespace App\Upload;

use App\Enum\DataStatus;
use App\Enum\UploadStatus;
use App\Models\Data;

class UploadDocumentQueue
{
    protected static ?int $id;
    protected array $where;

    public function __construct(bool $reload)
    {
        self::$id = null;
        $where = [UploadStatus::NO, UploadStatus::INIT];

        //Re-upload
        if ($reload) {
            $where = [
                UploadStatus::SUCCESS,
                UploadStatus::ERROR,
                UploadStatus::FAIL,
            ];
        }

        $this->where = $where;
    }

    public function hasPendingData(): bool
    {
        return Data::whereIn('upload_status', $this->where)
            ->when(self::$id, function ($query) {
                $query->where('id', '<>', self::$id);
            })
            ->exists();
    }

    public function firstPendingData()
    {
        return \DB::transaction(function () {
            $first = Data::whereIn('upload_status', $this->where)
                ->when(self::$id, function ($query) {
                    $query->where('id', '<>', self::$id);
                })
                ->first();

            if ($first) {
                $first->update([
                    'upload_status' => UploadStatus::INIT,
                    'uploaded_at' => now(),
                ]);

                $uploadDocument = [
                    'source' => $first->url,
                    'content' => $first->title,
                ];
                $this->setId($first->id);

                return $uploadDocument;
            }
            return null;
        });
    }

    public function setId(int $id)
    {
        self::$id = $id;
    }

    public function setStatus(int $status)
    {
        return Data::where('id', $this->getId())
            ->update(['upload_status' => $status]);
    }

    public function getId()
    {
        return self::$id;
    }

}
