<?php

namespace app;

use app\common\AppBase;
use data\Database;
use DB\SQL\Mapper;

class Edit extends AppBase
{
    function get($f3)
    {
        $mapper = new Mapper(Database::mysql(), 'export');
        $mapper->load(['id = ?', $_GET['id']]);
        if ($mapper->dry()) {
            echo 'Can not find xml with id [' . $_GET['id'] . ']';
        } else {
            $f3->set('title', 'Edit');
            $f3->set('data', $mapper);
            echo \Template::instance()->render('edit.html');
        }
    }

    function post($f3)
    {
        $mapper = new Mapper(Database::mysql(), 'export');
        $mapper->load(['id = ?', $_POST['id']]);
        if ($mapper->dry()) {
            $this->error['text'] = 'Can not find xml with id [' . $_POST['id'] . ']';
            echo $this->jsonResponse();
        } else {
            $mapper['xml'] = $_POST['xml'];
            $mapper['return_xml'] = '';
            $mapper->save();
            $this->error['code'] = 0;
            echo $this->jsonResponse();
        }
    }
}
