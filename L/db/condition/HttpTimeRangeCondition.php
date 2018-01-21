<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/9/30
 * Time: 11:16
 */

namespace L\db\condition;

use L\route\Request;

class HttpTimeRangeCondition extends TimeRangeCondition
{
    public function getStartTime(Request $request)
    {
        $start = $request->getParam($this->startTime);
        if ($start && !is_numeric($start)) {
            $start = strtotime(date('Y-m-d', strtotime($start)));
        }
        return $start;
    }

    public function getEndTime(Request $request)
    {
        $end = $request->getParam($this->endTime);
        if ($end && !is_numeric($end)) {
            $end = strtotime(date('Y-m-d', strtotime($end))) + 86399;
        }
        return $end;
    }
}