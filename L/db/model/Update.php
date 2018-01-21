<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/30
 * Time: 18:34
 */

namespace L\db\model;


use L\db\DBModel;

final class Update extends ConditionModel
{

    private $data;

    protected function execute()
    {
        return DBModel::model()->data([$this->data])->setDbCondition($this)->update();
    }

    public function data(array $data)
    {
        $this->data = $data;
        return $this;
    }
}