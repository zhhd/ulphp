<?php
/**
 * 数据库管理
 * Created by PhpStorm.
 * User: zhd
 * Date: 2017/6/2
 * Time: 14:07
 */

namespace ulphp\manage;


use ulphp\lib\db\mysql\Query as qMysql;
use ulphp\lib\db\redis\Query as qRedis;

class DBManage
{
    private static $mysql = [];
    private static $redis = [];

    /**
     * 获取mysql连接
     * @param $config
     * @return qMysql
     */
    public static function getMysql($config)
    {
        $host     = $config['hostname'];
        $db_name  = $config['database'];
        $user     = $config['username'];
        $password = $config['password'];
        $port     = $config['hostport'];
        $charset  = $config['charset'];

        if (!isset(static::$mysql[$host . $db_name])) {
            static::$mysql[$host . $db_name] = new qMysql($host, $port, $user, $password, $db_name, $charset);
        }

        return static::$mysql[$host . $db_name];
    }

    /**
     * 获取redis连接
     * @param $config
     * @return qRedis
     */
    public static function getRedis($config)
    {
        $host     = $config['host'];
        $password = $config['password'];
        $port     = $config['port'];
        $timeout  = $config['timeout'];

        if (!isset(static::$redis[$host])) {
            static::$redis[$host] = new qRedis($host, $password, $port, $timeout);
        }

        return static::$redis[$host];
    }
}