<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2017/6/2
 * Time: 11:28
 */

namespace ulphp\lib\db\mysql;

use PDO;

interface QueryInterface
{
    /**
     * 打开连接
     */
    public function connect();

    /**
     * 关闭连接
     */
    public function closeConnection();

    /**
     * 查询单条数据
     * @param string       $query  sql语句
     * @param array | null $params 参数
     * @param int          $fetchMode
     * @return mixed
     */
    public function row($query, $params = NULL, $fetchMode = PDO::FETCH_ASSOC);

    /**
     * 查询多条数据
     * @param string       $query  sql语句
     * @param array | null $params 参数
     * @param int          $fetchMode
     * @return mixed
     */
    public function select($query, $params = NULL, $fetchMode = PDO::FETCH_ASSOC);

    /**
     * 更新数据
     * @param string       $query  sql语句
     * @param array | null $params 参数
     * @return int 影响条数
     */
    public function update($query, $params = NULL);

    /**
     * 删除数据
     * @param string       $query  sql语句
     * @param array | null $params 参数
     * @return int 影响条数
     */
    public function delete($query, $params = NULL);

    /**
     * 插入数据
     * @param string       $query  sql语句
     * @param array | null $params 参数
     * @return int 插入的id
     */
    public function insert($query, $params = NULL);

    /**
     * 返回最后一次插入的id
     * @return int
     */
    public function lastInsertId();

    /**
     * 开启事务
     */
    public function beginTrans();

    /**
     * 提交事务
     */
    public function commitTrans();

    /**
     * 回滚事务
     */
    public function rollBackTrans();
}