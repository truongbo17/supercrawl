<?php
/**
 * A php library for converting relative urls to absolute.
 * Website: https://github.com/monkeysuffrage/phpuri
 *
 * <pre>

 * echo phpUri::parse('https://www.google.com/')->join('foo');
 * //==> https://www.google.com/foo
 * </pre>
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @author  P Guardiario <pguardiario@gmail.com>
 * @version 1.0
 */

namespace App\Lib;
/**
 * PhpUri
 */
class PhpUri
{

    /**
     * http(s)://
     * @var string
     */
    public $scheme;

    /**
     * www.example.com
     * @var string
     */
    public $authority;

    /**
     * /search
     * @var string
     */
    public $path;

    /**
     * ?q=foo
     * @var string
     */
    public $query;

    /**
     * #bar
     * @var string
     */
    public $fragment;

    protected $domains_multi_rules = [
        'journals.sub.uni-hamburg.de', 'opus.bsz-bw.de', 'opus4.kobv.de', 'physik.uzh.ch', 'archiv.ub.uni-heidelberg.de', '160.97.80.9:8080',
        'journals.lub.lu.se', 'apcz.umk.pl\/czasopisma\/index.php', 'czasopisma.ujd.edu.pl\/index.php', 'czasopisma.upjp2.edu.pl',
        'econjournals.sgh.waw.pl', 'pressto.amu.edu.pl\/index.php', 'czasopisma.uph.edu.pl\/index.php', 'czasopisma.uni.lodz.pl',
        'czasopisma.up.lublin.pl\/index.php', 'journals.agh.edu.pl', 'ojs.tnkul.pl\/index.php', 'czasopisma.mazowiecka.edu.pl\/index.php',
        'journals.umcs.pl', 'czasopisma.tnkul.pl\/index.php', 'ns2.journals.umcs.pl', 'czasopisma.uksw.edu.pl', 'ojs.academicon.pl',
        'journals.akademicka.pl', 'eczasopisma.p.lodz.pl', 'ojs.ihar.edu.pl\/index.php', 'journals.ur.edu.pl',
        'czasopisma.uni.opole.pl\/index.php', 'czasopisma.isppan.waw.pl\/index.php', 'czasopisma.kul.pl', 'journals.iaepan.pl',
        'journals.us.edu.pl\/index.php', 'czasopisma.ltn.lodz.pl\/index.php',
    ];

    private $unique_domain_rules = [
        [// google site doanh nghiệp cũ
            'match' => '/^https?\:\/\/sites\.google\.com\/a\//ui',
            'domain' => [
                '/(sites\.google\.com\/a\/[\w\-\.]+\/[\w\-\.]+)/ui',
            ],
            'name' => [
                '/(sites\.google\.com\/a\/[\w\-\.]+\/[\w\-\.]+)/ui',
            ]
        ],
        [// google site cá nhân cũ
            'match' => '/^https?\:\/\/sites\.google\.com\/site\//ui',
            'domain' => [
                '/(sites\.google\.com\/site\/[\w\-\.]+)/ui',
            ],
            'name' => [
                '/(sites\.google\.com\/site\/[\w\-\.]+)/ui',
            ]
        ],
        [// google site cá nhân mới
            'match' => '/^https?\:\/\/sites\.google\.com\/view\//ui',
            'domain' => [
                '/(sites\.google\.com\/view\/[\w\-\.]+)/ui',
            ],
            'name' => [
                '/(sites\.google\.com\/view\/[\w\-\.]+)/ui',
            ]
        ],
        [// google site doanh nghiệp mới(nhất định phải có dấu chấm)
            'match' => '/^https?\:\/\/sites\.google\.com\/[\w\-\.]+\.[\w\-\.]+\//ui',
            'domain' => [
                '/(sites\.google\.com\/[\w\-\.]+\.[\w\-\.]+\/[\w\-\.]+)/ui',
            ],
            'name' => [
                '/(sites\.google\.com\/[\w\-\.]+\.[\w\-\.]+\/[\w\-\.]+)/ui',
            ]
        ],
        [ // Các site có dạng domain.com/~site_name
            //http://www.fem.unicamp.br/~em672/
            //http://www.joinville.ifsc.edu.br/~janderson.duarte/
            //http://professor.ufabc.edu.br/~pieter.westera/
            //http://www.inf.ufsc.br/~fernando.gauthier/EGC6006/cronograma.html
            //http://www.sbfisica.org.br/~ebm/ix/arquivos
            //http://www.foz.unioeste.br/~lamat/
            //http://www.df.ufcg.edu.br/~adriano/
            //http://fma.if.usp.br/~mlima/
            //http://www.if.ufrj.br/~coelho/
            //http://www.cin.ufpe.br/~pasg/if678/
            'match' => '/https?\:\/\/([\w\-\.]+\.)+[\w\-\.]+\/\~[\w\-\.]+\/?/ui',
            'domain' => [
                '/https?\:\/\/(www\d+\.)?(([\w\-\_]+\.)+[\w\-\_]+\/\~[\w\-\_\.]+)\/?/ui',
                2
            ],
            'name' => [
                '/https?\:\/\/(www\d+\.)?(([\w\-\_]+\.)+[\w\-\_]+\/\~[\w\-\_\.]+)\/?/ui',
                2
            ]
        ],
        ///////////////////////////////////////////////////////////////////////////////////////////
        [ //http://dequi.eel.usp.br/domingos/qg301index.html
            'match' => '/https?\:\/\/dequi\.eel\.usp\.br\/[\w\-\.]+\/?/ui',
            'domain' => [
                '/(dequi\.eel\.usp\.br\/[\w\-\.]+)/ui',
            ],
            'name' => [
                '/(dequi\.eel\.usp\.br\/[\w\-\.]+)/ui',
            ]
        ],
        [ //http://www.if.ufrgs.br/public/tapf/
            'match' => '/https?\:\/\/www\d+\.if\.ufrgs\.br\/public\/[\w\-\.]+\/?/ui',
            'domain' => [
                '/(www\d+\.if\.ufrgs\.br\/public\/[\w\-\.]+)/ui',
            ],
            'name' => [
                '/(www\d+\.if\.ufrgs\.br\/public\/[\w\-\.]+)/ui',
            ]
        ],
    ];

