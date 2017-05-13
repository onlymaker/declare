<?php

namespace app;

use data\Database;
use DB\SQL\Mapper;

class Index
{
    function get($f3)
    {
        $size = 20;
        $mapper = new Mapper(Database::mysql(), 'export');
        $count = ceil($mapper->count() / $size);
        $pageNo = $_GET['pageNo'];
        if ($pageNo < 1) $pageNo = 1;
        else if ($pageNo > $count) $pageNo = $count;
        $page = $mapper->paginate($pageNo - 1, $size);
        $f3->set('pageNo', $pageNo);
        $f3->set('pageContent', $page['subset']);
        $f3->set('pageCount', $count);
        echo \Template::instance()->render('index.html');
    }
}
