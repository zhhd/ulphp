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
     * 获取Query
     * @return Model
     */
    public static function getQuery()
    {
        $class = get_called_class();
        if (!isset(static::$query[$class])) {
            $self                = new static();
            $self->table         = static::getMTable();
            static::$query[$class] = $self;
        }

        return static::$query[$class];
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
    protected static function getMTable()
    {
        // 当前类名
        $class = get_called_class();

        // 当前模型名
        $name = str_replace('\\', '/', $class);
        $name = basename($name);

        // 当前表名
        static::$m_table = lcfirst($name);
        $_pattern      = '/([A-Z]+)/';
        if (preg_match($_pattern, static::$m_table)) {
            static::$m_table = preg_replace($_pattern, "_$1", static::$m_table);
            static::$m_table = strtolower(static::$m_table);
        }

        static::$m_table = '`' . static::$m_table . '`';

        return static::$m_table;
    }

    /**
     * 获取单条数据
     * @param array $data  条件
     * @param null  $filed 列
     * @param bool  $cache 缓存时间/秒，false表示不缓存
     * @return array|bool|mixed
     */
    public function row(array $data = [], $filed = NULL, $cache = FALSE)
    {
        $key = json_encode($data) . json_encode($filed) . $this->table . 'row';

        if ($cache === FALSE) {
            cache_file()->clear($key);

            return parent::row($data, $filed);
        } else {
            if (cache_file()->key_exists($key)) {
                return json_decode(cache_file()->get($key), TRUE);
            } else {
                $result = parent::row($data, $filed);
                cache_file()->set($key, json_encode($result), $cache);

                return $result;
            }
        }
    }

    /**
     * 获取多条数据
     * @param array $data  条件
     * @param null  $filed 列
     * @param bool  $cache 缓存时间/秒，false表示不缓存
     * @return array|bool|mixed
     */
    public function select(array $data = [], $filed = NULL, $cache = FALSE)
    {
        $key = json_encode($data) . json_encode($filed) . $this->table . 'select';

        if ($cache === FALSE) {
            cache_file()->clear($key);

            return parent::select($data, $filed);
        } else {
            if (cache_file()->key_exists($key)) {
                return json_decode(cache_file()->get($key), TRUE);
            } else {
                $result = parent::select($data, $filed);
                cache_file()->set($key, json_encode($result), $cache);

                return $result;
            }
        }
    }

    /**
     * 获取单条数据
     * @param array $data  条件
     * @param null  $filed 列
     * @param bool  $cache 缓存时间/秒，false表示不缓存
     * @return array|bool|mixed
     */
    public static function find(array $data = [], $filed = NULL, $cache = FALSE)
    {
        return static::getQuery()->row($data, $filed, $cache);
    }

    /**
     * 获取多条数据
     * @param array $data  条件
     * @param null  $filed 列
     * @param bool  $cache 缓存时间/秒，false表示不缓存
     * @return array|bool|mixed
     */
    public static function all(array $data = [], $filed = NULL, $cache = FALSE)
    {
        return static::getQuery()->select($data, $filed, $cache);
    }

    /**
     * 单条新增
     * @param array $data 数据，一维数组
     * @return int
     */
    public static function create(array $data)
    {
        return static::getQuery()->insert($data);
    }

    /**
     * 多条新增
     * @param array $data 数据，二维数组
     * @return int
     */
    public static function createMore(array $data)
    {
        return static::getQuery()->insertMore($data);
    }

    /**
     * 删除数据
     * @param array $data
     * @return int
     */
    public static function destroy(array $data)
    {
        return static::getQuery()->delete($data);
    }

    /**
     * 更新数据
     * @param $data
     * @param $where
     * @return int
     */
    public static function save(array $data, $where)
    {
        return static::getQuery()->update($data, $where);
    }
}