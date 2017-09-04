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
        ob_start();
        try {
            unset($_GET['s']);

            $param    = str_replace($this->projectName(), '', $_SERVER['REQUEST_URI']);
            $_pattern = '/\?[\s\S]+/';
            $param    = preg_replace($_pattern, '', $param);
            $params   = explode('/', $param);
            $params   = str_replace('.html', '', $params);

            $controller = empty(($controller = array_shift($params))) ? $this->ucFormat($this->default_controller) : $this->ucFormat($controller);
            $method     = empty(($method = array_shift($params))) ? $this->default_method : $method;

            while (count($params)) {
                $_GET[array_shift($params)] = count($params) ? urldecode(array_shift($params)) : '';
            }

            static::$controller = $controller;
            static::$method     = $method;

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
        catch (\Exception $e) {
            log_file()->set($e->getMessage());
            $result = json(['status' => FALSE, 'msg' => '服务器繁忙，请稍后重试~ NO:500']);
            echo $result;
        }

        // 执行后置函数，可在自定义函数定义该函数
        if (function_exists('postposition')) {
            postposition($result);
        }
        ob_end_flush();
    }

    public function projectName()
    {
        $name = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
        return $name;
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