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
        if (empty(static::$cache_file)) {
            static::$cache_file = new CacheFile();
        }

        return static::$cache_file;
    }
}