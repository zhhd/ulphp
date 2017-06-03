<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2017/6/1
 * Time: 11:07
 */

/**
 * 全局过滤函数
 * @param $str
 * @return mixed
 */
function filter($str)
{
    $_filterFun = array("addslashes", "trim", "strip_tags");
    foreach ($_filterFun as $fun) {
        $str = call_user_func($fun, $str);
    }

    return $str;
}

/**
 * post获取
 * @param $key
 * @return null
 */
function post($key = NULL)
{
    if ($key == NULL) {
        return $_POST;
    } else if (isset($_POST[$key])) {
        return filter($_POST[$key]);
    } else {
        return NULL;
    }
}


/**
 * get获取
 * @param null $key
 * @return mixed|null
 */
function get($key = NULL)
{
    if ($key == NULL) {
        return $_GET;
    } else if (isset($_GET[$key])) {
        return filter($_GET[$key]);
    } else {
        return NULL;
    }
}

/**
 * post get 获取
 * @param $key
 * @return mixed|null
 */
function input($key)
{
    if (is_null($value = post($key))) {
        return get($key);
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
     * @return mixed
     */
    function session($key = NULL, $value = NULL)
    {
        if ($key == NULL) {
            return $_SESSION;
        } else if ($value == NULL && isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } else {
            $_SESSION[$key] = $value;
        }
    }
}

/**
 *
 * @param $file
 * @return mixed
 */
function config($file)
{
    $file = APP_PATH . '/config/' . $file . '.php';

    return include $file;
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
 * @param string $url
 * @param array  $params 参数
 * @param string $method 提交方式
 * @param array  $header 头部
 * @param bool   $multi  是否传输文件
 * @return mixed
 * @throws Exception
 */
function http($url, $params = [], $method = 'GET', $header = array(), $multi = FALSE)
{
    $opts = array(
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYPEER => FALSE,
        CURLOPT_SSL_VERIFYHOST => FALSE,
        CURLOPT_HTTPHEADER     => $header,
    );

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
    if ($error) throw new Exception('请求发生错误：' . $error);

    return $data;
}

/**
 * 重定向
 * @param     $url
 * @param int $code
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
        return TRUE;
    } elseif (isset($_SERVER['REQUEST_SCHEME']) && 'https' == $_SERVER['REQUEST_SCHEME']) {
        return TRUE;
    } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
        return TRUE;
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' == $_SERVER['HTTP_X_FORWARDED_PROTO']) {
        return TRUE;
    }

    return FALSE;
}

/**
 * 获取客户端IP地址,负载均衡请使用高级模式获取
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv  是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function ip($type = 0, $adv = FALSE)
{
    $type = $type ? 1 : 0;
    static $ip = NULL;
    if (NULL !== $ip) {
        return $ip[$type];
    }

    if ($adv) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (FALSE !== $pos) {
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
 * @param $value
 * @return string
 */
function json($value)
{
    return json_encode($value, JSON_UNESCAPED_UNICODE);
}

/**
 * 获取地址
 * @param null  $controller
 * @param array $params
 * @return string
 */
function url($controller = NULL, $params = [])
{
    if (empty($controller)) {
        $url = is_ssl() ? 'https://' : 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] .
            (empty($_SERVER['QUERY_STRING']) ? '' : '?' . $_SERVER['QUERY_STRING']);
    } else {
        $url = is_ssl() ? 'https://' : 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . $controller . '.html' .
            (count($params) ? '?' . http_build_query($params) : '');
    }

    $url = str_replace('/index.php?s=', '', $url);
    $url = str_replace('/index.php', '/', $url);
    $url = preg_replace('/&/', '?', $url, 1);

    return $url;
}

/**
 * 写日志
 * @param $log
 */
function write_log($log)
{
    $file = APP_PATH . 'log/' . now('Y-m-d') . '.txt';
    $file = str_replace('/', DIRECTORY_SEPARATOR, $file);
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $file);

    file_put_contents($file, now() . '      ' . $log . "\r\n", FILE_APPEND);

    if (DEBUG) {
        echo $log;
    }
}