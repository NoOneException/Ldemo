<?php

namespace module\admin\index;

use L\action\Controller;
use L\response\NullData;

class IndexController extends Controller
{
    public function indexAction()
    {
        var_dump(222);
        return new NullData();
    }
}