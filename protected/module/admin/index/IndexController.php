<?php

namespace module\admin\index;

use L\action\Controller;
use L\response\NullData;
use L\response\RenderData;

class IndexController extends Controller
{
    public function actionIndex()
    {
        var_dump(222);
        return new RenderData();
    }
}