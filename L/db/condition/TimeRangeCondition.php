<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/9/30
 * Time: 10:54
 */

namespace L\db\condition;

use L\db\DBCondition;
use L\route\Request;
use L\util\StringUtil;

class TimeRangeCondition implements ConditionHandler
{
    protected $startTime;
    protected $endTime;
    protected $startTimefield;
    protected $endTimeField;

    /**
     * TimeRangeCondition constructor.
     * @param int $startTime
     * @param int $endTime
     * @param string $startTimeField
     * @param string $endTimeField
     */
    public function __construct(int $startTime = 0, int $endTime = 0, string $startTimeField = 't.time', string $endTimeField = '')
    {
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->startTimefield = $startTimeField;
        $this->endTimeField = $endTimeField ? $endTimeField : $startTimeField;
    }

    public function getStartTime(Request $request)
    {
        return $this->startTime;
    }

    public function getEndTime(Request $request)
    {
        return $this->endTime;
    }

    public function onHandle(DBCondition $dbCondition, Request $request)
    {
        $start_time = $this->getStartTime($request);
        $end_time = $this->getEndTime($request);
        if (!is_numeric($start_time)) {
            $start_time = $start_time ? strtotime($start_time) : '';
        }
        if (!is_numeric($end_time)) {
            $end_time = $end_time ? strtotime($end_time) : '';
        }

        if (!StringUtil::isEmpty($start_time)) {
            $dbCondition->addItem($this->endTimeField, $start_time, '>=');
        }

        if (!StringUtil::isEmpty($end_time)) {
            $dbCondition->addItem($this->startTimefield, $end_time, '<=');
        }
    }
}