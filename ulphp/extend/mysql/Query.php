<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2017/6/18
 * Time: 10:39
 */

namespace ulphp\extend\mysql;

use ulphp\lib\db\mysql\Query as Lquery;

/**
 * 数据库操作类
 * Class Query 依赖 ulphp\lib\db\mysql\Query 底层执行sql类
 * @package ulphp\extend\mysql
 */
class Query
{
    /**
     * 配置文件名 config/mysql.php => mysql
     * @var string
     */
    protected $config;

    /**
     * 表名
     * @var string
     */
    protected $table = '';

    /**
     * 别名
     * @var string
     */
    protected $byname = '';

    /**
     * 列名
     * @var array
     */
    protected $filed = [];

    /**
     * order by
     * @var array
     */
    protected $order = [];

    /**
     * group by
     * @var array
     */
    protected $group = [];

    /**
     * having
     * @var array
     */
    protected $having = [];

    /**
     * where and
     * @var array
     */
    protected $where = [];

    /**
     * where or
     * @var array
     */
    protected $where_or = [];

    /**
     * join
     * @var array
     */
    protected $join = [];

    /**
     * left join
     * @var array
     */
    protected $left_join = [];

    /**
     * right join
     * @var array
     */
    protected $right_join = [];

    /**
     * page
     * @var array
     */
    protected $page = [];

    /**
     * 最后一次运行sql
     * @var string
     */
    public $last_sql = '';

    /**
     * 列分割
     * @param $array
     * @return string
     */
    private function filedSplit($array)
    {
        $filed = '';
        $keys  = ['.', ' count', ' sum', ' as ', ' avg', ' min', ' max', ' '];
        foreach ($array as $value) {
            if ($filed != '') {
                $filed .= ',';
            }

            $filed_key = FALSE;
            $value     = strtolower($value);
            foreach ($keys as $k) {
                if (strpos($value, $k)) {
                    $filed_key = TRUE;
                    break;
                }
            }

            if ($filed_key) {
                $filed .= $value;
            } else {
                $filed .= "`$value`";
            }
        }

        return $filed;
    }

    /**
     * 获取列
     * @param null|array $filed
     * @return null|string
     */
    private function getFiled($filed = NULL)
    {
        if (is_array($filed)) {
            $filed = $this->filedSplit($filed);
        } else if (empty($filed) && count($this->filed)) {
            $filed = $this->filedSplit($this->filed);
        } else if (empty($filed)) {
            $filed = ' * ';
        }

        return $filed;
    }

    /**
     * 重写where
     * @param $data
     * @return array
     */
    private function getWhere($data)
    {
        $where   = [];
        $whereOr = [];
        $param   = [];

        $i = 0;
        if (count($data)) {
            foreach ($data as $key => $value) {
                $i++;
                $_filed = str_replace('.', '_', $key) . $i;
                if (strpos($key, '.')) {
                    $where [] = " $key=:w_$_filed ";
                } else {
                    $where [] = " `$key`=:w_$_filed ";
                }

                $param[":w_$_filed"] = $value;
            }
        }

        /**
         * and 条件
         */
        if (count($this->where)) {
            foreach ($this->where as $value) {
                $filed     = $value[0];
                $condition = $value[1];
                $op        = $value[2];
                $i++;
                $_filed = str_replace('.', '_', $filed) . $i;
                if (strpos($filed, '.')) {
                    $where [] = " $filed $op :w_$_filed ";
                } else {
                    $where [] = " `$filed` $op :w_$_filed ";
                }
                $param[":w_$_filed"] = $condition;
            }
        }
        if (count($where)) {
            $where = implode(' and ', $where);
        } else {
            $where = '';
        }

        /**
         * or 条件
         */
        if (count($this->where_or)) {
            foreach ($this->where_or as $value) {
                $filed     = $value[0];
                $condition = $value[1];
                $op        = $value[2];

                $i++;
                $_filed = str_replace('.', '_', $filed) . $i;
                if (strpos($filed, '.')) {
                    $whereOr [] = " $filed $op :w_$_filed ";
                } else {
                    $whereOr [] = " `$filed` $op :w_$_filed ";
                }
                $param[":w_$_filed"] = $condition;
            }
        }
        if (count($whereOr)) {
            $whereOr = implode(' or ', $whereOr);
        } else {
            $whereOr = '';
        }


        /**
         * 两条件拼接
         */
        if (empty(trim($where))) {
            $where = $whereOr;
        } else if (!empty(trim($whereOr))) {
            $where = $where . ' or ' . $whereOr;
        }


        if (empty(trim($where))) {
            $param = NULL;
        } else {
            $where = " where $where";
        }


        return [$where, $param];
    }

