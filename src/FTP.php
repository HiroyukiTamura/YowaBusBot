<?php
/**
 * Created by PhpStorm.
 * User: hiroyuki2
 * Date: 2017/05/21
 * Time: 16:33
 */

namespace src;

define("ftp_html_server", "sv2.html.xdomain.ne.jp");
define("ftp_php_server", "sv2.php.xdomain.ne.jp");
define("ftp_html_user", "wppsc.html.xdomain.jp");
define("ftp_php_user", "wppsc.php.xdomain.jp");
define("ftp_pass", "a0120777777");
define("HTML_ROOT_META", "radiocloud/");
define("HTML_ROOT", "radiocloud/episodelist/");
define("HTML_ROOT_PRG", "radiocloud3/pgrlist/");
//$remote_file = "/radiocloud/programlist.json";
//$to_download_file = "programlist_downloaded.json";

class FTP
{
    private $className;
    private $connId;
    private $remotePath;
    private $localPath;

    function __construct()
    {
        $this->className = get_class($this) . "::";
        \Logger::logTT($this->className . __FUNCTION__);
    }

    function setRemoteFile($remotePath){
        $this->remotePath = $remotePath;
    }

    function setLocalFile($localPath){
        $this->localPath = $localPath;
    }

    function connect($isHtml){
        if ($isHtml){
            $this->connId = ftp_connect(ftp_html_server);
        } else {
            $this->connId = ftp_connect(ftp_php_server);
        }

        if (!$this->connId){
            \Logger::logTT("connect error");
            die();
        }
    }

    function login($isHtml){
        if ($isHtml){
            $ftp_user = ftp_html_user;
        } else {
            $ftp_user = ftp_php_user;
        }

        if (@ftp_login($this->connId, $ftp_user, ftp_pass)) {
            \Logger::logTT(" <<< FTP LOGIN >>>");
        } else {
            \Logger::logTT("FTP Connect Error");
            ftp_close($this->connId);
            die();
        }

        $resPasv = ftp_pasv($this->connId, true);
        if (!$resPasv){
            \Logger::logTT("パッシブエラー");
            ftp_close($this->connId);
            die();
        }
    }

    function translateFile($send){
        if ($send){
            $ftpResult = ftp_put($this->connId, $this->remotePath, $this->localPath, FTP_BINARY, false);
        } else {
            $ftpResult = ftp_get($this->connId, $this->localPath, $this->remotePath, FTP_BINARY, false);
        }

        if (!$ftpResult){
            \Logger::logTT("translateFile Error; send: " . print_r($send));
            ftp_close($this->connId);
            die();
        }

        return $ftpResult;
    }

    function setAndTranslateFile($localFile, $remoteFile, $send){
        $this->setLocalFile($localFile);
        $this->setRemoteFile($remoteFile);
        $this->translateFile($send);
    }

    function close(){
        ftp_close($this->connId);
    }
}