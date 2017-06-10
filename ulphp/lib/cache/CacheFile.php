<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2017/6/10
 * Time: 15:34
 */

namespace ulphp\lib\cache;


use ulphp\lib\cache\base\CacheInterface;

class CacheFile implements CacheInterface
{
    /**
     * 缓存存放目录
     * @var string
     */
    public $path = APP_PATH . '~runtime/cache/';

    public function __construct()
    {
        if (!is_dir($this->path)) {
            mkdir($this->path, 7777, TRUE);
        }
    }

    /**
     * 获取缓存
     * @param string $key     缓存键
     * @param bool   $default 缓存不存在返回值
     * @return string|bool    缓存值
     */
    public function get($key, $default = FALSE)
    {
        $filename = $this->path . md5($key);
        if (is_file($filename)) {
            $file   = fopen($filename, 'r');
            $expire = fgets($file);

            if (0 != $expire && $_SERVER['REQUEST_TIME'] > filemtime($filename) + $expire) {
                //缓存过期删除缓存文件
                $this->unlink($filename);
            } else {
                $content = '';
                while (!feof($file)) {
                    $content .= fgets($file) . "\r\n";
                }
                $default = $content;
            }
            fclose($file);
        }

        return $default;
    }

    /**
     * 设置缓存
     * @param string $key    缓存键
     * @param string $value  缓存值
     * @param int    $expire 缓存时间
     * @return bool
     */
    public function set($key, $value, $expire = 0)
    {
        $filename = $this->path . md5($key);
        file_put_contents($filename, "$expire\r\n" . $value);
    }

    /**
     * 清理缓存
     * @param null|string $key 缓存键，为空清理所有
     * @return mixed
     */
    public function clear($key = NULL)
    {
        $dir = $this->path;
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== FALSE) {
                    if (is_file($dir . $file)) {
                        $this->unlink($dir . $file);
                    }
                }
                closedir($dh);
            }
        }
    }

    /**
     * 判断文件是否存在后，删除
     * @param $path
     * @return bool
     */
    private function unlink($path)
    {
        return is_file($path) && unlink($path);
    }
}