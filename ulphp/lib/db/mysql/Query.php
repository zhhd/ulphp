<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2017/6/2
 * Time: 10:33
 */

namespace ulphp\lib\db\mysql;

use PDO;

/**
 * 数据库底层操作
 * Class Query
 * @package ulphp\lib\db\mysql
 */
class Query implements QueryInterface
{
    /**
     * PDO实例
     * @var PDO
     */
    public $pdo;

    /**
     * 数据库配置
     * @var array
     */
    public $settings;


    function __construct($host, $port, $user, $password, $db_name, $charset = 'utf8')
    {
        $this->settings = array(
            'host'     => $host,
            'port'     => $port,
            'user'     => $user,
            'password' => $password,
            'dbname'   => $db_name,
            'charset'  => $charset,
        );
        $this->connect();
    }

    /**
     * 打开连接
     */
    public function connect()
    {
        $dsn       = 'mysql:dbname=' . $this->settings["dbname"] . ';host=' .
            $this->settings["host"] . ';port=' . $this->settings['port'];
        $this->pdo = new PDO($dsn, $this->settings["user"], $this->settings["password"],
            array(
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . (!empty($this->settings['charset']) ?
                        $this->settings['charset'] : 'utf8'),
            ));
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
    }

    /**
     * 关闭连接
     */
    public function closeConnection()
    {
        $this->pdo = NULL;
    }

    /**
     * 查询单条数据
     * @param string       $query  sql语句
     * @param array | null $params 参数
     * @param int          $fetchMode
     * @return array|bool 没有数据返回false
     */
    public function row($query, $params = NULL, $fetchMode = PDO::FETCH_ASSOC)
    {
        try {
            $sQuery = $this->pdo->prepare($query);
            $sQuery->execute($params);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage() . '.sql:' . $query);
        }

        return $sQuery->fetch($fetchMode);
    }

    /**
     * 查询多条数据
     * @param string       $query  sql语句
     * @param array | null $params 参数
     * @param int          $fetchMode
     * @return array|bool 没有数据返回false
     */
    public function select($query, $params = NULL, $fetchMode = PDO::FETCH_ASSOC)
    {
        try {
            $sQuery = $this->pdo->prepare($query);
            $sQuery->execute($params);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage() . '.sql:' . $query);
        }

        return $sQuery->fetchAll($fetchMode);
    }

    /**
     * 更新数据
     * @param string       $query  sql语句
     * @param array | null $params 参数
     * @return int 影响条数
     */
    public function update($query, $params = NULL)
    {
        try {
            $sQuery = $this->pdo->prepare($query);
            $sQuery->execute($params);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage() . '.sql:' . $query);
        }

        return $sQuery->rowCount();
    }

    /**
     * 删除数据
     * @param string       $query  sql语句
     * @param array | null $params 参数
     * @return int 影响条数
     */
    public function delete($query, $params = NULL)
    {
        try {
            $sQuery = $this->pdo->prepare($query);
            $sQuery->execute($params);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage() . '.sql:' . $query);
        }

        return $sQuery->rowCount();
    }

    /**
     * 插入数据
     * @param string       $query  sql语句
     * @param array | null $params 参数
     * @return int 插入的id
     */
    public function insert($query, $params = NULL)
    {
        try {
            $sQuery = $this->pdo->prepare($query);
            $sQuery->execute($params);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage() . '.sql:' . $query);
        }
        if ($sQuery->rowCount() > 0) {
            return $this->lastInsertId();
        } else {
            return NULL;
        }
    }

    /**
     * 返回最后一次插入的id
     * @return int
     */
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * 开启事务
     */
    public function beginTrans()
    {
        $this->pdo->beginTransaction();
    }

    /**
     * 提交事务
     */
    public function commitTrans()
    {
        $this->pdo->commit();
    }

    /**
     * 回滚事务
     */
    public function rollBack()
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }


}