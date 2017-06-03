<?php
/**
 * 数据库管理
 * Created by PhpStorm.
 * User: zhd
 * Date: 2017/6/2
 * Time: 14:07
 */

namespace ulphp;


use ulphp\lib\db\mysql\Query;

class DBManage
{
    private static $mysql = [];

    /**
     * 获取mysql连接
     * @param $config
     * @return Query
     */
    public static function getMysql($config)
    {
        $host     = $config['hostname'];
        $db_name  = $config['database'];
        $user     = $config['username'];
        $password = $config['password'];
        $port     = $config['hostport'];
        $charset  = $config['charset'];

        if (!isset(self::$mysql[$host . $db_name])) {
            self::$mysql[$host . $db_name] = new Query($host, $port, $user, $password, $db_name, $charset);
        }

        return self::$mysql[$host . $db_name];
    }
}