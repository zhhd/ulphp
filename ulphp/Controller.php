<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2017/6/1
 * Time: 14:52
 */

namespace ulphp;

use \ulphp\core\Controller as CoreController;

class Controller
{
    /**
     * 模板渲染
     * @throws \Exception
     */
    protected function fetch()
    {
        $view      = strtolower('view' . '/' . CoreController::$controller . '/' . CoreController::$method);
        $view_new  = APP_PATH . $view;
        $view_new  = str_replace('/', DIRECTORY_SEPARATOR, $view_new);
        $view_new  = str_replace('\\', DIRECTORY_SEPARATOR, $view_new);
        $view_php  = $view_new . '.php';
        $view_html = $view_new . '.html';

        if (file_exists($view_php)) {
            include $view_php;
        } else if (file_exists($view_html)) {
            include $view_html;
        } else {
            throw new \Exception("模板不存在：" . $view);
        }
    }
}