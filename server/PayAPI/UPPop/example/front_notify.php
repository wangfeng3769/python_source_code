<?php

require_once('../quickpay_service.php');

$strWebURL = "http://www.eduoauto.com";

try {
    $response = new quickpay_service($_POST, quickpay_conf::RESPONSE);
    if ($response->get('respCode') != quickpay_service::RESP_SUCCESS) {
        $err = sprintf("Error: %d => %s", $response->get('respCode'), $response->get('respMsg'));
        throw new Exception($err);
    }
    $arr_ret = $response->get_args(); 
    //告诉用户交易完成
    $strNotify = $_REQUEST['notify'];
    $strNotifyURL = $strWebURL. "/". $strNotify. 
    								"?qid=". $arr_ret['qid']. "&orderNumber=". $arr_ret['orderNumber']. 
    								"&orderAmount=". $arr_ret['orderAmount']. 
    								"&transType=". $arr_ret['transType'];
    echo file_get_contents("http://$strNotifyURL");	
    die();						
		Header("HTTP/1.1 303 See Other"); 
		Header("Location: $strNotifyURL"); 

}
catch(Exception $exp) {
    $str .= var_export($exp, true);
    die("error happend: " . $str);
}

?>