    /**
     * 获取order by
     * @return string
     */
    private function getOrder()
    {
        $order = '';
        if (count($this->order)) {
            $order = $this->filedSplit($this->order);
            $order = "order by $order";
        }

        return $order;
    }

    /**
     * 获取 group by
     * @return string
     */
    private function getGroup()
    {
        $group = '';
        if (count($this->group)) {
            $group = $this->filedSplit($this->group);
            $group = "group by $group";
        }

        return $group;
    }

    /**
     * 获取 having
     * @return array
     */
    private function getHaving()
    {
        if (count($this->having)) {
            $having = [];
            $param  = [];

            $i = 0;
            foreach ($this->having as $value) {
                $filed     = $value[0];
                $condition = $value[1];
                $op        = $value[2];

                $i++;
                $_filed = str_replace('.', '_', $filed) . $i;
                if (strpos($filed, '.')) {
                    $having [] = " $filed $op :h_$_filed ";
                } else {
                    $having [] = " `$filed` $op :h_$_filed ";
                }
                $param[":h_$_filed"] = $condition;
            }

            $having = implode(' and ', $having);
            $having = ' having ' . $having;
        } else {
            $having = '';
            $param  = NULL;
        }

        return [$having, $param];
    }

    /**
     * 获取 page
     * @return string
     */
    private function getPage()
    {
        $page = '';
        if (isset($this->page['limit'])) {
            $page .= ' limit ' . $this->page['limit'];
        }
        if (isset($this->page['offset'])) {
            $page .= ' offset ' . $this->page['offset'];
        }

        return $page;
    }

    /**
     * 获取 join
     * @return string
     */
    private function getJoin()
    {
        $join = '';
        if (count($this->join)) {
            foreach ($this->join as $value) {
                $table = $value['table'];
                $on    = $value['on'];

                if (strpos($table, 'as') || strpos($table, ' ')) {
                    $join .= " join $table on $on ";
                } else {
                    $join .= " join `$table` on $on ";
                }
            }
        }

        return $join;
    }

    /**
     * left join
     * @return string
     */
    private function getLeftJoin()
    {
        $join = '';
        if (count($this->left_join)) {
            foreach ($this->left_join as $value) {
                $table = $value['table'];
                $on    = $value['on'];

                if (strpos($table, 'as') || strpos($table, ' ')) {
                    $join .= " left join $table on $on ";
                } else {
                    $join .= " left join `$table` on $on ";
                }
            }
        }

        return $join;
    }

    /**
     * right join
     * @return string
     */
    private function getRightJoin()
    {
        $join = '';
        if (count($this->right_join)) {
            foreach ($this->right_join as $value) {
                $table = $value['table'];
                $on    = $value['on'];

                if (strpos($table, 'as') || strpos($table, ' ')) {
                    $join .= " right join $table on $on ";
                } else {
                    $join .= " right join `$table` on $on ";
                }
            }
        }

        return $join;
    }

    /**
     * 获取数据操作
     * @return Lquery
     */
    public function getDb()
    {
        return mysql_db($this->config);
    }

    /**
     * where and
     * @param string $field     字段名
     * @param string $condition 值
     * @param string $op        表达式
     * @return $this
     */
    public function where($field, $condition, $op = '=')
    {
        $this->where[] = [$field, $condition, $op];

        return $this;
    }

    /**
     * where or
     * @param string $field     字段名
     * @param string $condition 值
     * @param string $op        表达式
     * @return $this
     */
    public function whereOr($field, $condition, $op = '=')
    {
        $this->where_or[] = [$field, $condition, $op];

        return $this;
    }

