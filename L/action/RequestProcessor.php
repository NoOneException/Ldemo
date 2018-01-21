<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/24
 * Time: 16:05
 */

namespace L\action;


use L\checker\CheckerManager;
use L\interceptor\ActionAfterInterceptor;
use L\interceptor\ActionBeforeInterceptor;
use L\response\Response;
use L\route\Request;

class RequestProcessor
{
    private $performer;

    public function __construct(RequestPerformer $performer)
    {
        $this->performer = $performer;
    }

    public function onProcess(): Response
    {
        $this->performer->init();
        $this->execActionBeforeInterceptors($this->performer->request);
        $this->execParamCheckers();
        $response = $this->performer->run();
        $this->execActionAfterInterceptors($response);
        return $response;
    }

    private function execActionBeforeInterceptors(Request $request)
    {
        if ($actionBeforeInterceptors = $this->performer->beforeAction()) {
            foreach ($actionBeforeInterceptors as $actionBeforeInterceptor) {
                if (is_string($actionBeforeInterceptor)) {
                    $actionBeforeInterceptor = new $actionBeforeInterceptor();
                }
                if ($actionBeforeInterceptor instanceof ActionBeforeInterceptor) {
                    $actionBeforeInterceptor->onActionBefore($request);
                }
            }
        }
    }

    private function execActionAfterInterceptors(Response $response)
    {
        if ($actionAfterInterceptors = $this->performer->afterAction()) {
            foreach ($actionAfterInterceptors as $actionAfterInterceptor) {
                if (is_string($actionAfterInterceptor)) {
                    $actionAfterInterceptor = new $actionAfterInterceptor();
                }
                if ($actionAfterInterceptor instanceof ActionAfterInterceptor) {
                    $actionAfterInterceptor->onActionAfter($response);
                }
            }
        }
    }

    private function execParamCheckers()
    {
        if ($checkers = $this->performer->checkers()) {
            CheckerManager::doCheck($checkers);
        }
    }
}