<?php

namespace App\Crawler\Store;

use App\Lib\StringUtils;
use App\Models\Data;
use Vuh\CliEcho\CliEcho;

class StoreData
{
    public static function store(string $url, array $data)
    {
        if (Data::where('title_hash', hashUrl($data['title']))->exists()) {
//            \Log::alert('Duplicate title : ' . $data['title'] . ' | hash : ' . hashUrl($data['title']));
            CliEcho::warningnl('Duplicate title : ' . $data['title']);
            return false;
        }

        dump($data);

        return Data::create([
            'title' => $data['title'],
            'title_hash' => hashUrl($data['title']),
            'url' => $url,
            'language' => StringUtils::detectLanguage($data['title']),
        ]);
    }
}