    /**
     * order by
     * @param array|string $order
     * @return $this
     */
    public function order($order)
    {
        if (is_array($order)) {
            $this->order = array_merge($this->order, $order);
        } else {
            $this->order[] = $order;
        }


        return $this;
    }

    /**
     * group by
     * @param array|string $group
     * @return $this
     */
    public function group($group)
    {
        if (is_array($group)) {
            $this->group = array_merge($this->group, $group);
        } else {
            $this->group[] = $group;
        }

        return $this;
    }

    /**
     * join
     * @param string $table
     * @param string $on
     * @return $this
     */
    public function join($table, $on)
    {
        $this->join[] = ['table' => $table, 'on' => $on];

        return $this;
    }

    /**
     * left join
     * @param string $table
     * @param string $on
     * @return $this
     */
    public function leftJoin($table, $on)
    {
        $this->left_join[] = ['table' => $table, 'on' => $on];

        return $this;
    }

    /**
     * right join
     * @param string $table
     * @param string $on
     * @return $this
     */
    public function rightJoin($table, $on)
    {
        $this->right_join[] = ['table' => $table, 'on' => $on];

        return $this;
    }

    /**
     * having
     * @param string $field     字段名
     * @param string $condition 值
     * @param string $op        表达式
     * @return $this
     */
    public function having($field, $condition, $op = '=')
    {
        $this->having[] = [$field, $condition, $op];

        return $this;
    }

    /**
     * offset
     * @param $offset
     * @return $this
     */
    public function offset($offset)
    {
        $this->page['offset'] = $offset;

        return $this;
    }

    /**
     * limit
     * @param $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->page['limit'] = $limit;

        return $this;
    }

    /**
     * 分页
     * @param int $page
     * @param int $limit
     * @return $this
     */
    public function page($page, $limit)
    {
        $this->page['offset'] = ($page - 1) * $limit;
        $this->page['limit']  = $limit;

        return $this;
    }

    public function clear()
    {
        $this->join       = [];
        $this->left_join  = [];
        $this->right_join = [];
        $this->where      = [];
        $this->where_or   = [];
        $this->group      = [];
        $this->order      = [];
        $this->having     = [];
        $this->page       = [];
    }

    /**
     * 获取单条数据
     * @param array  $data  条件
     * @param string $filed 列
     * @return array|bool 没有数据返回false
     */
    public function row(array $data = [], $filed = NULL)
    {
        /**
         * 列
         */
        $filed = $this->getFiled($filed);

        /**
         * join
         */
        $join = $this->getJoin();

        /**
         * left join
         */
        $leftJoin = $this->getLeftJoin();

        /**
         * right join
         */
        $rightJoin = $this->getRightJoin();

        /**
         * where
         */
        $where = $this->getWhere($data);
        $param = $where[1];
        $where = $where[0];

        /**
         * group by
         */
        $group = $this->getGroup();

        /**
         * order by
         */
        $order = $this->getOrder();

        /**
         * having
         */
        $having = $this->getHaving();
        if (!empty($having[0])) {
            $param = array_merge($param, $having[1]);
        }
        $having = $having[0];

        /**
         * 拼接sql
         */
        $query          = "select $filed from $this->table $join $leftJoin $rightJoin $where $group $having $order limit 1";
        $this->last_sql = $query;
        $this->clear();

        return $this->getDb()->row($query, $param);
    }

    /**
     * 获取全部数据
     * @param array      $data  条件
     * @param null|array $filed 列
     * @return array|bool 没有数据返回false
     */
    public function select(array $data = [], $filed = NULL)
    {
        /**
         * 列
         */
        $filed = $this->getFiled($filed);

        /**
         * where
         */
        $where = $this->getWhere($data);
        $param = $where[1];
        $where = $where[0];

        /**
         * join
         */
        $join = $this->getJoin();

        /**
         * left join
         */
        $leftJoin = $this->getLeftJoin();

        /**
         * right join
         */
        $rightJoin = $this->getRightJoin();

        /**
         * group by
         */
        $group = $this->getGroup();

        /**
         * order by
         */
        $order = $this->getOrder();

        /**
         * having
         */
        $having = $this->getHaving();
        if (!empty($having[0])) {
            $param = array_merge($param, $having[1]);
        }
        $having = $having[0];

        $page = $this->getPage();

        /**
         * 拼接sql
         */
        $query          = "select $filed from $this->table $join $leftJoin $rightJoin $where $group $having $order $page";
        $this->last_sql = $query;
        $this->clear();

        return $this->getDb()->select($query, $param);
    }

