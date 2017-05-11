<?php

namespace app;

use data\Database;
use data\OrderDB;
use DB\SQL\Mapper;
use Httpful\Mime;
use Httpful\Request;
use Ramsey\Uuid\Uuid;

class Creator
{
    function create($f3)
    {
        $xml = $this->buildXml($f3);
        header('Content-Type:application/xml');
        echo $xml;
        ob_start();
        var_dump(Request::post($f3->get('DECLARE_URL'))->body($xml)->sendsType(Mime::FORM)->send());
        $f3->log(ob_get_clean());
    }

    function buildXml($f3) {
        $f3->set('id', $_REQUEST['id']);
        $f3->set('ap', $_REQUEST['ap']);
        $f3->set('cc', $_REQUEST['cc']);

        $odb = OrderDB::mysql();

        $order = new Mapper($odb, 'order_item');
        $order->load(['trace_id = ?', $f3->get('id')]);

        if ($order->dry()) {
            $f3->log('Can not find order: id');
            return $f3->get('id');
        }

        $model = new Mapper($odb, 'prototype');
        $model->load(['ID = ?', $order['prototype_id']]);

        $contact = new Mapper($odb, 'distribution');
        $contact->load(['ID = ?', $order['distribution_id']]);

        $this->buildOrderInfo($f3, $order, $contact);
        $this->buildExpInventoryHead($f3, $order, $contact);
        $this->buildExpInventoryList($f3, $order, $model);

        $f3->set('guid', Uuid::uuid1());
        $f3->set('expOrderInfo', $this->expOrderInfo);
        $f3->set('expInventoryHead', $this->expInventoryHead);
        $f3->set('expInventoryList', $this->expInventoryList);
        $f3->set('baseTransfer', $this->baseTransfer);
        $f3->set('baseSubscribe', $this->baseSubscribe);
        $f3->set('signature', $this->signature);
        $f3->set('extendMessage', $this->extendMessage);
        $xml = \Template::instance()->render('declare-template.xml');

        $mapper = new Mapper(Database::mysql(), 'export');
        $mapper['guid'] = $f3->get('guid');
        $mapper['xml'] = $xml;
        $mapper->save();

        return $xml;
    }

    function buildOrderInfo($f3, $order, $contact)
    {
        $this->expOrderInfo['orderNo'] = $order['trace_id'];
        $this->expOrderInfo['payTime'] = date('YmdHis', strtotime($order['create_time']));
        $this->expOrderInfo['goodsNum'] = 1;
        $this->expOrderInfo['currency'] = 142; // 人民币
        $this->expOrderInfo['rate'] = 1;
        $this->expOrderInfo['orderTotalAmount'] = $order['price'];
        $this->expOrderInfo['consignee'] = $contact['name'];
        $this->expOrderInfo['consigneeCountry'] = $f3->get('cc');
        $this->expOrderInfo['consigneeAddress'] = $contact['address'] . ' ' . $contact['city'] . ' ' . $contact['state'];
        $this->expOrderInfo['consigneeTel'] = $contact['phone'];
        $this->expOrderInfo['consigneeEmail'] = $contact['email'];
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

    function buildExpInventoryHead($f3, $order, $contact) {
        $this->expInventoryHead['orderNo'] = $order['trace_id'];
        $this->expInventoryHead['ebpCode'] = $f3->get('EBPCODE');
        $this->expInventoryHead['ebpName'] = $f3->get('EBPNAME');
        $this->expInventoryHead['ebcCode'] = $f3->get('EBCCODE');
        $this->expInventoryHead['ebcName'] = $f3->get('EBCNAME');
        $this->expInventoryHead['productCode'] = '9151010032742290X5';
        $this->expInventoryHead['productName'] = '成都欧魅时尚科技有限责任公司';
        $this->expInventoryHead['logisticsCode'] = '510198Z006';
        $this->expInventoryHead['logisticsName'] = '中国邮政速递物流股份有限公司';
        $this->expInventoryHead['logisticsNo'] = $contact['distribution_number'];
        $this->expInventoryHead['preNo'] = '';
        $this->expInventoryHead['invtNo'] = '';
        $this->expInventoryHead['ieFlag'] = 'E';
        $this->expInventoryHead['declTime'] = date('YmdHis');
        $this->expInventoryHead['customsCode'] = 7902;
        $this->expInventoryHead['portCode'] = 7902;
        $this->expInventoryHead['ieDate'] = date('Ymd');
        $this->expInventoryHead['agentCode'] = '9151010032742290X5';
        $this->expInventoryHead['agentName'] = '成都欧魅时尚科技有限责任公司';
        $this->expInventoryHead['areaCode'] = '';
        $this->expInventoryHead['areaName'] = '';
        $this->expInventoryHead['tradeMode'] = 9610;
        $this->expInventoryHead['trafMode'] = 5;
        $this->expInventoryHead['trafNo'] = '';
        $this->expInventoryHead['voyageNo'] = '';
        $this->expInventoryHead['billNo'] = '';
        $this->expInventoryHead['loctNo'] = '';
        $this->expInventoryHead['packageNum'] = '';
        $this->expInventoryHead['licenseNo'] = '';
        $this->expInventoryHead['arrivedPort'] = $f3->get('ap');
        $this->expInventoryHead['country'] = $f3->get('cc');
        $this->expInventoryHead['freight'] = 0;
        $this->expInventoryHead['feeCurrency'] = 142;
        $this->expInventoryHead['feeFlag'] = 3;
        $this->expInventoryHead['insuredFee'] = 0;
        $this->expInventoryHead['inrCurrency'] = 142;
        $this->expInventoryHead['inrFlag'] = 3;
        $this->expInventoryHead['wrapType'] = 2;
        $this->expInventoryHead['packNo'] = 1;
        $this->expInventoryHead['grossWeight'] = 1;
        $this->expInventoryHead['netWeight'] = 1;
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

    function buildExpInventoryList($f3, $order, $model) {
        $this->expInventoryList['gnum'] = '{{@serial}}'; // set when report
        $this->expInventoryList['itemNo'] = '';
        $this->expInventoryList['gcode'] = 6402990000;
        $this->expInventoryList['gname'] = 'PU女鞋';
        $this->expInventoryList['gmodel'] = $model['model'];
        $this->expInventoryList['barCode'] = $order['trace_id'];
        $this->expInventoryList['country'] = $f3->get('cc');
        $this->expInventoryList['currency'] = 142;
        $this->expInventoryList['qty'] = 1;
        $this->expInventoryList['qty1'] = 1;
        $this->expInventoryList['qty2'] = '';
        $this->expInventoryList['unit'] = 011;
        $this->expInventoryList['unit1'] = 011;
        $this->expInventoryList['unit2'] = '';
        $this->expInventoryList['price'] = $order['price'];
        $this->expInventoryList['totalPrice'] = $order['price'];
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
