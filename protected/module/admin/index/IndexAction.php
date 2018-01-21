<?php

namespace module\admin\index;

use L\action\Action;
use L\db\DBModel;
use L\db\model\Delete;
use L\db\model\Query;
use L\response\JsonData;
use L\response\RenderData;
use L\response\Response;

class IndexAction extends Action
{
    public function run(): Response
    {
        $data = Query::model('client')->addId(2106)->one();
        var_dump($data);
        var_dump(DBModel::getLastSql()->toString());
        echo 111;
        return new RenderData(['aa'=>'aa','bb'=>'bb']);
//        return new RenderData(['aa' => 'aa', 'bb' => 'bb']);
    }
}