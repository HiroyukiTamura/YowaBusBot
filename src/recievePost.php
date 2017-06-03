<?php
/**
 * Created by PhpStorm.
 * User: hiroyuki2
 * Date: 2017/05/19
 * Time: 22:32
 * エントリポイントおじさん。
 */

require_once "Logger.php";
require_once "HtmlRetriever.php";
require_once "Scraper.php";
require_once "EpScraper.php";
require_once "FTP.php";

define('pw', 'a0120777777');
define("CURSOR_LOG_PATH", "cursorLog.log");
define("TEMPFILE_PATH", "tempfile.json");
define("EP_DIR", "episodes/");

set_time_limit(0);

$json = json_decode(file_get_contents( 'php://input'), true);

if ($json == false){
    Logger::logTT("failed to json decode:" . json_last_error_msg(), false);
    die();
}

if ($json['pw'] !== pw){
    Logger::logTT("pw false:" . $json['pw'], false);
    die();
}

Logger::logTT($json['param'], false);

switch ($json['param']){
    case 'FIRE':
        $cursorJson = json_decode(file_get_contents(CURSOR_LOG_PATH), true);

        if(empty($cursorJson)){
            Logger::logTT("cursor空");
            $prgJson = operatePrg(RECENT);
            file_put_contents(CURSOR_LOG_PATH, $prgJson);//これをもとにEPListを作成してゆく。
            file_put_contents(FILENAME_RECENT, $prgJson);
            file_put_contents(FILENAME_PRGLIST, $prgJson);

            sleep(10);

            $prgJson = operatePrg(SCHEDULE);
            file_put_contents(FILENAME_SCHEDULE, $prgJson);

            $ftp = new \src\FTP();
            $ftp->connect(true);
            $ftp->login(true);
            $ftp->setAndTranslateFile(FILENAME_RECENT, HTML_ROOT_PRG . FILENAME_RECENT, true);
            $ftp->setAndTranslateFile(FILENAME_SCHEDULE, HTML_ROOT_PRG . FILENAME_SCHEDULE, true);
            $ftp->setAndTranslateFile(FILENAME_PRGLIST, HTML_ROOT_META . FILENAME_PRGLIST, true);
            $ftp->close();
        } else {
            Logger::logTT(implode($cursorJson[0]));
            $retriever = new \src\HtmlRetriever(ARCHIVE, $cursorJson[0][1]);
            $html = $retriever->get_web_page()["content"];
            $scraper = new \src\EpScraper($html);
            $scraper->setEpInfo($cursorJson[0]);
            $epJson = $scraper->scrape();
            $localFile = EP_DIR . "episode" . $cursorJson[0][0] . ".json";
            file_put_contents($localFile, $epJson);
            $ftp = new \src\FTP();
            $ftp->connect(true);
            $ftp->login(true);

            //アップロードする
            $ftp->setLocalFile($localFile);
            $ftp->setRemoteFile(HTML_ROOT . "episode" .$cursorJson[0][0] . ".json");
            $ftp->translateFile(true);
            $ftp->close();

            array_shift($cursorJson);
            file_put_contents(CURSOR_LOG_PATH, json_encode($cursorJson, JSON_UNESCAPED_UNICODE));
        }
        break;
}

function operatePrg($command1){
    $retriever = new \src\HtmlRetriever(ROOT, $command1);
    $html = $retriever->get_web_page()["content"];
    $scraper = new \src\Scraper($html);
    return $scraper->scrape();
}