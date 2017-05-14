<?php
namespace app;

use app\common\Url;

class Upload extends \Web
{
    use Url;

    function get($f3)
    {
        $f3->set('title', 'Upload');
        echo \Template::instance()->render('upload.html');
    }

    function upload($f3)
    {
        list($receive) = array_keys(parent::receive(null, true, false));

        $f3->log('receive: ' . $receive);

        $name = preg_replace('/^.+[\\\\\\/]/', '', $receive);

        if (is_file($receive)) {
            $type = $this->mime($receive);
            if ($type == 'application/vnd.ms-excel') {
                $this->parseExcel($receive);
                $url = $this->url(substr($f3->get('UPLOADS'), strlen($f3->get('ROOT'))) . $name);
            } else {
                unlink($receive);
            }
        }

        echo json_encode(['name' => $name, 'type' => $type, 'url' => $url], JSON_UNESCAPED_UNICODE);
    }

    function beforeRoute($f3)
    {
        if ($f3->get('AJAX')) {
            header('Access-Control-Allow-Origin:*');
        }
    }

    function parseExcel($file) {
        @ini_set('memory_limit', '256M');
        $excel = \PHPExcel_IOFactory::load($file);
        $sheet = $excel->getSheet(0);
        $rows = $sheet->toArray();
        $f3 = \Base::instance();
        $creator = new Creator();
        foreach ($rows as $i => $data) {
            $xml = $creator->buildXml($f3, $data[0], $data[1], $data[2]);
            if ($xml == $data[0]) {
                $f3->log(
                    '{excel} row {i}: {id} not found',
                    [
                        'excel' => $file,
                        'i' =>$i,
                        'id' => $data[0]
                    ]
                );
            } else {
                $f3->log(
                    '{excel} row {i}: {id} handled',
                    [
                        'excel' => $file,
                        'i' =>$i,
                        'id' => $data[0]
                    ]
                );
            }
        }
    }
}
