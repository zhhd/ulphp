<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2017/6/1
 * Time: 10:04
 */

namespace ulphp\core;


class Controller
{
    private       $default_controller = 'index';
    private       $default_method     = 'index';
    public static $controller         = 'index';
    public static $method             = 'index';

    public function load()
    {
        $s = ltrim(get('s'), '/');
        $s = explode('/', $s);
        $s = array_filter($s);
        $s = str_replace('.html', '', $s);

        $controller = is_null(($controller = array_shift($s))) ? $this->ucFormat($this->default_controller) : $this->ucFormat($controller);
        $method     = is_null(($method = array_shift($s))) ? $this->default_method : $method;

        static::$controller = $controller;
        static::$method     = $method;

        unset($_GET['s']);

        $class = '\controller\\' . $controller;
        $obj   = new $class();

        $refClass  = new \ReflectionClass($class);
        $parameter = [];
        foreach ($refClass->getMethod($method)->getParameters() as $item) {
            $parameter[] = input($item->name);
        }

        $result = call_user_func_array([$obj, $method], $parameter);
        if (is_int($result) || is_string($result)) {
            echo $result;
        } else if (!is_null($result)) {
            echo json($result);
        }
    }

    public function ucFormat($controller)
    {
        $strs       = explode('_', $controller);
        $controller = '';
        foreach ($strs as $str) {
            $controller .= ucfirst($str);
        }

        return $controller;
    }
}