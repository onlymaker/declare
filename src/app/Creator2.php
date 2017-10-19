<?php

namespace app;

use data\Database;
use DB\SQL\Mapper;
use Ramsey\Uuid\Uuid;

class Creator2
{
    static $info = [
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ'
    ];
    private $id;
    private $data;

    function buildXml($f3, $data) {
        $this->id = trim($data[0]);

        $db = Database::mysql();
        $mapper = new Mapper($db, 'export');
        $mapper->load(['trace_id = ?', $this->id]);
        if (!$mapper->dry()) {
            return $mapper['xml']; // trace id already existed in export
        }

        $this->data = $data;

        $this->buildOrderInfo($f3);
        $this->buildExpInventoryHead($f3);
        $this->buildExpInventoryList($f3);

        $f3->set('guid', Uuid::uuid1());
        $f3->set('expOrderInfo', $this->expOrderInfo);
        $f3->set('expInventoryHead', $this->expInventoryHead);
        $f3->set('expInventoryList', $this->expInventoryList);
        $f3->set('baseTransfer', $this->baseTransfer);
        $f3->set('baseSubscribe', $this->baseSubscribe);
        $f3->set('signature', $this->signature);
        $f3->set('extendMessage', $this->extendMessage);
        $xml = \Template::instance()->render('declare-template.xml');

        $mapper['trace_id'] = $this->id;
        $mapper['xml'] = $xml;
        $mapper->save();

        return $xml;
    }

    function buildOrderInfo($f3)
    {
        $info = array_flip(self::$info);
        $this->expOrderInfo['orderNo'] = $this->id;
        $this->expOrderInfo['payTime'] = date('YmdHis', strtotime($this->data[$info['D']]));
        $this->expOrderInfo['goodsNum'] = $this->data[$info['C']];
        $this->expOrderInfo['currency'] = $this->data[$info['E']];
        $this->expOrderInfo['rate'] = $this->data[$info['F']];
        $this->expOrderInfo['orderTotalAmount'] = $this->data[$info['G']];
        $this->expOrderInfo['consignee'] = $this->data[$info['H']];
        $this->expOrderInfo['consigneeCountry'] = $this->data[$info['I']];
        $this->expOrderInfo['consigneeAddress'] = $this->data[$info['J']];
        $this->expOrderInfo['consigneeTel'] = $this->data[$info['K']];
        $this->expOrderInfo['consigneeEmail'] = $this->data[$info['L']];
        $this->expOrderInfo['ebpCode'] = $f3->get('EBPCODE');
        $this->expOrderInfo['ebpName'] = $f3->get('EBPNAME');
    }

    private $expOrderInfo = [
        'orderNo' => '',
        'payTime' => '',
        'goodsNum' => '',
        'currency' => '',
        'rate' => '',
        'orderTotalAmount' => '',
        'consignee' => '',
        'consigneeCountry' => '',
        'consigneeAddress' => '',
        'consigneeTel' => '',
        'consigneeEmail' => '',
        'ebpCode' => '',
        'ebpName' => '',
    ];

    function buildExpInventoryHead($f3) {
        $info = array_flip(self::$info);
        $this->expInventoryHead['orderNo'] = $this->id;
        $this->expInventoryHead['ebpCode'] = $f3->get('EBPCODE');
        $this->expInventoryHead['ebpName'] = $f3->get('EBPNAME');
        $this->expInventoryHead['ebcCode'] = $f3->get('EBCCODE');
        $this->expInventoryHead['ebcName'] = $f3->get('EBCNAME');
        $this->expInventoryHead['productCode'] = '9151010032742290X5';
        $this->expInventoryHead['productName'] = '成都欧魅时尚科技有限责任公司';
        $this->expInventoryHead['logisticsCode'] = '5101989827';
        $this->expInventoryHead['logisticsName'] = '成都嘉航报关服务有限公司';
        $this->expInventoryHead['logisticsNo'] = $this->data[$info['B']];
        $this->expInventoryHead['preNo'] = '';
        $this->expInventoryHead['invtNo'] = '';
        $this->expInventoryHead['ieFlag'] = 'E';
        $this->expInventoryHead['declTime'] = date('YmdHis');
        $this->expInventoryHead['customsCode'] = 7902;
        $this->expInventoryHead['portCode'] = 7902;
        $this->expInventoryHead['ieDate'] = $this->data[$info['M']];
        $this->expInventoryHead['agentCode'] = '510196552A';
        $this->expInventoryHead['agentName'] = '成都欧魅时尚科技有限责任公司';
        $this->expInventoryHead['areaCode'] = '5101989827';
        $this->expInventoryHead['areaName'] = '成都嘉航报关服务有限公司';
        $this->expInventoryHead['tradeMode'] = 9610;
        $this->expInventoryHead['trafMode'] = 5;
        $this->expInventoryHead['trafNo'] = '';
        $this->expInventoryHead['voyageNo'] = $this->data[$info['N']];
        $this->expInventoryHead['billNo'] = $this->data[$info['O']];
        $this->expInventoryHead['loctNo'] = '';
        $this->expInventoryHead['packageNum'] = '';
        $this->expInventoryHead['licenseNo'] = '';
        $this->expInventoryHead['arrivedPort'] = $this->data[$info['P']];
        $this->expInventoryHead['country'] = $this->data[$info['Q']];
        $this->expInventoryHead['freight'] = $this->data[$info['R']];
        $this->expInventoryHead['feeCurrency'] = $this->data[$info['S']];
        $this->expInventoryHead['feeFlag'] = $this->data[$info['T']];
        $this->expInventoryHead['insuredFee'] = $this->data[$info['U']];
        $this->expInventoryHead['inrCurrency'] = $this->data[$info['V']];
        $this->expInventoryHead['inrFlag'] = $this->data[$info['W']];
        $this->expInventoryHead['wrapType'] = $this->data[$info['X']];
        $this->expInventoryHead['packNo'] = $this->data[$info['Y']];
        $this->expInventoryHead['grossWeight'] = $this->data[$info['Z']];
        $this->expInventoryHead['netWeight'] = $this->data[$info['AA']];
        $this->expInventoryHead['note'] = '';
    }