    /**
     * 更新数据
     * @param array $data  数据
     * @param array $where 条件
     * @return int
     * @throws \PDOException
     */
    public function update(array $data = [], array $where = [])
    {
        $set   = [];
        $param = [];
        if (count($data)) {
            foreach ($data as $key => $value) {
                $set[$key]        = "`$key`=:s_$key";
                $param[":s_$key"] = $value;
            }
            $set = implode(',', $set);
        } else {
            throw new \PDOException("update error");
        }

        $where    = $this->getWhere($where);
        $whereStr = $where[0];
        if (count($where[1])) {
            $param = array_merge($param, $where[1]);
        }

        $query          = "update $this->table set $set $whereStr";
        $this->last_sql = $query;
        $this->clear();

        return $this->getDb()->update($query, $param);
    }

    /**
     * 单条插入数据
     * @param array $data 数据
     * @return int 最后一次插入id
     */
    public function insert(array $data = [])
    {
        $filed  = [];
        $values = [];
        $param  = [];
        foreach ($data as $key => $value) {
            $filed[]          = "`$key`";
            $values[]         = ":f_$key";
            $param[":f_$key"] = $value;
        }
        $filed  = implode(',', $filed);
        $values = implode(',', $values);

        $query          = "insert into $this->table ($filed) values ($values)";
        $this->last_sql = $query;
        $this->clear();

        return $this->getDb()->insert($query, $param);
    }

    /**
     * 多条插入数据
     * @param array $data 数据
     * @return int
     */
    public function insertMore(array $data = [])
    {
        $filed  = [];
        $values = [];
        $param  = [];

        foreach ($data[0] as $key => $value) {
            $filed[] = "`$key`";
        }
        $i = 0;
        foreach ($data as $item) {
            $value_sub = [];
            foreach ($item as $key => $value) {
                $value_sub[]             = ":f_{$key}_{$i}";
                $param[":f_{$key}_{$i}"] = $value;
            }
            $values[] = '(' . implode(',', $value_sub) . ')';
            $i++;
        }
        $filed  = implode(',', $filed);
        $values = implode(',', $values);

        $query          = "insert into $this->table ($filed) values $values";
        $this->last_sql = $query;
        $this->clear();

        return $this->getDb()->insert($query, $param);
    }

    /**
     * 删除数据
     * @param array $where 条件
     * @return int 影响行数
     */
    public function delete(array $where = [])
    {
        $where    = $this->getWhere($where);
        $whereStr = $where[0];
        $param    = $where[1];

        $query          = "delete from $this->table $whereStr";
        $this->last_sql = $query;
        $this->clear();

        return $this->getDb()->delete($query, $param);
    }

    /**
     * count
     * @param string | array $filed 列
     * @param array          $data  数据
     * @return int
     */
    public function count($filed = '*', array $data = [])
    {
        /**
         * 列
         */
        $filed = $this->getFiled($filed);

        /**
         * where
         */
        $where = $this->getWhere($data);
        $param = $where[1];
        $where = $where[0];

        /**
         * having
         */
        $having = $this->getHaving();
        if (!empty($having[0])) {
            $param = array_merge($param, $having[1]);
        }
        $having = $having[0];

        /**
         * 拼接sql
         */
        $query          = "select count($filed) as count from $this->table $where $having";
        $this->last_sql = $query;
        $this->clear();

        return $this->getDb()->row($query, $param)['count'];
    }