    private $matched_rule = null;
    private $init = null;

    private function __construct($string)
    {
        $this->init = $string;

        $this->unique_domain_rules[] = [
            'match' => '/\/\/(' . implode('|', $this->domains_multi_rules) . ')\//ui',
            'domain' => [
                '/(' . implode('|', $this->domains_multi_rules) . ')\/[\w\-\.]+/ui',
            ],
            'name' => [
                '/(' . implode('|', $this->domains_multi_rules) . ')\/[\w\-\.]+/ui',
            ]
        ];

        preg_match_all('/^(([^:\/?#]+):)?(\/\/([^\/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?$/', $string, $m);
        $this->scheme = $m[2][0];
        $this->authority = $m[4][0];
        /**
         * CHANGE:
         * @author Dominik Habichtsberg <Dominik.Habichtsberg@Hbg-IT.de>
         * @since  24 Mai 2015 10:02 Uhr
         *
         * Former code:  $this->path = ( empty( $m[ 5 ][ 0 ] ) ) ? '/' : $m[ 5 ][ 0 ];
         * No tests failed, when the path is empty.
         * With the former code, the relative urls //g and #s failed
         */
        $this->path = $m[5][0];
        $this->query = $m[7][0];
        $this->fragment = $m[9][0];
    }

    private function to_str()
    {
        $ret = '';
        if (!empty($this->scheme)) {
            $ret .= "{$this->scheme}:";
        }

        if (!empty($this->authority)) {
            $ret .= "//{$this->authority}";
        }

        $ret .= $this->normalize_path($this->path);

        if (!empty($this->query)) {
            $ret .= "?{$this->query}";
        }

        if (!empty($this->fragment)) {
            $ret .= "#{$this->fragment}";
        }

        return $ret;
    }

    private function normalize_path($path)
    {
        if (empty($path)) {
            return '';
        }

        $normalized_path = $path;
        $normalized_path = preg_replace('`//+`', '/', $normalized_path, -1, $c0);
        $normalized_path = preg_replace('`^/\\.\\.?/`', '/', $normalized_path, -1, $c1);
        $normalized_path = preg_replace('`/\\.(/|$)`', '/', $normalized_path, -1, $c2);

        /**
         * CHANGE:
         * @author Dominik Habichtsberg <Dominik.Habichtsberg@Hbg-IT.de>
         * @since  24 Mai 2015 10:05 Uhr
         * changed limit form -1 to 1, because climbing up the directory-tree failed
         */
        $normalized_path = preg_replace('`/[^/]*?/\\.\\.(/|$)`', '/', $normalized_path, 1, $c3);
        $num_matches = $c0 + $c1 + $c2 + $c3;

        return ($num_matches > 0) ? $this->normalize_path($normalized_path) : $normalized_path;
    }

    /**
     * Parse an url string
     *
     * @param string $url the url to parse
     *
     * @return phpUri
     */
    public static function parse($url)
    {
        $uri = new phpUri($url);

        /**
         * CHANGE:
         * @author Dominik Habichtsberg <Dominik.Habichtsberg@Hbg-IT.de>
         * @since  24 Mai 2015 10:25 Uhr
         * The base-url should always have a path
         */
        if (empty($uri->path)) {
            $uri->path = '/';
        }

        return $uri;
    }

    public function getHomeUrl()
    {
        return $this->scheme . "://" . $this->authority;
    }

