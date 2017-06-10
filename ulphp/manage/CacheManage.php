<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2017/6/10
 * Time: 16:04
 */

namespace ulphp\manage;


use ulphp\lib\cache\CacheFile;

class CacheManage
{
    private static $cache_file;

    /**
     * @return CacheFile
     */
    public static function getCacheFile()
    {
        if (empty(self::$cache_file)) {
            self::$cache_file = new CacheFile();
        }

        return self::$cache_file;
    }
}