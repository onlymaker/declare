<?php
namespace app;

class Receiver
{
    function receive($f3)
    {
        $parser = xml_parser_create();
        xml_parse_into_struct($parser, file_get_contents($f3->UI . 'notify.xml'), $result, $index);
        var_dump($result[$index['CEBEXPINVTRETURNMESSAGE'][0]]['attributes']['GUID']);
        var_dump($result[$index['GUID'][0]]['value']);
        var_dump($result[$index['RETURNTIME'][0]]['value']);
        var_dump($result[$index['RETURNINFO'][0]]['value']);
        var_dump($result[$index['RETURNSTATUS'][0]]['value']);
    }
}
