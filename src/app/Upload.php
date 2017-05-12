<?php
namespace app;

use app\common\Url;

class Upload extends \Web
{
    use Url;

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
        // TODO: parse excel
    }
}
