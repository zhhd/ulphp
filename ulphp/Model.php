<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2017/6/1
 * Time: 14:53
 */

namespace ulphp;

use ulphp\extend\mysql\Query;

class Model extends Query
{
    /**
     * 静态调用表名
     * @var
     */
    private static $m_table;

    /**
     * 查询类
     * @var array
     */
    private static $query = [];

    function __construct()
    {
        $this->initTable();
    }

    /**
     * 智能获取表名
     */
    protected function initTable()
    {
        if (empty($this->table)) {
            // 当前类名
            $class = get_class($this);

            // 当前模型名
            $name = str_replace('\\', '/', $class);
            $name = basename($name);

            // 当前表名
            $this->table = lcfirst($name);
            $_pattern    = '/([A-Z]+)/';
            if (preg_match($_pattern, $this->table)) {
                $this->table = preg_replace($_pattern, "_$1", $this->table);
                $this->table = strtolower($this->table);
            }

            if (empty($this->byname)) {
                $this->table = '`' . $this->table . '`';
            } else {
                $this->table = '`' . $this->table . '` as ' . $this->byname;
            }

        }
    }

    /**
     * 智能获取表名
     * @return mixed
     */
    public static function getMTable()
    {
        // 当前类名
        $class = get_called_class();

        // 当前模型名
        $name = str_replace('\\', '/', $class);
        $name = basename($name);

        // 当前表名
        self::$m_table = lcfirst($name);
        $_pattern      = '/([A-Z]+)/';
        if (preg_match($_pattern, self::$m_table)) {
            self::$m_table = preg_replace($_pattern, "_$1", self::$m_table);
            self::$m_table = strtolower(self::$m_table);
        }

        self::$m_table = '`' . self::$m_table . '`';

        return self::$m_table;
    }


    /**
     * 获取Query
     * @return Query
     */
    public static function getQuery()
    {
        $class = get_called_class();
        if (!isset(self::$query[$class])) {
            self::$query[$class]        = new Query();
            self::$query[$class]->table = self::getMTable();
        }

        return self::$query[$class];
    }

    /**
     * 单条查询
     * @param array             $data  条件
     * @param null|array|string $filed 列
     * @return array|bool
     */
    public static function find(array $data = [], $filed = NULL)
    {
        return self::getQuery()->row($data, $filed);
    }

    /**
     * 多条查询
     * @param array             $data  条件
     * @param null|array|string $filed 列
     * @return array|bool
     */
    public static function all(array $data = [], $filed = NULL)
    {
        return self::getQuery()->select($data, $filed);
    }

    /**
     * 单条新增
     * @param array $data 数据，一维数组
     * @return int
     */
    public static function create(array $data)
    {
        return self::getQuery()->insert($data);
    }

    /**
     * 多条新增
     * @param array $data 数据，二维数组
     * @return int
     */
    public static function createMore(array $data)
    {
        return self::getQuery()->insertMore($data);
    }

    /**
     * 删除数据
     * @param array $data
     * @return int
     */
    public static function destroy(array $data)
    {
        return self::getQuery()->delete($data);
    }

    /**
     * 更新数据（实在不知道取什么名字了）
     * @param $data
     * @param $where
     * @return int
     */
    public static function save(array $data, $where)
    {
        return self::getQuery()->update($data, $where);
    }
}