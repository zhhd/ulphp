<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2017/6/10
 * Time: 15:59
 */

namespace ulphp\manage;


use ulphp\lib\log\LogFile;

class LogManage
{
    private static $log_file;

    /**
     * @return LogFile
     */
    public static function getLogFile()
    {
        if (empty(static::$log_file)) {
            static::$log_file = new LogFile();
        }

        return static::$log_file;
    }
}