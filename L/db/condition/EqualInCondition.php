<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/30
 * Time: 19:53
 */

namespace L\db\condition;


use L\db\DBCondition;
use L\route\Request;
use L\util\StringUtil;

class EqualInCondition extends EqualCondition
{
    public function onHandle(DBCondition $dbCondition, Request $request)
    {
        $val = $request->getParam($this->httpField);
        $val = trim($val, ',');
        if (!StringUtil::isEmpty($val)) {
            $vals = StringUtil::safeExplode(',', $val);
            $dbCondition->addInItem($this->dbField, $vals);
        }
    }
}