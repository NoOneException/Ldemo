<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/28
 * Time: 11:20
 */

namespace L\db;

final class SQLBuilder
{
    const PARAM_PREFIX = ':pr';

    public function insert(array $data, string $table): DBSql
    {
        return $this->insertAll([$data], $table);
    }

    public function insertAll(array $datas, string $table): DBSql
    {
        $sql = 'INSERT INTO %s %s ;';
        $params = [];
        $sql = sprintf($sql, $table, $this->buildInsertData($datas, $params));
        return new DBSql($sql, $params);
    }

    public function update(array $data, DBCondition $dbCondition): DBSql
    {
        $params = $dbCondition->getParams();
        $sql = implode(' ', [
            'UPDATE',
            $this->buildTable($dbCondition->getTable()),
            $this->buildUpdateData($data, $params),
            $this->buildWhere($dbCondition->getWhere(), $params),
            $this->buildLimit($dbCondition->getLimit(), $dbCondition->getOffset())
        ]);
        return new DBSql($sql . ';', $dbCondition->getParams());
    }

    public function delete(DBCondition $dbCondition): DBSql
    {
        $sql = implode(' ', [
            'DELETE FROM',
            $this->buildTable($dbCondition->getTable()),
            $this->buildWhere($dbCondition->getWhere(), $dbCondition->getParams()),
            $this->buildLimit($dbCondition->getLimit(), $dbCondition->getOffset())
        ]);
        return new DBSql($sql . ';', $dbCondition->getParams());
    }

    public function query(DBCondition $dbCondition): DBSql
    {
        $params = $dbCondition->getParams();
        $sql = implode(' ', [
            $this->buildSelect($dbCondition->getSelect()),
            $this->buildTable($dbCondition->getTable(), $dbCondition->getTableAlias()),
            $this->buildJoin($dbCondition->getJoin()),
            $this->buildWhere($dbCondition->getWhere(), $params),
            $this->buildGroup($dbCondition->getGroup()),
            $this->buildOrder($dbCondition->getOrder()),
            $this->buildHaving($dbCondition->getHaving()),
            $this->buildLimit($dbCondition->getLimit(), $dbCondition->getOffset())
        ]);
        return new DBSql($sql . ';', $params);
    }

    private function buildTable(string $table, string $tableAlias = ''): string
    {
        if ($tableAlias) {
            return "`$table` AS $tableAlias";
        }
        return "`$table`";
    }

    private function buildInsertData(array $datas, array &$params): string
    {
        $lineArr = [];
        $keyArr = [];
        foreach ($datas as $i => $data) {
            $numArr = [];
            foreach ($data as $key => $value) {
                if ($i === 0) {
                    $keyArr[] = '`' . $key . '`';
                }
                $numArr[] = '?';
                $params[] = $value;
            }
            $lineArr[] = '(' . implode(',', $numArr) . ')';
        }
        return '(' . implode(',', $keyArr) . ') VALUES ' . implode(',', $lineArr);
    }

    private function buildUpdateData(array $data, array &$params): string
    {
        $item = [];
        foreach ($data as $key => $value) {
            $prKey = self::PARAM_PREFIX . count($params);
            $item[] = "`$key`=$prKey";
            $params[$prKey] = $value;
        }
        return 'SET ' . implode(',', $item);
    }

    private function buildOrder(array $order): string
    {
        return $order ? 'ORDER BY ' . implode(',', $order) : '';
    }

    private function buildSelect(array $select): string
    {
        return 'SELECT ' . implode(',', $select) . ' FROM';
    }

    private function buildJoin(array $join): string
    {
        if (!$join) {
            return '';
        }
        $res = [];
        foreach ($join as $item) {
            $res[] = "{$item[0]} JOIN {$item[1]} AS {$item[2]} ON {$item[3]}";
        }
        return implode(' ', $res);
    }

    private function buildWhere(array $where, array &$params): string
    {
        $conditionStr = '';
        if ($where) {
            foreach ($where as $whereItem) {
                list($operator, $item) = $whereItem;
                $operator = trim(strtoupper($operator));
                $operator === 'OR' or $operator = 'AND';
                if (is_array($item)) {
                    $innerOperator = 'AND';
                    if (isset($item[0]) && $item[0] == 'OR') {
                        $innerOperator = 'OR';
                        unset($item[0]);
                    }
                    $partArr = [];
                    foreach ($item as $key => $value) {
                        if (strpos($key, '.') !== false) {
                            $key = "`$key`";
                        }
                        if (is_array($value)) {
                            if (is_array($value[0])) {
                                foreach ($value as $val) {
                                    $partArr[] = $this->buildItemByArr($key, $val, $params);
                                }
                            } else {
                                $partArr[] = $this->buildItemByArr($key, $value, $params);
                            }
                        } else {
                            $partArr[] = $this->buildSimpleItem('=', $key, $value, $params);
                        }
                    }
                    if ($partArr) {
                        $partStr = implode(" $innerOperator ", $partArr);
                        $conditionStr .= " {$operator} ( {$partStr} )";
                    }
                } elseif (is_string($item)) {
                    $conditionStr .= " {$operator} ( {$item} ) ";
                }
            }
        }
        return 'WHERE 1 ' . $conditionStr;
    }

    private function buildItemByArr(string $key, $value, array &$params): string
    {
        $compare = trim(strtoupper($value[0]));
        switch ($compare) {
            case 'IN':
            case 'NOT IN':
                $res = $this->buildInItem($compare, $key, $value[1], $params);
                break;
            case 'BETWEEN':
            case 'NOT BETWEEN':
                $res = $this->buildBetweenItem($compare, $key, $value[1], $params);
                break;
            default:
                $res = $this->buildSimpleItem($compare, $key, $value[1], $params);
        }
        return $res;
    }

    private function buildInItem(string $compare, string $key, array $values, array &$params): string
    {
        if (empty($values)) {
            return $compare === 'NOT IN' ? '1' : '1=0';
        }
        $prKeys = [];
        foreach ($values as $i => $value) {
            $prKey = self::PARAM_PREFIX . count($params);
            $prKeys[$i] = $prKey;
            $params[$prKey] = $value;
        }
        $prValueStr = implode(',', $prKeys);
        return "$key $compare ( $prValueStr ) ";
    }

    private function buildBetweenItem(string $compare, string $key, array $values, array &$params): string
    {
        $prKey1 = self::PARAM_PREFIX . count($params);
        $params[$prKey1] = $values[0];
        $prKey2 = self::PARAM_PREFIX . count($params);
        $params[$prKey2] = $values[1];
        return "$key $compare $prKey1 AND $prKey2 ";
    }

    private function buildSimpleItem(string $compare, string $key, $value, array &$params): string
    {
        if (is_null($value)) {
            return "$key IS NULL ";
        }
        $prKey = self::PARAM_PREFIX . count($params);
        $params[$prKey] = $value;
        return "$key $compare $prKey ";
    }

    private function buildGroup(string $group): string
    {
        return $group ? 'GROUP BY ' . $group : '';
    }

    private function buildHaving(array $having): string
    {
        return $having ? 'HAVING ' . $having : '';
    }

    private function buildLimit(int $limit, int $offset): string
    {
        if (!$limit) {
            return '';
        }
        if (!$offset) {
            return "LIMIT $limit";
        }
        return "LIMIT $offset,$limit";
    }

}