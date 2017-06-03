<?php

/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2017/6/1
 * Time: 14:13
 */
namespace controller;

use model\UserWeichat;
use ulphp\Controller;

class Index extends Controller
{
    public function index()
    {
//        $user   = new UserWeichat();
//        $result = $user->select();
//
//        var_dump($user->last_sql);
//        var_dump($result);
//        var_dump(url('index/index',['a'=>'//']));

        $this->fetch();
    }
}