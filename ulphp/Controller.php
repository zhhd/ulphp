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
    private $assign  = [];
    private $_static = '';
    private $_css    = '';
    private $_js     = '';

    /**
     * 渲染变量
     * @param string $key
     * @param mixed  $value
     * @return array
     */
    protected function assign($key = NULL, $value = NULL)
    {
        $this->assign[$key] = $value;
    }

    /**
     * 视图渲染
     * @param null $view 视图
     * @throws \Exception
     */
    protected function fetch($view = NULL)
    {
        ob_start();
        if (empty($view)) {
            $view = strtolower(CoreController::$controller . '/' . CoreController::$method);
        }

        $view_new  = APP_PATH . 'view' . '/' . $view;
        $view_new  = str_replace('/', DIRECTORY_SEPARATOR, $view_new);
        $view_new  = str_replace('\\', DIRECTORY_SEPARATOR, $view_new);
        $view_php  = $view_new . '.php';
        $view_html = $view_new . '.html';

        define('__CSS__', $this->_css());
        define('__JS__', $this->_js());
        define('__STATIC__', $this->_static());

        /**
         * 变量渲染
         */
        extract($this->assign);

        /**
         * 引入视图
         */
        if (file_exists($view_php)) {
            include $view_php;
        } else if (file_exists($view_html)) {
            include $view_html;
        } else {
            throw new \Exception("视图不存在：" . $view);
        }
        ob_end_flush();
    }

    /**
     * 获取static 路径
     * @return string
     */
    private function _static()
    {
        if (empty($this->_static)) {
            $phpSelf       = dirname($_SERVER['PHP_SELF']);
            $this->_static = $phpSelf . '/' . 'static';
        }

        return $this->_static;
    }

    /**
     * 获取css 路径
     * @return string
     */
    private function _css()
    {
        if (empty($this->_css)) {
            $this->_css = $this->_static() . '/css';
        }

        return $this->_css;
    }

    /**
     * 获取js路径
     * @return string
     */
    private function _js()
    {
        if (empty($this->_js)) {
            $this->_js = $this->_static() . '/js';
        }

        return $this->_js;
    }
}