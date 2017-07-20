<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2017/6/10
 * Time: 15:49
 */

namespace ulphp\lib\log;


use ulphp\lib\log\base\LogInterface;

class LogFile implements LogInterface
{
    /**
     * 日志存放目录
     * @var string
     */
    public $path = APP_PATH . '~runtime/log/';

    public function __construct()
    {
        if (!is_dir($this->path)) {
            mkdir($this->path, 7777, TRUE);
        }
    }

    /**
     * 写日志
     * @param $content string 日志内容
     */
    public function set($content)
    {
        $path = $this->path . now('Y-m-d') . '.txt';

        $_content = "记录时间：" . now();
        $_content .= " 日志内容：$content\r\n";
        file_put_contents($path, $_content, FILE_APPEND);
    }
}