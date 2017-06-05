<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2017/6/5
 * Time: 16:40
 */

namespace ulphp\lib\db\redis;


class Query extends \Redis
{
    function __construct($host, $password, $port = 6379, $timeout = 30)
    {
        parent::__construct();
        parent::connect($host);
        parent::auth($password);
    }
}