<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2017/6/10
 * Time: 15:27
 */

namespace ulphp\lib\cache\base;


interface CacheInterface
{
    /**
     * 获取缓存
     * @param string $key     缓存键
     * @param bool   $default 缓存不存在返回值
     * @return string|bool    缓存值
     */
    public function get($key, $default = FALSE);

    /**
     * 设置缓存
     * @param string $key    缓存键
     * @param string $value  缓存值
     * @param int    $expire 缓存时间
     * @return bool
     */
    public function set($key, $value, $expire = 0);

    /**
     * 清理缓存
     * @param null|string $key 缓存键，为空清理所有
     * @return mixed
     */
    public function clear($key = NULL);
}