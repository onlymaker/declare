<?php
namespace app;

use data\Database;
use DB\SQL\Mapper;

class Receiver
{
    function receive($f3)
    {
        $xml = file_get_contents($f3->UI . 'notify.xml');
        $parser = xml_parser_create();
        xml_parse_into_struct($parser, $xml, $result, $index);

        $guid = $result[$index['CEBEXPINVTRETURNMESSAGE'][0]]['attributes']['GUID'];
        $returnGuid = $result[$index['GUID'][0]]['value'];
        $returnTime = $result[$index['RETURNTIME'][0]]['value'];
        $returnInfo = $result[$index['RETURNINFO'][0]]['value'];
        $returnStatus = $result[$index['RETURNSTATUS'][0]]['value'];

        $mapper = new Mapper(Database::mysql(), 'notify');
        $mapper['guid'] = $guid;
        $mapper['return_guid'] = $returnGuid;
        $mapper['return_time'] = $returnTime;
        $mapper['return_info'] = $returnInfo;
        $mapper['return_status'] = $returnStatus;
        $mapper['xml'] = $xml;
        $mapper->save();

        foreach($mapper->fields() as $field) var_dump($mapper->$field);
    }
}
