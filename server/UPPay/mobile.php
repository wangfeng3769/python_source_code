<?php

$xml = file_get_contents("php://input");
//$xml = '<?xml version="1.0" encoding="UTF-8"?\><cupMobile application="UPNoCards" version="1.01"><transaction type="Purchase.PMReq"><submitTime>20121226164243</submitTime><merchant id="898110148994677"/><order id="10182"><generateTime>20121226164243</generateTime></order><settleDate>20121226</settleDate><transmitTime>1226164648</transmitTime><billAmount>000000000100</billAmount><accountNumber1>4392258101462267</accountNumber1><transSerialNumber>173506</transSerialNumber><terminal id="18989468"/><transAmount currency="156">000000000100</transAmount></transaction></cupMobile>';
$fp = fopen("/home/hoheart/yinliancallback.txt", "a+");
fwrite($fp, "\n$xml" . date('Y-m-d H:i:s') . '.' . microtime());


require_once( dirname( __FILE__ ) . '/../hfrm/Frm.php' );
require_once (Frm::$ROOT_PATH . 'PayAPI/upmobile/classes/UnionPayMobileManager.php');
$o = new UnionPayMobileManager();
$o -> callBackReq($xml);
fwrite($fp, "\n" . date('Y-m-d H:i:s') . '.' . microtime());
fclose($fp);
?>
