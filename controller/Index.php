<?php

/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2017/6/1
 * Time: 14:13
 */
namespace controller;

use ulphp\Controller;

class Index extends Controller
{
    public function index()
    {
        $this->fetch();
    }
}