    /**
     * Join with a relative url
     *
     * @param string $relative the relative url to join
     *
     * @return string
     */
    public function join($relative)
    {
        $uri = new phpUri($relative);
        switch (TRUE) {
            case !empty($uri->scheme):
            case !empty($uri->authority):
                break;

            case empty($uri->path):
                $uri->path = $this->path;
                if (empty($uri->query)) {
                    $uri->query = $this->query;
                }
                break;

            case strpos($uri->path, '/') === 0:
                break;

            default:
                $base_path = $this->path;
                if (strpos($base_path, '/') === FALSE) {
                    $base_path = '';
                } else {
                    $base_path = preg_replace('/\/[^\/]+$/', '/', $base_path);
                }
                if (empty($base_path) && empty($this->authority)) {
                    $base_path = '/';
                }
                $uri->path = $base_path . $uri->path;
        }

        if (empty($uri->scheme)) {
            $uri->scheme = $this->scheme;
            if (empty($uri->authority)) {
                $uri->authority = $this->authority;
            }
        }

        return $uri->to_str();
    }

    public function getUniDomain()
    {

        $domain = $this->getByRules();
        if (!$domain) {
            $domain = $this->authority;
        }
        return preg_replace("/^www\d*\./", "", $domain);
    }

    public static function urlEncode($link)
    {
        $link = str_replace("\n", "", $link);
        $link = preg_replace("/\s\s+/", "", $link);
        $is_encoded = preg_match('~%[0-9A-F]{2}~i', $link);
        if ($is_encoded) {
            $link = str_replace(" ", '%20', $link);
            return $link;
        }
        $matches = [];
        $is_match = preg_match('/^https?\:\/\/([\w\-]+\.)+[\w\-]+(\:\d+)?\/?/', $link, $matches);
        if ($is_match) {
            $base = $matches[0];
            $remain = str_replace($base, '', $link);
        } else {
            $base = "";
            $remain = $link;
        }
        $remain = str_replace("/", '__slash__', $remain);
        $remain = str_replace("?", '__question_mark__', $remain);
        $remain = str_replace("&", '__and_mark__', $remain);
        $remain = str_replace("=", '__equal_mark__', $remain);
        $remain = str_replace(",", '__comma_mark__', $remain);
        $remain = str_replace("#", '__sharp_mark__', $remain);
        $remain = str_replace("~", '__approximate__', $remain);
        $remain = str_replace(" ", '__space__', $remain);
        $remain = urlencode($remain);
        $remain = str_replace("__slash__", '/', $remain);
        $remain = str_replace("__question_mark__", '?', $remain);
        $remain = str_replace("__and_mark__", '&', $remain);
        $remain = str_replace("__equal_mark__", '=', $remain);
        $remain = str_replace("__comma_mark__", ',', $remain);
        $remain = str_replace("__sharp_mark__", '#', $remain);
        $remain = str_replace("__approximate__", '~', $remain);
        $remain = str_replace("__space__", '%20', $remain);
        return $base . $remain;
    }

    public static function googleSitesUniLink($url)
    {
        $matches = [];
        $is_match = preg_match('/^https?\:\/\/sites\.google\.com\/site\/[\w\-\.]+\/?/ui', $url, $matches);
        if ($is_match) {
            return rtrim($matches[0], "/");
        } elseif (preg_match('/^https?\:\/\/sites\.google\.com\/a\/[\w\-\.]+\/[\w\-\.]+\/?/ui', $url, $matches)) {
            return rtrim($matches[0], "/");
        } elseif (preg_match('/^https?\:\/\/sites\.google\.com\/view\/[\w\-\.]+\/?/ui', $url, $matches)) {
            ;
            if ($is_match) {
                return rtrim($matches[0], "/");
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function googleSiteName($url)
    {
        if (strpos($url, '/site/')) {
            $remain = preg_replace('/^https?\:\/\/sites\.google\.com\/(site\/[\w\-\.]+)\/?.*/ui', "$1", $url);
        } elseif (strpos($url, '/view/')) {
            $remain = preg_replace('/^https?\:\/\/sites\.google\.com\/(view\/[\w\-\.]+)\/?.*/ui', "$1", $url);
        } else {
            $remain = preg_replace('/^https?\:\/\/sites\.google\.com\/(a\/[\w\-\.]+\/[\w\-\.]+)\/?.*/ui', "$1", $url);
        }
        return $remain;
    }

    private function getByRules()
    {
        $rule = self::getMatchedRule($this->init, $this->unique_domain_rules);
        if ($rule !== false) {
            return self::getDomainByRules($this->init, $this->unique_domain_rules[$rule]);
        }
        return false;
    }

    /**
     * @param $url
     * @param array|PhpUri $rule
     *
     * @return bool|mixed
     */
    private static function getDomainByRules($url, $rule)
    {
        $domain_rule = $rule['domain'];
        $rule_length = count($domain_rule);
        if ($rule_length < 1) {
            return false;
        }
        $matches = [];
        if (preg_match($domain_rule[0], $url, $matches)) {
            if ($rule_length == 1) {
                return $matches[0];
            } else {
                return $matches[$domain_rule[1]];
            }
        } else {
            return false;
        }
    }

    private static function getMatchedRule($url, array $rules)
    {
        foreach ($rules as $k => $rule) {
            if (preg_match($rule['match'], $url)) {
                return $k;
            }
        }
        return false;
    }

    public function __toString()
    {
        return $this->to_str();
    }

}
