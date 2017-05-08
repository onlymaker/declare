<?php

namespace app;

use Ramsey\Uuid\Uuid;

class Creator
{
    function create($f3)
    {
        $f3->set('guid', Uuid::uuid1());
        $f3->set('expOrderInfo', $this->expOrderInfo);
        $f3->set('expInventoryHead', $this->expInventoryHead);
        $f3->set('expInventoryList', $this->expInventoryList);
        $f3->set('baseTransfer', $this->baseTransfer);
        $f3->set('baseSubscribe', $this->baseSubscribe);
        $f3->set('signature', $this->signature);
        $f3->set('extendMessage', $this->extendMessage);
        header('Content-Type:application/xml');
        echo \Template::instance()->render('declare-template.xml');
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
