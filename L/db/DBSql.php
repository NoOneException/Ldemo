<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/29
 * Time: 14:43
 */

namespace L\db;

use L\util\StringUtil;
use L\util\SysUtil;

final class DBSql
{
    private $sql;
    private $params;
    private $curSqlLine;
    private $execTime;
    private $memory;
    private $curTime;

    public function __construct(string $sql, array $params = [])
    {
        $this->sql = $sql;
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getSql(): string
    {
        return $this->sql;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    public function toString(): string
    {
        return 'SQL:' . $this->toReadableSql() . "\n Params:" . var_export($this->params,true);
    }

    public function toHtml(): string
    {

    }

    public function toReadableSql(): string
    {
        $sql = $this->sql;
        if ($this->params) {
            foreach ($this->params as $k => $v) {
                $needle = is_int($k) ? '?' : $k;
                $sql = StringUtil::replaceOnce($needle, $v, $sql);
            }
        }
        return $sql;
    }

    /**
     * @param array $debugBackTraces
     * @return $this
     */
    public function setCurSqlLine(array $debugBackTraces)
    {
        if (!$this->curSqlLine) {
            $this->curSqlLine = SysUtil::getCallLine($debugBackTraces, 3);
        }
        return $this;
    }

    /**
     * @param float $execTime
     * @return $this
     */
    public function setExecTime(float $execTime)
    {
        $this->execTime = $execTime;
        return $this;
    }

    /**
     * @param string $memory
     * @return $this
     */
    public function setMemory(string $memory)
    {
        $this->memory = $memory;
        return $this;
    }

    /**
     * @param float $curTime
     * @return $this
     */
    public function setCurTime(float $curTime)
    {
        $this->curTime = $curTime;
        return $this;
    }

}