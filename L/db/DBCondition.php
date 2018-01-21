<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/29
 * Time: 16:19
 */

namespace L\db;


use L\db\condition\ConditionHandler;

class DBCondition
{
    private $select = ['*'];
    private $table = '';
    private $tableAlias = 't';
    private $join = [];
    private $order = [];
    private $group = '';
    private $having = [];
    private $where = [];
    private $limit = 0;
    private $offset = 0;
    private $params = [];

    public function init()
    {
        $this->select = ['*'];
        $this->table = '';
        $this->tableAlias = 't';
        $this->join = [];
        $this->order = [];
        $this->group = '';
        $this->having = [];
        $this->where = [];
        $this->limit = 0;
        $this->offset = 0;
        $this->params = [];
    }

    public function addConditions(array $conditions)
    {
        foreach ($conditions as $condition) {
            if ($condition instanceof ConditionHandler) {
                $condition->onHandle($this, \L::app()->request);
            }
        }
        return $this;
    }

    public function addCondition(ConditionHandler $condition)
    {
        return $this->addConditions([$condition]);
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @param string $table
     * @param bool $needTablePrefix
     * @return $this
     */
    public function table(string $table, bool $needTablePrefix = true)
    {
        $this->table = $needTablePrefix ? $this->getTablePrefix() . $table : $table;
        return $this;
    }

    /**
     * @param string $tableAlias
     * @return $this
     */
    public function tableAlias(string $tableAlias)
    {
        $this->tableAlias = $tableAlias;
        return $this;
    }

    /**
     * @return string
     */
    public function getTableAlias(): string
    {
        return $this->tableAlias;
    }

    /**
     * @param string $order
     * @return $this
     */
    public function order(string $order)
    {
        $this->order = [$order];
        return $this;
    }

    public function addOrder(string $order)
    {
        $this->order[] = $order;
        return $this;
    }

    /**
     * @return array
     */
    public function getOrder(): array
    {
        return $this->order;
    }

    /**
     * @param array|string $select
     * @return $this
     */
    public function select($select)
    {
        $this->select = is_array($select) ? $select : [$select];
        return $this;
    }

    public function addSelect($select)
    {
        $this->select = array_merge($this->select, is_array($select) ? $select : [$select]);
        return $this;
    }

    /**
     * @return array
     */
    public function getSelect(): array
    {
        return $this->select;
    }

    /**
     * @param array|string $where
     * @param string $operator
     * @return $this
     */
    public function where($where, string $operator = 'AND')
    {
        $this->where[] = [$operator, $where];
        return $this;
    }

    /**
     * @return array
     */
    public function getWhere(): array
    {
        return $this->where;
    }

    public function addItem(string $key, $value, string $compare = '=', string $operator = 'AND')
    {
        return $this->where([$key => [$compare, $value]], $operator);
    }

    public function addInItem(string $key, array $values, string $operator = 'AND')
    {
        return $this->where([$key => ['IN', $values]], $operator);
    }

    public function addNotInItem(string $key, array $values, string $operator = 'AND')
    {
        return $this->where([$key => ['NOT IN', $values]], $operator);
    }

    public function addLikeItem(string $key, string $value, string $operator = 'AND')
    {
        return $this->where([$key => ['LIKE', $value]], $operator);
    }

    public function addBetweenItem(string $key, string $value1, string $value2, string $operator = 'AND')
    {
        return $this->where([$key => ['BETWEEN', [$value1, $value2]]], $operator);
    }

    public function addNotBetweenItem(string $key, string $value1, string $value2, string $operator = 'AND')
    {
        return $this->where([$key => ['NOT BETWEEN', [$value1, $value2]]], $operator);
    }

    public function addStrItem(string $str, array $params = [], string $operator = 'AND')
    {
        return $this->addParams($params)->where($str, $operator);
    }

    public function addOrItems(array ...$item)
    {
        $args = func_get_args();
        foreach ($args as $k => $arg) {
            if (empty($arg)) {
                unset($args[$k]);
            }
        }
        $arrays = array_merge(['OR'], $args);
        return $this->where($arrays);
    }

    public function addFalseItem()
    {
        return $this->addStrItem('1=0');
    }

    /**
     * @param string $group
     * @return $this
     */
    public function group(string $group)
    {
        $this->group = $group;
        return $this;
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param string $having
     * @param array $params
     * @return $this
     */
    public function having(string $having, array $params)
    {
        $this->having = $having;
        return $this->addParams($params);
    }

    /**
     * @return array
     */
    public function getHaving(): array
    {
        return $this->having;
    }

    /**
     * @param string $type 'INNER' | 'LEFT' | 'RIGHT'
     * @param string $table
     * @param string $alias
     * @param string $on
     * @param bool $needTablePrefix
     * @return $this
     */
    public function join(string $type, string $table, string $alias, string $on, bool $needTablePrefix = true)
    {
        $table = $needTablePrefix ? $this->getTablePrefix() . $table : $table;
        $this->join[] = [$type, $table, $alias, $on];
        return $this;
    }

    public function leftJoin(string $table, string $alias, string $on, bool $needTablePrefix = true)
    {
        return $this->join('LEFT', $table, $alias, $on, $needTablePrefix);
    }

    public function rightJoin(string $table, string $alias, string $on, bool $needTablePrefix = true)
    {
        return $this->join('RIGHT', $table, $alias, $on, $needTablePrefix);
    }

    public function innerJoin(string $table, string $alias, string $on, bool $needTablePrefix = true)
    {
        return $this->join('INNER', $table, $alias, $on, $needTablePrefix);
    }

    /**
     * @return array
     */
    public function getJoin(): array
    {
        return $this->join;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $offset
     * @return $this
     */
    public function offset(int $offset)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    public function addParams(array $params = [])
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    public function getTablePrefix(): string
    {
        return \L::app()->db->tablePrefix;
    }

    public function addId(int $id)
    {
        $this->addItem('id', $id);
        return $this;
    }
}