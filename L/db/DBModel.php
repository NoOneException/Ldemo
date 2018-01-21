<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/29
 * Time: 16:11
 */

namespace L\db;

/**
 * Class DBModel
 *
 * @property  SQLBuilder sqlBuilder
 * @property  DBCondition dbCondition
 * @package L\db
 */
final class DBModel
{
    private $dbCondition;
    private $sqlBuilder;
    private $data;
    private $table;
    private static $_model;

    private function __construct()
    {
    }

    public static function model()
    {
        if (!isset(self::$_model)) {
            self::$_model = new self();
            self::$_model->dbCondition = new DBCondition();
            self::$_model->sqlBuilder = new SQLBuilder();
        }
        return self::$_model;
    }

    public function table($table, $needTablePrefix = true)
    {
        $this->table = $needTablePrefix ? \L::app()->db->tablePrefix . $table : $table;
        return $this;
    }

    public function insert()
    {
        $dbSql = $this->sqlBuilder->insertAll($this->data, $this->table);
        return \L::app()->db->getCommand()->exec($dbSql, DBCommand::RETURN_TYPE_LAST_ID);
    }

    public function delete()
    {
        $dbSql = $this->sqlBuilder->delete($this->dbCondition);
        return \L::app()->db->getCommand()->exec($dbSql, DBCommand::RETURN_TYPE_COUNT);
    }

    public function query()
    {
        $dbSql = $this->sqlBuilder->query($this->dbCondition);
        return \L::app()->db->getCommand()->query($dbSql);
    }

    public function update()
    {
        $dbSql = $this->sqlBuilder->update($this->data[0], $this->dbCondition);
        return \L::app()->db->getCommand()->exec($dbSql, DBCommand::RETURN_TYPE_COUNT);
    }

    public static function execSql(string $sql, array $params = [], int $returnType = DBCommand::RETURN_TYPE_BOOL)
    {
        return \L::app()->db->getCommand()->exec(new DBSql($sql, $params), $returnType);
    }

    public static function queryOneBySql(string $sql, array $params = []): array
    {
        $res = self::queryAllBySql($sql, $params);
        return $res[0];
    }

    public static function queryAllBySql(string $sql, array $params = []): array
    {
        return \L::app()->db->getCommand()->query(new DBSql($sql, $params));
    }

    /**
     * @param DBCondition $dbCondition
     * @return $this
     */
    public function setDbCondition(DBCondition $dbCondition)
    {
        $this->dbCondition = $dbCondition;
        return $this;
    }

    public function addData(array $data)
    {
        $this->data[] = $data;
        return $this;
    }

    public function data(array $data)
    {
        $this->data = $data;
        return $this;
    }

    public static function getAllSql(): array
    {
        return \L::app()->db->getAllSql();
    }

    public static function getLastSql(): DBSql
    {
        return \L::app()->db->getLastSql();
    }
}