<?php

namespace app;

use app\common\AppBase;
use data\Database;
use DB\SQL\Mapper;
use Httpful\Mime;
use Httpful\Request;

class Index extends AppBase
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
        $f3->set('title', 'Index');
        $f3->set('pageNo', $pageNo);
        $f3->set('pageContent', $page['subset']);
        $f3->set('pageCount', $count);
        echo \Template::instance()->render('index.html');
    }

    function post($f3)
    {
        $mapper = new Mapper(Database::mysql(), 'export');
        $mapper->load(['id = ?', $_POST['id']]);
        if ($mapper->dry()) {
            $this->error['text'] = 'Can not find xml';
            echo $this->jsonResponse();
        } else {
            $status = new Mapper(Database::mysql(), 'export');
            $status->max = 'max(status)';
            $status->load();
            $next = ($status['max'] ?? 0) + 1;
            $xml = str_replace('{{@serial}}', $next, $mapper['xml']);
            $response = Request::post($f3->get('DECLARE_URL'))->body(['xml' => $xml])->sendsType(Mime::FORM)->send();
            $this->error['code'] = 0;
            echo $this->jsonResponse(['body' => $response->body, 'raw' => $response->raw_body]);
        }
    }
}
