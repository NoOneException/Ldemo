<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/28
 * Time: 16:17
 */

namespace L\exception;

use L\db\DBSql;

class DBException extends \Exception
{
    private $dbSql;

    public function __construct(string $message, DBSql $dbSql)
    {
        $this->dbSql = $dbSql;
        parent::__construct($message, 20000);
    }
}