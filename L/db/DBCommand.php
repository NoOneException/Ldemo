<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/28
 * Time: 12:53
 */

namespace L\db;

use L\exception\DBException;
use PDO;
use PDOException;
use PDOStatement;

final class DBCommand
{
    const RETURN_TYPE_BOOL = 1;
    const RETURN_TYPE_COUNT = 2;
    const RETURN_TYPE_LAST_ID = 3;

    private $connect;

    public function __construct(PDO $connect)
    {
        $this->connect = $connect;
    }

    public function exec(DBSql $dbSql, int $returnType = self::RETURN_TYPE_BOOL)
    {
        try {
            $dbSql->setCurSqlLine(debug_backtrace());
            $pdo = $this->connect;
            $pdoStatement = $pdo->prepare($dbSql->getSql());
            $this->bindValues($pdoStatement, $dbSql->getParams());
            $s_time = microtime(true);
            $dbSql->setMemory((memory_get_usage() / 1024) . 'k')->setCurTime($s_time);
            $r = $pdoStatement->execute();
            $dbSql->setExecTime(number_format(microtime(true) - $s_time, 4));
            if ($returnType == self::RETURN_TYPE_COUNT) {
                $r = $pdoStatement->rowCount();
            } else if ($returnType == self::RETURN_TYPE_LAST_ID) {
                $r = $pdo->lastInsertId();
            }
            \L::app()->db->addSql($dbSql);
            return $r;
        } catch (PDOException $e) {
            throw new DBException($e->getMessage(), $dbSql);
        }
    }

    public function query(DBSql $dbSql): array
    {
        try {
            $dbSql->setCurSqlLine(debug_backtrace());
            $pdo = $this->connect;
            $pdoStatement = $pdo->prepare($dbSql->getSql());
            $this->bindValues($pdoStatement, $dbSql->getParams());
            $s_time = microtime(true);
            $dbSql->setMemory((memory_get_usage() / 1024) . 'k')->setCurTime($s_time);
            $pdoStatement->execute();
            $dbSql->setExecTime(number_format(microtime(true) - $s_time, 4));
            \L::app()->db->addSql($dbSql);
            $r = $pdoStatement->fetchAll();
            return $r;
        } catch (PDOException $e) {
            throw new DBException($e->getMessage(), $dbSql);
        }
    }

    private function bindValues(PDOStatement $statement, array $params)
    {
        if ($params) {
            foreach ($params as $key => $value) {
                $parameter = is_int($key) ? $key + 1 : $key;
                $dataType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $statement->bindValue($parameter, $value, $dataType);
            }
        }

    }
}