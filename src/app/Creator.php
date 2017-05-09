<?php

namespace app;

use data\Database;
use data\OrderDB;
use DB\SQL\Mapper;
use Httpful\Mime;
use Ramsey\Uuid\Uuid;

class Creator
{
    function create($f3)
    {
        $odb = OrderDB::mysql();

        $order = new Mapper($odb, 'order_item');
        $order->load(['trace_id = ?', $_REQUEST['id']]);

        if ($order->dry()) {
            goto NOT_FOUND;
        }

        $model = new Mapper($odb, 'prototype');
        $model->load(['ID = ?', $order['prototype_id']]);

        $contact = new Mapper($odb, 'distribution');
        $contact->load(['ID = ?', $order['distribution_id']]);

        $this->buildOrderInfo($f3, $order, $model, $contact);

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

        header('Content-Type:application/xml');
        echo $xml;

        /*$response = \Httpful\Request::post($f3->get('DECLARE_URL'))
            ->body($xml)
            ->sendsType(Mime::FORM)
            ->send();
        var_dump($response);*/

        NOT_FOUND:
        return header('HTTP/1.1 404 Not Found');
    }

    function buildOrderInfo($f3, $order, $model, $contact)
    {
        $this->expOrderInfo['orderNo'] = $order['trace_id'];
        $this->expOrderInfo['payTime'] = date('YmdHis', strtotime($order['create_time']));
        $this->expOrderInfo['goodsNum'] = 1;
        $this->expOrderInfo['currency'] = 142; // 人民币
        $this->expOrderInfo['rate'] = 1;
        $this->expOrderInfo['orderTotalAmount'] = $order['price'] ? $order['price'] : $model['cost'];
        $this->expOrderInfo['consignee'] = $contact['name'];
        $this->expOrderInfo['consigneeCountry'] = $contact['country'];
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
        'copCode' => '',
        'copName' => '',
        'dxpMode' => '',
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
