<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/28
 * Time: 11:49
 */

namespace L\db;


final class Transaction
{
    private $connect;

    public function __construct(\PDO $connect)
    {
        $this->connect = $connect;
    }

    public function commit()
    {
        if ($this->connect) {
            $this->connect->commit();
            $this->connect = null;
        }
    }

    public function rollBack()
    {
        if ($this->connect) {
            $this->connect->rollBack();
            $this->connect = null;
        }
    }

    public function close()
    {
        $this->connect = null;
    }
}