<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2017/9/5
 * Time: 9:59
 */

namespace ulphp\lib\debug;


use ulphp\lib\log\LogFile;

class Debug
{
    private static $logFile;
    private static $time;
    private static $use_time;

    /**
     * LogFile
     * @return LogFile
     */
    public static function getLogFile()
    {
        if (empty(static::$logFile)) {
            static::$logFile = new LogFile();
        }
        return static::$logFile;
    }

    public static function start()
    {
        static::$time = microtime(TRUE);
    }

    public static function end()
    {
        static::$use_time = (microtime(TRUE) - static::$time) * 1000;
        self::getLogFile()->set(url() . ' -- 使用时间：' . static::$use_time . 'ms');
    }
}