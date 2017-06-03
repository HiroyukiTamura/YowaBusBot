<?php
/**
 * Created by PhpStorm.
 * User: hiroyuki2
 * Date: 2017/05/21
 * Time: 19:02
 */

namespace src;

abstract class ScrapeStr
{
    protected $html;
    protected $className;

    function __construct($html)
    {
        $this->className = get_class($this ) . "::";
        \Logger::logTT($this->className . __METHOD__);
        $this->html = $html;
    }

    protected function scrapeSingle($needleStart, $needleEnd, $offset = 0, $includeNeedle = false){
        if ($needleStart === null){
            $fileBf = 0;
        } else {
            $fileBf = mb_strpos($this->html, $needleStart);
            if (!$includeNeedle){
                $fileBf += mb_strlen($needleStart);
            }
        }
        if ($needleEnd === null){
            $len = null;
        } else {
            $len = mb_strpos($this->html, $needleEnd) - $fileBf - $offset;
        }
        return mb_substr($this->html, $fileBf, $len);
    }

    public abstract function Scrape();
}