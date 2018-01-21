<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2018/1/21
 * Time: 16:27
 */

namespace L\action;


use L;
use L\base\Component;
use L\interceptor\ActionAfterInterceptor;
use L\interceptor\ActionBeforeInterceptor;
use L\response\Response;
use L\route\Request;
use ReflectionClass;


/**
 * Class RequestPerformer
 * @package L\action
 * @property  Request request
 */
abstract class RequestPerformer implements Component
{
    public $request;
    public $baseUrl;
    public $lastUrl;

    public function init()
    {
        $this->baseUrl = L::app()->url->getBaseUrl();
        $this->request = L::app()->request;
        $this->lastUrl = $this->request->getLastUrl();
        $this->initProperty();
    }

    abstract public function run(): Response;

    private function initProperty()
    {
        $r = new ReflectionClass(get_called_class());
        foreach ($r->getProperties() as $property) {
            $name = $property->getName();
            if ($property->isPublic() && $this->request->getParam($name) !== null) {
                $this->$name = $this->request->getParam($name);
            }
        }

    }

    /**
     * @return ActionBeforeInterceptor[]
     */
    public function beforeAction(): array
    {
        return [];
    }

    /**
     * @return array [
     *      [ $checker_name , $checker_args],
     * ]
     */
    public function checkers(): array
    {
        return [];
    }

    /**
     * @return ActionAfterInterceptor[]
     */
    public function afterAction(): array
    {
        return [];
    }

    public function genurl(string $actionStr = '', array $params = [])
    {
        return L::app()->url->genurl($actionStr, $params);
    }
}