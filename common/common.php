<?php
/**
 * 全局函数
 * Created by PhpStorm.
 * User: zhd
 * Date: 2017/6/12
 * Time: 9:54
 */

/**
 * 后置函数
 * 程序结束时调用的函数
 * @param string $result 输出值
 */
function postposition($result)
{
    // 处理的代码，如写日志
}

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

