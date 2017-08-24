<?php

namespace module\admin\index;

use L\action\Action;
use L\response\JsonData;
use L\response\RenderData;
use L\response\Response;

class IndexAction extends Action
{
    public function run(): Response
    {
        echo 111;
//        return new JsonData(['aa'=>'aa','bb'=>'bb']);
        return new RenderData(['aa' => 'aa', 'bb' => 'bb']);
    }
}