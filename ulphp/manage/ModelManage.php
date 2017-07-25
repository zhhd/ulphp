<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2017/7/25
 * Time: 14:23
 */

namespace ulphp\manage;


use ulphp\Model;

class ModelManage
{
    private static $model = [];

    /**
     * model实例化
     * @param string $table
     * @param string $config
     * @return Model
     */
    public static function model($table, $config = 'mysql')
    {
        $key = $table . $config;
        if (!isset(static::$model[$key])) {
            $model               = new Model();
            $model->config       = $config;
            $model->table        = $table;
            static::$model[$key] = $model;
        }

        return static::$model[$key];
    }
}