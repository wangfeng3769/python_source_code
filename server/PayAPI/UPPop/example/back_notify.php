<?php

require_once('../quickpay_service.php');

try {
    $response = new quickpay_service($_POST, quickpay_conf::RESPONSE);
    if ($response->get('respCode') != quickpay_service::RESP_SUCCESS) {
        $err = sprintf("Error: %d => %s", $response->get('respCode'), $response->get('respMsg'));
        throw new Exception($err);
    }

    $arr_ret = $response->get_args();
    
    $strNotify = $_REQUEST['notify'];
    $strNotifyURL = $strWebURL. "/". $strNotify. 
    								"?qid=". $arr_ret['qid']. "&orderNumber=". $arr_ret['orderNumber']. 
    								"&orderAmount=". $arr_ret['orderAmount']. 
    								"&transType=". $arr_ret['transType'];

    //更新数据库，将交易状态设置为已付款
    //注意保存qid，以便调用后台接口进行退货/消费撤销

    require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
    $om = new OrderManager();
    //orderNo处理方式未定
    $order = $om -> getOrder($orderId);
    require_once (Frm::$ROOT_PATH . 'order_charge/classes/OrderCharge.php');
    $orderCharge = new OrderCharge();
    try {
        $orderCharge -> orderPayment($orderId, $arr_ret['orderAmount'], $arr_ret['qid'], OrderCharge::$PAYMENT_TYPE_UPWEB);
    } catch (Exception $e) {
        //修改状态出错,退钱到银联账户
        
    }
    

    
    echo file_get_contents("http://$strNotifyURL"); 
    die();  
		Header("HTTP/1.1 303 See Other"); 
		Header("Location: $strNotifyURL"); 



    //以下仅用于测试
    file_put_contents('notify.txt', var_export($arr_ret, true));

}
catch(Exception $exp) {
    //后台通知出错
    file_put_contents('notify.txt', var_export($exp, true));
}

?>
