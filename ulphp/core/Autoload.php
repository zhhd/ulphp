<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2017/6/1
 * Time: 9:53
 */

namespace ulphp\core;

class Autoload
{
    public function run()
    {
        $this->autoload();
    }

    public function autoload()
    {
        spl_autoload_register([$this, 'package']);
    }

    public function package($class)
    {
        $file = APP_PATH . $class . '.php';
        $file = str_replace('/', DIRECTORY_SEPARATOR, $file);
        $file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
        if (file_exists($file)) {
            require $file;
        } else {
            throw new \Exception("$file not found.");
        }
    }
}