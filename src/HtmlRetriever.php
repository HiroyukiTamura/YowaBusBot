<?php
/**
 * Created by PhpStorm.
 * User: hiroyuki2
 * Date: 2017/05/20
 * Time: 16:37
 */

namespace src;

define("ROOT_URL", "https://radiocloud.jp/");
define("RECENT_URL", "indexLPSD");
define("SCHEDULE_URL", "indexLBCD");
define("RECENT", 0);
define("SCHEDULE", 1);

define("ROOT", 0);
define("ARCHIVE", 1);

define("FILENAME_RECENT", "lbsd.json");
define("FILENAME_SCHEDULE", "lbcd.json");
define("FILENAME_PRGLIST", "programlist.json");

define("PRGLIST_LOG", 2);
define("PRGLIST_URL", "http://wppsc.html.xdomain.jp/radiocloud/programlist.json ");//--> 放送日順のデータ

class HtmlRetriever{
    private $url;
    private $className;

    function __construct($command0, $command1)
    {
        $this->className = get_class($this) . "::";
        \Logger::logTT($this->className . __METHOD__);
        switch ($command0){
            case ROOT:
                switch ($command1){
                    case RECENT:
                        $this->url = ROOT_URL . RECENT_URL;
                        break;
                    case SCHEDULE:
                        $this->url = ROOT_URL . SCHEDULE_URL;
                        break;
                }
                break;
            case ARCHIVE:
                $this->url = ROOT_URL . "archive/" . $command1;
                break;
        }
    }

    function get_web_page($cookiesIn = '', $data = false){
//        file_put_contents(PATH, get_class($this) . "::" . __FUNCTION__, FILE_APPEND);
        \Logger::logTT($this->className . __FUNCTION__);

        $options = array(
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => true,     //return headers in addition to content
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
            CURLINFO_HEADER_OUT    => true,
            CURLOPT_SSL_VERIFYPEER => false,     // Disabled SSL Cert checks
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_COOKIE         => $cookiesIn
        );

        $ch      = curl_init($this->url);
        if ($data !== false){
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_USERAGENT, 'ttttttttttttttttttt');
        curl_setopt_array( $ch, $options );
        $rough_content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );

        $header_content = substr($rough_content, 0, $header['header_size']);
        $body_content = trim(str_replace($header_content, '', $rough_content));
        $pattern = "#Set-Cookie:\\s+(?<cookie>[^=]+=[^;]+)#m";
        preg_match_all($pattern, $header_content, $matches);
        $cookiesOut = implode("; ", $matches['cookie']);

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['headers']  = $header_content;
        $header['content'] = $body_content;
        $header['cookies'] = $cookiesOut;
        return $header;
    }
}