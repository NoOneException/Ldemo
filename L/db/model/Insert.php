<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/30
 * Time: 18:33
 */

namespace L\db\model;


use L\db\DBModel;

final class Insert
{
    private $table = '';
    private $data;
    private static $_instance;

    private function __construct()
    {
    }

    public static function model(string $table = '', bool $needTablePrefix = true)
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance->table($table, $needTablePrefix);
    }

    public function table(string $table,bool $needTablePrefix = true)
    {
        $this->table = $needTablePrefix ? $this->getTablePrefix() . $table : $table;
        return $this;
    }

    public function getTablePrefix(): string
    {
        return \L::app()->db->tablePrefix;
    }

    public function init()
    {
        $this->table = '';
        $this->data = null;
    }

    public function addData(array $data)
    {
        $this->data[] = $data;
        return $this;
    }

    public function exec()
    {
        if ($this->table && $this->data) {
            $res = DBModel::model()->table($this->table)->data($this->data)->insert();
        } else {
            $res = false;
        }
        $this->init();
        return $res;
    }


}