    private $expInventoryHead = [
        'orderNo' => '',
        'ebpCode' => '',
        'ebpName' => '',
        'ebcCode' => '',
        'ebcName' => '',
        'productCode' => '',
        'productName' => '',
        'logisticsCode' => '',
        'logisticsName' => '',
        'logisticsNo' => '',
        'preNo' => '',
        'invtNo' => '',
        'ieFlag' => '',
        'declTime' => '',
        'customsCode' => '',
        'portCode' => '',
        'ieDate' => '',
        'agentCode' => '',
        'agentName' => '',
        'areaCode' => '',
        'areaName' => '',
        'tradeMode' => '',
        'trafMode' => '',
        'trafNo' => '',
        'voyageNo' => '',
        'billNo' => '',
        'loctNo' => '',
        'packageNum' => '',
        'licenseNo' => '',
        'arrivedPort' => '',
        'country' => '',
        'freight' => '',
        'feeCurrency' => '',
        'feeFlag' => '',
        'insuredFee' => '',
        'inrCurrency' => '',
        'inrFlag' => '',
        'wrapType' => '',
        'packNo' => '',
        'grossWeight' => '',
        'netWeight' => '',
        'note' => '',
    ];

    function buildExpInventoryList($f3) {
        $info = array_flip(self::$info);
        $this->expInventoryList['gnum'] = '{{@serial}}'; // set when report
        $this->expInventoryList['itemNo'] = $this->data[$info['AC']];
        $this->expInventoryList['gcode'] = $this->data[$info['AE']];
        $this->expInventoryList['gname'] = $this->data[$info['AF']];
        $this->expInventoryList['gmodel'] = $this->data[$info['AG']];
        $this->expInventoryList['barCode'] = $this->id;
        $this->expInventoryList['country'] = $this->data[$info['AK']];
        $this->expInventoryList['currency'] = $this->data[$info['E']];
        $this->expInventoryList['qty'] = $this->data[$info['AI']];
        $this->expInventoryList['qty1'] = $this->data[$info['AO']];
        $this->expInventoryList['qty2'] = 1;
        $this->expInventoryList['unit'] = $this->data[$info['AL']];
        $this->expInventoryList['unit1'] = $this->data[$info['AN']];
        $this->expInventoryList['unit2'] = '011';
        $this->expInventoryList['price'] = $this->data[$info['AH']];
        $this->expInventoryList['totalPrice'] = $this->data[$info['AJ']];
        $this->expInventoryList['note'] = '';
    }

    private $expInventoryList = [
        'gnum' => '',
        'itemNo' => '',
        'gcode' => '',
        'gname' => '',
        'gmodel' => '',
        'barCode' => '',
        'country' => '',
        'currency' => '',
        'qty' => '',
        'qty1' => '',
        'qty2' => '',
        'unit' => '',
        'unit1' => '',
        'unit2' => '',
        'price' => '',
        'totalPrice' => '',
        'note' => '',
    ];

    private $baseTransfer = [
        'copCode' => '9151010032742290X5',
        'copName' => '成都欧魅时尚科技有限责任公司',
        'dxpMode' => 'DXP',
        'dxpId' => '',
        'note' => '',
    ];

    private $baseSubscribe = [
        'status' => '',
        'dxpMode' => '',
        'dxpAddress' => '',
        'note' => '',
    ];

    private $signature = [
        'SignedInfo' => '',
        'CanonicalizationMethod' => '',
        'SignatureMethod' => '',
        'Reference' => '',
        'Transforms' => '',
        'DigestMethod' => '',
        'DigestValue' => '',
        'SignatureValue' => '',
        'KeyInfo' => '',
        'KeyName' => '',
        'X509Data' => '',
        'X509Certificate' => '',
    ];

    private $extendMessage = [
        'name' => '',
        'version' => '',
        'Message' => '',
    ];
}
