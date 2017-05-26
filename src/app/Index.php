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
            $this->error['text'] = 'Can not find xml with id [' . $_POST['id'] . ']';
            echo $this->jsonResponse();
        } else {
            $status = new Mapper(Database::mysql(), 'export');
            $status->max = 'max(status)';
            $status->load();
            $next = ($status['max'] ?? 0) + 1;
            $xml = str_replace('{{@serial}}', $next, $mapper['xml']);

            if (!$mapper['return_xml']) {
                $f3->log('report ' . $mapper['trace_id']);
                $response = Request::post($f3->get('DECLARE_URL'))->body(['xml' => $xml])->sendsType(Mime::FORM)->send();
                ob_start();
                var_dump($response);
                $f3->log(ob_get_clean());
                $result = $this->parseReturnXml($response->raw_body);
                $mapper['return_info'] = $result['return_info'];
                $mapper['return_status'] = $result['return_status'];
                $mapper['return_xml'] = $result['return_xml'];
                $mapper->save();
            }

            $this->error['code'] = 0;
            echo $this->jsonResponse([
                'info' => $mapper['return_info'],
                'status' => $mapper['return_status'],
                'xml' => $mapper['xml']
            ]);
        }
    }

    function parseReturnXml($xml)
    {
        try {
            $parser = xml_parser_create();
            xml_parse_into_struct($parser, $xml, $result, $index);

            //$guid = $result[$index['CEBEXPINVTRETURNMESSAGE'][0]]['attributes']['GUID'];
            //$returnGuid = $result[$index['GUID'][0]]['value'];
            //$returnTime = $result[$index['RETURNTIME'][0]]['value'];
            $returnInfo = $result[$index['RETURNINFO'][0]]['value'];
            $returnStatus = $result[$index['RETURNSTATUS'][0]]['value'];

            return [
                'return_info' => $returnInfo,
                'return_status' => $returnStatus,
                'return_xml' => $xml
            ];
        } catch (\Exception $e) {
            return [
                'return_info' => $e->getMessage(),
                'return_status' => $e->getCode(),
                'return_xml' => $e->getTraceAsString()
            ];
        }
    }
}