    /**
     * sum
     * @param string|array $filed 列
     * @param array        $data  数据
     * @return int
     */
    public function sum($filed = 'id', array $data = [])
    {
        /**
         * 列
         */
        $filed = $this->getFiled($filed);

        /**
         * where
         */
        $where = $this->getWhere($data);
        $param = $where[1];
        $where = $where[0];

        /**
         * having
         */
        $having = $this->getHaving();
        if (!empty($having[0])) {
            $param = array_merge($param, $having[1]);
        }
        $having = $having[0];

        /**
         * 拼接sql
         */
        $query          = "select sum($filed) as sum from $this->table $where $having";
        $this->last_sql = $query;
        $this->clear();

        $sum = $this->getDb()->row($query, $param)['sum'];

        return empty($sum) ? 0 : $sum;
    }

    /**
     * avg
     * @param  string|array $filed 列
     * @param array         $data  数据
     * @return int
     */
    public function avg($filed = 'id', array $data = [])
    {
        /**
         * 列
         */
        $filed = $this->getFiled($filed);

        /**
         * where
         */
        $where = $this->getWhere($data);
        $param = $where[1];
        $where = $where[0];

        /**
         * having
         */
        $having = $this->getHaving();
        if (!empty($having[0])) {
            $param = array_merge($param, $having[1]);
        }
        $having = $having[0];

        /**
         * 拼接sql
         */
        $query          = "select avg($filed) as avg from $this->table $where $having";
        $this->last_sql = $query;
        $this->clear();

        $avg = $this->getDb()->row($query, $param)['avg'];

        return empty($avg) ? 0 : $avg;
    }

    /**
     * min
     * @param string | array $filed 列
     * @param array          $data  数据
     * @return int
     */
    public function min($filed = 'id', array $data = [])
    {
        /**
         * 列
         */
        $filed = $this->getFiled($filed);

        /**
         * where
         */
        $where = $this->getWhere($data);
        $param = $where[1];
        $where = $where[0];

        /**
         * having
         */
        $having = $this->getHaving();
        if (!empty($having[0])) {
            $param = array_merge($param, $having[1]);
        }
        $having = $having[0];

        /**
         * 拼接sql
         */
        $query          = "select min($filed) as min from $this->table $where $having";
        $this->last_sql = $query;
        $this->clear();

        $min = $this->getDb()->row($query, $param)['min'];

        return empty($min) ? 0 : $min;
    }

    /**
     * max
     * @param string | array $filed 列
     * @param array          $data  数据
     * @return int
     */
    public function max($filed = 'id', array $data = [])
    {
        /**
         * 列
         */
        $filed = $this->getFiled($filed);

        /**
         * where
         */
        $where = $this->getWhere($data);
        $param = $where[1];
        $where = $where[0];

        /**
         * having
         */
        $having = $this->getHaving();
        if (!empty($having[0])) {
            $param = array_merge($param, $having[1]);
        }
        $having = $having[0];

        /**
         * 拼接sql
         */
        $query          = "select max($filed) as max from $this->table $where $having";
        $this->last_sql = $query;
        $this->clear();

        $max = $this->getDb()->row($query, $param)['max'];

        return empty($max) ? 0 : $max;
    }

    /**
     * 配合operation()函数使用
     * @param string     $filed 列
     * @param string|int $value 值
     * @param string     $op    运算符
     * @return array
     */
    public function opValues($filed, $value, $op)
    {
        return [
            'filed' => $filed,
            'value' => $value,
            'op'    => $op,
        ];
    }

    /**
     * 字段做运算操作
     * @param array $fileds [opValues(),...]
     * @param array $data   条件
     * @return int |bool
     */
    public function operation($fileds, $data)
    {
        /*
         * 列
         */
        $filed = [];
        foreach ($fileds as $row) {
            $key     = $row['filed'];
            $value   = $row['value'];
            $op      = $row['op'];
            $filed[] = "`$key`= `$key` $op $value";
        }
        $filed = implode(',', $filed);

        /**
         * where
         */
        $where = $this->getWhere($data);
        $param = $where[1];
        $where = $where[0];

        /**
         * having
         */
        $having = $this->getHaving();
        if (!empty($having[0])) {
            $param = array_merge($param, $having[1]);
        }
        $having = $having[0];

        /**
         * 拼接sql
         */
        $query          = "update $this->table set $filed $where $having";
        $this->last_sql = $query;
        $this->clear();

        $last_id = $this->getDb()->update($query, $param);

        return $last_id;
    }
}