<?php
/**
 * Created by PhpStorm.
 * User: hiroyuki2
 * Date: 2017/05/20
 * Time: 16:54
 */
namespace \Logger::class;

define("PATH", "log.log");
class Logger{
    public static function logTT($strings, $append = FILE_APPEND){
        file_put_contents(PATH, $strings . "\n", $append);
    }
}
