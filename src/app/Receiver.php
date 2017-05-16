<?php
namespace app;

use data\Database;
use DB\SQL\Mapper;

class Receiver
{
    function receive($f3)
    {
        //$xml = file_get_contents($f3->UI . 'notify.xml');
        $xml = $_POST['message'];
        $mapper = $this->parseResult($xml);
        foreach($mapper->fields() as $field) var_dump($mapper->$field);
    }

    function parseResult($xml)
    {
        $f3 = \Base::instance();
        try {
            $parser = xml_parser_create();
            xml_parse_into_struct($parser, $xml, $result, $index);

            $guid = $result[$index['CEBEXPINVTRETURNMESSAGE'][0]]['attributes']['GUID'];
            $returnGuid = $result[$index['GUID'][0]]['value'];
            $returnTime = $result[$index['RETURNTIME'][0]]['value'];
            $returnInfo = $result[$index['RETURNINFO'][0]]['value'];
            $returnStatus = $result[$index['RETURNSTATUS'][0]]['value'];

            $mapper = new Mapper(Database::mysql(), 'notify');
            $mapper->load(['return_guid = ?', $returnGuid]);
            if ($mapper->dry()) {
                $mapper['guid'] = $guid;
                $mapper['return_guid'] = $returnGuid;
                $mapper['return_time'] = $returnTime;
                $mapper['return_info'] = $returnInfo;
                $mapper['return_status'] = $returnStatus;
                $mapper['xml'] = $xml;
                $mapper->save();
            } else {
                $f3->log('Existed: ' . $mapper['xml']);
            }

            return $mapper;
        } catch (\Exception $e) {
            return [
                'return_info' => $e->getMessage(),
                'return_status' => $e->getCode(),
                'xml' => $e->getTraceAsString()
            ];
        }
    }
}
