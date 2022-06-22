<?php
/**
 * Giảm độ dài của chuỗi nhưng giữ lại một số thông tin 2 đầu để vẫn cảm nhận được sự khác nhau giữa các chuỗi trong 1
 * danh sách
 *
 * User: hocvt
 * Date: 2019-12-02
 * Time: 15:50
 */

namespace App\Lib;


class TextReducer {

    /**
     * Example
     *  Max length 70
     *  From "The preg_replace() function is an inbuilt function in PHP which is used to perform"
     *  To   "The preg_replace() function is an...on in PHP which is used to perform"
     *
     * @param $string
     * @param int $max_length
     * @param string $replacement
     *
     * @return string|string[]|null
     */
    public static function text($string, int $max_length = 100, string $replacement = "...") {
        $max_length = $max_length - mb_strlen($replacement);
        if ($max_length < 2) {
            return mb_substr($string, 0, 2) . $replacement;
        }
        $before_length = (int)($max_length / 2);
        return preg_replace("/^(.{" . $before_length . "})(.*)(.{" . ($max_length - $before_length) . "})$/ui",
            "$1" . $replacement . "$3",
            $string);
    }

    /**
     *
     * Example
     *  Max length 50
     *  From https://id.123dok.com/document/yr373wpy-buku-siswa-dan-buku-guru-kelas-viii-8-kurikulum-2013-edisi-revisi-2016-2017-semua-mata-pelajaran-b-indo-siswa.html
     *  To   https://id.123dok.com/document/...-indo-siswa.html
     *
     * @param $url
     * @param int $max_length
     * @param string $replacement
     *
     * @return string|string[]|null
     */
    public static function url($url, int $max_length = 100, string $replacement = "...") {
        if (mb_strlen($url) < $max_length) {
            return $url;
        }
        if (preg_match("/^(https?\:)?\/\/[^\/\.]+(\.[^\/\.]+)+/", $url, $matches)) {
            $domain = $matches[0];
            if (mb_strlen($domain) < $max_length + 6) {
                $right_text = mb_substr($url, -6);
                $remain_text = mb_substr($url, mb_strlen($domain), -6);
                return $domain . self::text($remain_text, $max_length - mb_strlen($domain . $right_text), $replacement) . $right_text;
            }
        }
        return self::text($url, $max_length, $replacement);
    }


    /**
     * Example
     *  Max length 50
     *  From sites.google.com/a/dongorgecommunitygroup.com/dongorgecommunitygroup
     *  To   sites.google.com/a/dongorgec...gorgecommunitygroup
     *
     * @param $path
     * @param int $max_length
     * @param string $replacement
     * @param string $directory_separate
     *
     * @return string|string[]|null
     */
    public static function path($path, int $max_length = 100, string $replacement = "...", string $directory_separate = "/") {
        if(mb_strlen( $path ) < $max_length ){
            return $path;
        }
        $directory_separate = preg_quote( $directory_separate, "/" );
        if(preg_match( "/^" . $directory_separate . "?[^" . $directory_separate . "]+/", $path, $matches)){
            $root = $matches[0];
            if(mb_strlen( $root ) < $max_length + 6){
                $right_text = mb_substr( $path, -6);
                $remain_text = mb_substr( $path, mb_strlen( $root ), -6);
                return $root . self::text( $remain_text, $max_length - mb_strlen( $root . $right_text ), $replacement ) . $right_text;
            }
        }
        return self::text( $path, $max_length, $replacement );
    }

}
