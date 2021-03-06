<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2017/6/1
 * Time: 11:07
 */

/**
 * 全局过滤函数
 * @param string     $str
 * @param array|null $filterFun 过滤函数,为null则使用全局过滤函数
 * @return mixed
 */
function filter($str, $filterFun)
{
    if (is_null($filterFun)) {
        $filterFun = isset(config('config')['submit_filter']) ? config('config')['submit_filter'] : [];
    }
    foreach ($filterFun as $fun) {
        $str = call_user_func($fun, $str);
    }

    return $str;
}

/**
 * post获取
 * @param string|null $key       键
 * @param array|null  $filterFun 过滤函数,为null则使用全局过滤函数
 * @return null|string|array
 */
function post($key = null, $filterFun = null)
{
    if ($key == null) {
        return $_POST;
    } elseif (isset($_POST[$key])) {
        return filter($_POST[$key], $filterFun);
    } else {
        return null;
    }
}


/**
 * get获取
 * @param string|null $key       键
 * @param array|null  $filterFun 过滤函数,为null则使用全局过滤函数
 * @return null|string|array
 */
function get($key = null, $filterFun = null)
{
    if ($key == null) {
        return $_GET;
    } elseif (isset($_GET[$key])) {
        return filter($_GET[$key], $filterFun);
    } else {
        return null;
    }
}

/**
 * post get 获取
 * @param string     $key       键
 * @param array|null $filterFun 过滤函数,为null则使用全局过滤函数
 * @return null|string|array
 */
function input($key, $filterFun = null)
{
    if (is_null($value = post($key, $filterFun))) {
        return get($key, $filterFun);
    } else {
        return $value;
    }
}

/**
 * 方便调试
 */
if (!function_exists('session')) {
    /**
     * session 获取
     * @param null $key
     * @param null $value
     * @return array|null|string
     */
    function session($key = null, $value = null)
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        if ($key == null) {
            return $_SESSION;
        } elseif ($value == null && isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } elseif ($key != null && $value != null) {
            $_SESSION[$key] = $value;
        }

        return null;
    }
}

/**
 * 读取配置文件
 * @param string $file
 * @return mixed
 */
function config($file)
{
    $config_var = 'CONFIG_VAR_' . $file;
    if (!isset($GLOBALS[$config_var])) {
        $file = APP_PATH . 'config/' . $file . '.php';

        global $$config_var;
        if (is_file($file)) {
            $$config_var = include $file;
        } else {
            $$config_var = [];
        }
    }

    return $GLOBALS[$config_var];
}

/**
 * 实例化model
 * @param string $table  表名
 * @param string $config 配置
 * @return \ulphp\Model
 */
function model($table, $config = 'mysql')
{
    return \ulphp\manage\ModelManage::model($table, $config);
}

/**
 * 当前时间
 * @param string $format
 * @return false|string
 */
function now($format = 'Y-m-d H:i:s')
{
    return date($format, time());
}

/**
 * http 请求
 * @param string $url     网址
 * @param array  $params  参数
 * @param string $method  提交方式
 * @param array  $header  头部
 * @param bool   $multi   是否传输文件
 * @param int    $timeout 超时时间s
 * @return mixed
 * @throws Exception
 */
function http($url, $params = [], $method = 'GET', $header = [], $multi = false, $timeout = 30)
{
    $opts = [
        CURLOPT_TIMEOUT        => $timeout,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_HTTPHEADER     => $header,
    ];
    if (strpos($url, 'https://') !== false) {
        $opts[CURLOPT_SSL_VERIFYPEER] = false;
        $opts[CURLOPT_SSL_VERIFYHOST] = false;
    }

    switch (strtoupper($method)) {
        case 'GET':
        case 'get':
            if (count($params)) {
                $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
            } else {
                $opts[CURLOPT_URL] = $url;
            }
            break;
        case 'POST':
        case 'post':
            //判断是否传输文件
            $params                   = $multi ? $params : http_build_query($params);
            $opts[CURLOPT_URL]        = $url;
            $opts[CURLOPT_POST]       = 1;
            $opts[CURLOPT_POSTFIELDS] = $params;
            break;
        default:
            throw new Exception('不支持的请求方式！');
    }

    $ch = curl_init();
    curl_setopt_array($ch, $opts);
    $data  = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    if ($error)
        throw new Exception('请求发生错误：' . $error);

    return $data;
}

/**
 * 重定向
 * @param string $url
 * @param int    $code
 */
function redirect($url, $code = 302)
{
    header("HTTP/1.1 $code Moved Permanently");
    header("Location:$url");
    exit();
}

/**
 * 判断是否https
 * @return bool
 */
function is_ssl()
{
    if (isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))) {
        return true;
    } elseif (isset($_SERVER['REQUEST_SCHEME']) && 'https' == $_SERVER['REQUEST_SCHEME']) {
        return true;
    } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
        return true;
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' == $_SERVER['HTTP_X_FORWARDED_PROTO']) {
        return true;
    }

    return false;
}

