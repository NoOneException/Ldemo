<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/10/9
 * Time: 12:19
 */

namespace L\event;


class Event
{
    private $params = null;
    private $type;

    /**
     * @return null
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param mixed $params
     * @return $this
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

}