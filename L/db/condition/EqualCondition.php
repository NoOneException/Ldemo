<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/30
 * Time: 19:42
 */

namespace L\db\condition;


use L\db\DBCondition;
use L\route\Request;
use L\util\StringUtil;

class EqualCondition implements ConditionHandler
{
    protected $httpField;
    protected $dbField;

    /**
     * EqualCondition constructor.
     * @param $httpField
     * @param null $dbField
     */
    public function __construct($httpField, $dbField = null)
    {
        $this->httpField = $httpField;
        $this->dbField = $dbField == null ? $httpField : $dbField;
    }

    public function onHandle(DBCondition $dbCondition, Request $request)
    {
        $val = $request->getParam($this->httpField);
        $isEmpty = StringUtil::isEmpty($val);
        if (!$isEmpty) {
            $dbCondition->addItem($this->dbField, $val);
        }
    }
}