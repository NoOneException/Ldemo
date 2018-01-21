<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/30
 * Time: 18:34
 */

namespace L\db\model;


use L\db\DBModel;

final class Query extends ConditionModel
{

    protected function execute()
    {
        return DBModel::model()->setDbCondition($this)->query();
    }

    public function one()
    {
        $res = $this->limit(1)->exec();
        return $res[0];
    }
}