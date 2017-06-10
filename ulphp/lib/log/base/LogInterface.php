<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2017/6/10
 * Time: 15:50
 */

namespace ulphp\lib\log\base;


interface LogInterface
{
    /**
     * 写日志
     * @param $content string 日志内容
     */
    public function set($content);
}