/**
 * 获取客户端IP地址,负载均衡请使用高级模式获取
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv  是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function ip($type = 0, $adv = false)
{
    $type = $type ? 1 : 0;
    static $ip = null;
    if (null !== $ip) {
        return $ip[$type];
    }

    if ($adv) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) {
                unset($arr[$pos]);
            }
            $ip = trim(current($arr));
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip   = $long ? [$ip, $long] : ['0.0.0.0', 0];

    return $ip[$type];
}

/**
 * json 中文不转义
 * @param mixed $value
 * @return string
 */
function json($value)
{
    return json_encode($value, JSON_UNESCAPED_UNICODE);
}


/**
 * 获取地址，不填为当前地址
 * @param null|string|bool $controller 控制器/函数名
 *                                     为空时返回当前链接并携带当前参数
 *                                     为false时仅返回当前链接
 * @param array            $params     参数
 * @param int              $paramMode  参数模式 0在已有参数上拼接，1覆盖已有参数
 * @return string
 */
function url($controller = null, $params = [], $paramMode = 0)
{
    $ssl     = is_ssl() ? 'https://' : 'http://';
    $baseUrl = $ssl . $_SERVER['HTTP_HOST'] . str_replace('/index.php', '/', $_SERVER['PHP_SELF']);

    // 仅返回当前链接
    if ($controller === false) {
        $controller = \ulphp\core\Controller::$controller;
        $method     = \ulphp\core\Controller::$method;
        $controller = controller_to_link($controller);

        $url = $baseUrl . $controller . '/' . $method . '.html';
    } // 当前链接并携带当前参数
    elseif (empty($controller)) {
        $controller = \ulphp\core\Controller::$controller;
        $method     = \ulphp\core\Controller::$method;
        $controller = controller_to_link($controller);
        if (!$paramMode) {
            foreach (get() as $key => $value) {
                if (!isset($params[$key])) {
                    $params[$key] = $value;
                }
            }
        }
        $paramsStr = '';
        foreach ($params as $key => $value) {
            $paramsStr .= ($paramsStr != '' ? '/' : '');
            $paramsStr .= $key . '/' . urlencode($value);
        }
        $url = $baseUrl . $controller . '/' . $method . ($paramsStr == '' ? '.html' : '/' . $paramsStr . '.html');
    } // 返回指定控制器链接
    else {
        $controllers = explode('/', $controller);
        $controller  = $controllers[0];
        $method      = $controllers[1];
        $controller  = controller_to_link($controller);
        $paramsStr   = '';
        foreach ($params as $key => $value) {
            $paramsStr = $key . '/' . urlencode($value) . ($paramsStr != '' ? '/' : '');
        }
        $url = $baseUrl . $controller . '/' . $method . ($paramsStr == '' ? '.html' : '/' . $paramsStr . '.html');
    }

    return $url;
}

/**
 * 控制器名转换成url可识别链接
 * @param $controller
 * @return mixed|string
 */
function controller_to_link($controller)
{
    $controller = lcfirst($controller);
    $_pattern   = '/([A-Z]+)/';
    if (preg_match($_pattern, $controller)) {
        $controller = preg_replace($_pattern, "_$1", $controller);
        $controller = strtolower($controller);
    }
    return $controller;
}

/**
 * 获取mysql连接
 * @param string $file 配置文件名，省略后缀
 * @return \ulphp\lib\db\mysql\Query
 */
function mysql_db($file = 'mysql')
{
    $config = config($file);

    return \ulphp\manage\DBManage::getMysql($config);
}

/**
 * 获取redis连接
 * @param string $file 配置文件名，省略后缀
 * @return \ulphp\lib\db\redis\Query
 */
function redis_db($file = 'redis')
{
    $config = config($file);

    return \ulphp\manage\DBManage::getRedis($config);
}

/**
 * 判断请求类型
 * @return bool
 */
function isGet()
{
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        return true;
    } else {
        return false;
    }
}

/**
 * 引入视图
 * @param $file string
 */
function load_view($file)
{
    $name = APP_PATH . 'view/' . $file;
    if (is_file($filename = $name . '.php')) {
        include $filename;
    } else {
        include $name . '.html';
    }
}

/**
 * 文件日志对象
 * @return \ulphp\lib\log\LogFile
 */
function log_file()
{
    return \ulphp\manage\LogManage::getLogFile();
}

/**
 * 文件缓存对象
 * @return \ulphp\lib\cache\CacheFile
 */
function cache_file()
{
    return \ulphp\manage\CacheManage::getCacheFile();
}

/**
 * 封装退出
 * 后置函数postposition存在则会调用并传入$result值，可在common/common.php中对其进行声明
 * @param string $result 记录日志
 */
function __exit($result = '')
{
    // 执行后置函数，可在自定义函数定义该函数
    if (function_exists('postposition')) {
        postposition($result);
    }
    ob_end_flush();
    exit();
}


if (!function_exists('validate')) {
    /**
     * 验证器，使用注解验证时统一进入的函数
     * @param string $type 数据类型，被验证值的数据类型
     *                     null
     *                     int
     * @param string $desc 注释
     * @return string
     */
    function validate($type, $desc)
    {
        switch ($type) {
            case 'null':
                return json(['state' => false, 'msg' => $desc . '不能为空']);
                break;
            case 'int':
                return json(['state' => false, 'msg' => $desc . '必须为数字']);
                break;
        }
    }
}