<?php
/**
 * Created by PhpStorm.
 * User: hiroyuki2
 * Date: 2017/05/21
 * Time: 18:59
 */

namespace src;
require_once "ScrapeStr.php";

class EpScraper extends ScrapeStr
{
    private $epInfo;
    private $lineMasks;
    private $isTempJsonExist;
    private $tempJsonName;
    private $oldEpList;

    function setEpInfo($epInfo){
        $this->epInfo = $epInfo;
        $this->lineMasks = ["\n", "\r"];
    }

    function scrape(){
        \Logger::logTT($this->className . __FUNCTION__);
        $start = microtime(true);
        $this->tempJsonName = "temp/" . "episode" . $this->epInfo[0] . ".json";
        $this->isTempJsonExist = file_exists($this->tempJsonName);
        $needle = "<li class=\"contents_box\">";
        $needle2 = "<liclass=\"contents_box\">";

        if ($this->isTempJsonExist){
            \Logger::logTT("isTempJsonExist === true");
            $this->oldEpList = json_decode(file_get_contents($this->tempJsonName), true);
            if ($this->oldEpList === false){
                \Logger::logTT(json_last_error_msg());
                die(json_last_error_msg());
            }
            $oldFileUrl = $this->oldEpList["episodes"][count($this->oldEpList["episodes"])-1][4];
            $this->html = $this->scrapeSingle($oldFileUrl, null);
            \Logger::logTT($oldFileUrl);

        } else {
            //合致するjsonがないようなので、削除していきます
            $files = glob("temp/*.*");
            if (!empty($files))
                array_map('unlink', $files);//'unlink'はファイル削除を行う関数。
        }

        $epCount = mb_substr_count($this->html, $needle);//この行と次の行の順番を入れ替えると、epCountの値が変わるので注意
        \Logger::logTT($epCount);
        $this->html = $this->scrapeSingle($needle, "https://radiocloud.jp/login");
        $this->html = str_replace(" ", "", $this->html);//改行は置換しない

        $epList = array("imgnum" => $this->epInfo[0], "imgurl" => $this->epInfo[1], "programtitle" => $this->epInfo[2], "last_update" => $this->epInfo[3], "hp" => $this->epInfo[4], "episodes" => array());

        for($i = 0; $i < $epCount; $i++) {
//            $date = $this->scrapeSingle("<dt>", "</dt>");//改行される??
//            $date = str_replace($this->lineMasks, "", $date);
            $date = $this->scrapeWithoutMasks("<dt>", "</dt>");
//            $title = $this->scrapeSingle('<span>', '</span>');
//            $title = str_replace($this->lineMasks, "", $title);
            $title = $this->scrapeWithoutMasks('<span>', '</span>');

            $des = $this->scrapeSingle('<div>', '</div>');//descriptionは改行コードを置換しない
//            $removeDate = $this->scrapeSingle("spanclass=\"end_date\">", "</span>");
//            $removeDate = substr_replace($removeDate, " ", -5, 0);//日付と時刻の間に空白を入れる
            $removeDate = $this->scrapeRemoveDate();

//            $fileUrl = $this->scrapeSingle("//www.spiral-pf.com/embed/", "\">\n<inputname=\"content_id\"", 0, true);
            $fileUrl = $this->scrapeWithoutMasks("//www.spiral-pf.com/embed/", "\">\n<inputname=\"content_id\"", 0, true);

//            $contentId = $this->scrapeSingle("\"content_id\"type=\"hidden\"value=\"", "\">\n\n<aclass=\"playbutton\"");
            $contentId = $this->scrapeWithoutMasks("\"content_id\"type=\"hidden\"value=\"", "\">\n\n<aclass=\"playbutton\"");

            $arr = array($date, $title, $des, $removeDate, $fileUrl, $contentId);
            $epList["episodes"][] = $arr;
//            $encodeTest = json_encode($arr, JSON_UNESCAPED_UNICODE);
//
//            if (!$encodeTest){
//                for($n =0; $n<count($arr); $n++){
//                    \Logger::logTT($arr[$n]);
//                }
//                die();
//            }
            if (mb_strpos($this->html, $needle2) !== false) {
                $this->html = $this->scrapeSingle($needle2, null);
                $end = microtime(true);

                if ($end - $start > 30){
                    $this->onLimit($epList);//MUST DIE
                    \Logger::logTT("over 30sec: " . $end - $start);
                    die("over 30sec: " . $end - $start);
                }
            } else {
                \Logger::logTT("im only echoed once");
            }
        }

        if ($this->isTempJsonExist){
            $this->oldEpList["episodes"] = array_merge($this->oldEpList["episodes"], $epList["episodes"]);
            $epList = $this->oldEpList;
        }

        $epListJson = json_encode($epList, JSON_UNESCAPED_UNICODE);
        if (!$epListJson){
            \Logger::logTT(json_last_error_msg());
            die();
        }

        return $epListJson;
    }

    function scrapeRemoveDate()
    {
        $string = $this->scrapeSingle("spanclass=\"end_date\">", "<inputname=", strlen("</span>") + 1);
        return substr_replace($string, " ", -5, 0);//日付と時刻の間に空白を入れる
    }

    function scrapeWithoutMasks($startNeedle, $endNeedle, $offset = 0, $includeNeedle = false){
        $string = $this->scrapeSingle($startNeedle, $endNeedle, $offset, $includeNeedle);
        return str_replace($this->lineMasks, "", $string);
    }

    private function onLimit($epList){
        if ($this->isTempJsonExist){
            //マージするんじゃ！
            $this->oldEpList["episodes"] = array_merge($this->oldEpList["episodes"], $epList["episodes"]);

            $json = json_encode($this->oldEpList, JSON_UNESCAPED_UNICODE);
            if ($json === false){
                \Logger::logTT(json_last_error_msg());
                die(json_last_error_msg());
            }

        } else {

            $json = json_encode($epList, JSON_UNESCAPED_UNICODE);
            if ($json === false) {
                \Logger::logTT(json_last_error_msg());
                die(json_last_error_msg());
            }
        }

        if (file_put_contents($this->tempJsonName, $json) === false){
            \Logger::logTT('file_put_contents($this->tempJsonName, $json) === false');
            die('file_put_contents($this->tempJsonName, $json) === false');
        }
    }
}