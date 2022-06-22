<?php

namespace App\Lib\Helper;

use App\Lib\PhpUri;

class AbsoluteUrl
{
    public static function url_to_absolute($baseUrl, $current_url, $relativeUrl)
    {
        $relativeUrl = preg_replace("/(#\w+)$/", '', $relativeUrl); //delete fragment #tag

        if ($relativeUrl == './' || $relativeUrl == '#') {
            return null;
        }

        if (substr($relativeUrl, 0, 1) == '/') {
            $relativeUrl = $baseUrl . $relativeUrl;
        } else if (filter_var($relativeUrl, FILTER_VALIDATE_URL) === FALSE) {
            $array = explode('/', $current_url);
            array_pop($array);
            $relativeUrl = implode('/', $array) . '/' . $relativeUrl;
        }
        return PhpUri::parse($baseUrl)->join($relativeUrl);
    }
}
