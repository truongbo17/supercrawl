<?php


namespace App\Lib;

/***/

class StringUtils
{
    public static function normalize(?string $string)
    {
        $string = trim($string);
        $string = mb_strtolower($string);
        $string = preg_replace("/\p{Z}+/ui", " ", $string);
        $string = preg_replace("/[^\p{M}\w\s]+/ui", " ", $string);
        $string = preg_replace("/\s{2,}/", " ", $string);
        return trim($string);
    }

    public static function wordsCount(string $string)
    {
        return count(self::extractWords($string));
    }

    public static function countUniqueWords(string $string)
    {
        return count(self::extractUniqueWords($string));
    }

    public static function charactersCount(string $string)
    {
        if (preg_match_all("/\p{L}/ui", $string, $matches)) {
            return count($matches[0]);
        }
        return 0;
    }

    public static function extractWords(string $string)
    {
        $string = preg_replace("/[\(\[].*[\)\]]/", " ", $string);
        $string = preg_replace("/[\W\p{Z}\p{N}]/u", " ", $string);
        $string = preg_replace("/\s{2,}/", " ", $string);

        $latin = $cjk = $hangul = [];
        if (preg_match_all("/[a-zàâçéèêëîïôûùüÿñæœ]{2,}/ui", $string, $matches)) {
            $latin = $matches[0];
        }
        if (preg_match_all("/[\p{Hiragana}\p{Katakana}\p{Han}]/ui", $string, $matches)) {
            $cjk = $matches[0];
        }
        if (preg_match_all("/[\p{Hangul}]/ui", $string, $matches)) {
            $hangul = $matches[0];
        }

        return [...$latin, ...$cjk, ...$hangul];
    }

    public static function extractUniqueWords(string $string)
    {
        $words = self::extractWords($string);

        $unique_words = [];
        foreach ($words as $word) {
            if (strlen($word) < 3) {
                continue;
            }
            $word = preg_replace("/[^\p{L}]/u", "", $word);
            if (!$word) {
                continue;
            }
            $word = mb_strtolower($word);
            if (in_array($word, $unique_words)) {
                continue;
            }
            $unique_words[] = $word;
        }
        return $unique_words;
    }

    public static function isLatin(string $string)
    {
        $string = self::trim($string);
        if (mb_strlen($string) < 2) {
            return !preg_match("/[\p{Hiragana}\p{Katakana}\p{Han}\p{Hangul}]/ui", $string);
        }
        return !preg_match("/[\p{Hiragana}\p{Katakana}\p{Han}\p{Hangul}]\s?[\p{Hiragana}\p{Katakana}\p{Han}\p{Hangul}]/ui", $string);
    }

    public static function isJapanese(string $string)
    {
        //http://www.rikai.com/library/kanjitables/kanji_codes.unicode.shtml
        return preg_match("/[\p{Hiragana}\p{Katakana}\p{Han}]\s?[\p{Hiragana}\p{Katakana}\p{Han}]/ui", $string);
    }

    public static function detectLanguage(string $string)
    {
        $language = (new \LanguageDetection\Language(['en', 'id', 'es', 'pt-BR', 'pt-PT', 'it', 'de', 'sv', 'fi', 'fr', 'zh-Hans', 'zh-Hant', 'tr', 'tr_TR', 'nl', 'ru', 'ja', 'ko', 'pl', 'ro', 'th', 'ar']))
            ->detect($string)
            ->bestResults()
            ->close();
        $language = array_keys($language)[0] ?? 'en';
        if ($language == 'pt-BR' || $language == 'pt-PT') {
            $language = 'pt';
        }
        if ($language == 'tr_TR') {
            $language = 'tr';
        }
        if ($language == 'zh-Hans' || $language == 'zh-Hant') {
            $language = 'zh';
        }
        if ($language == 'sv') {
            $language = 'se';
        }
        return $language;
    }

    public static function detectLanguageByTika(string $string)
    {
        $client = new \HocVT\TikaSimple\TikaSimpleClient();

        $language = $client->language($string);
        if ($language == 'zh-TW' || $language == 'zh-CN') {
            $language = 'zh';
        }
        if ($language == 'sv') {
            $language = 'se';
        }
        return $language;
    }

    public static function trim($str)
    {
        return preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $str);
    }
}

