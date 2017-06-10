<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2017/6/1
 * Time: 9:50
 */

/**
 * 时区设置
 */
ini_set('date.timezone', 'PRC');

/**
 * 错误提示
 */
ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);

/**
 * 常量设置
 */
define('APP_PATH', __DIR__ . '/../');

/**
 * 初始化 核心文件
 */
require __DIR__ . '/core/Autoload.php';
require __DIR__ . '/core/Exception.php';
require __DIR__ . '/core/Controller.php';
require __DIR__ . '/helper.php';

/**
 * 加载数据库帮助类
 */


$autoload = new \ulphp\core\Autoload();
$autoload->run();

$exception = new \ulphp\core\Exception();
$exception->run();

$controller = new \ulphp\core\Controller();
$controller->load();

