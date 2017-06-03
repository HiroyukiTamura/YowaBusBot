<?php
/**
 * Created by PhpStorm.
 * User: hiroyuki2
 * Date: 2017/05/20
 * Time: 17:27
 */
namespace src;
require_once "ScrapeStr.php";

class Scraper extends ScrapeStr {

    function scrape(){
        \Logger::logTT($this->className . __METHOD__);
        $this->html = $this->scrapeSingle("<!--// 番組情報一覧-->", "https://radiocloud.jp/login");
        $this->html = str_replace(array(" ", "\r", "\n"), '', $this->html);
        $ep_count = mb_substr_count($this->html, "プレイリストへいく");
        $prgList = [];
        for($i = 0; $i < $ep_count; $i++){
            $this->html = $this->scrapeSingle("<liclass=\"program_box\">", null);
            $imgNum = $this->scrapeSingle("program_icon_pc_", ".jpg");
            $address = $this->scrapeSingle("radiocloud.jp/archive/", "\"class=\"mainimg\">");
            $title = $this->scrapeSingle("<h4>", "</h4>");
            $update = $this->scrapeSingle("<pclass=\"day\">", "</p>");//なぜか改行される??
            $hp = $this->scrapeHP();
            $prgList[$i] = array($imgNum, $address, $title, $update, $hp);
        }

        $prgListJson = json_encode($prgList, JSON_UNESCAPED_UNICODE);
        if ($prgListJson === false){
            \Logger::logTT($this->className . json_last_error_msg());
            die();
        } else {
            return $prgListJson;
        }
    }

    private function scrapeHP(){
        $needleStart = "official\"><ahref=\"";
        $needleStart1 = "officialofficial_non\"><phref=\"";
        $hpBf = mb_strpos($this->html, $needleStart) + mb_strlen($needleStart);
        $hpBf1 = mb_strpos($this->html, $needleStart1) + mb_strlen($needleStart1);
        if ($hpBf < $hpBf1 || $hpBf1 == mb_strlen($needleStart1)){
            return $this->scrapeSingle($needleStart, "target=\"_blank\">番組公式ページ", 1);
        } else {
            return null;
        }
    }
}