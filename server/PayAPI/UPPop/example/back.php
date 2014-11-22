<?php

//后台接口示例

require_once('../quickpay_service.php');

/*
参数定义如下：
$strOrdNumber：订单的ID号，string类型，例如："2013520148963174"
$strOrdTime：订单的时间，string类型，格式为YYYYmmhhddHHMMSS，例如："20130114153919"
$dOrdAmount：订单金额，int类型，数值表示为金额*100，例如：100.50元，输入格式应为10050
$strFNUrl：前台回调页面参数，具体为URL，此URL作为回调后调用的地址，地址为相对地址，例如："PayAPI/UPPop/web/Notify.php"
$strBNUrl：前台回调页面参数，具体为URL，此URL作为回调后调用的地址，地址为相对地址，例如："PayAPI/UPPop/web/Notify.php"
*/

function Refund($strQID, $strOrdNumber, $strOrdTime, $dOrdAmount, $strFNUrl, $strBNUrl, $TransType=quickpay_conf::REFUND)
{
	//交易类型 退货=REFUND 或 消费撤销=CONSUME_VOID, 如果原始交易是PRE_AUTH，那么后台接口也支持对应的
	//  PRE_AUTH_VOID(预授权撤销), PRE_AUTH_COMPLETE(预授权完成), PRE_AUTH_VOID_COMPLETE(预授权完成撤销)
	$param['transType']             = $TransType;
	
	$param['origQid']								= $strQID; //原交易返回的qid, 从数据库中获取
	
	$param['orderAmount']           = $dOrdAmount;        //交易金额
	$param['orderNumber']           = $strOrdNumber; //订单号，必须唯一(不能与原交易相同)
	$param['orderTime']             = $strOrdTime;   //交易时间, YYYYmmhhddHHMMSS
	$param['orderCurrency']         = quickpay_conf::CURRENCY_CNY;  //交易币种，
	
	$param['customerIp']            = $_SERVER['REMOTE_ADDR'];  //用户IP
	$param['frontEndUrl']           = $strFNUrl;    //前台回调URL, 后台交易可为空
	$param['backEndUrl']            = "http://www.eduoauto.com/PayAPI/UPPop/web/back_notify.php?notify=".$strBNUrl;    //后台回调URL
	
	//其余可填空的参数可以不填写
	
	//提交
	$pay_service = new quickpay_service($param, quickpay_conf::BACK_PAY);
	$ret = $pay_service->post();
	
	//同步返回（表示服务器已收到后台接口请求）, 处理成功与否以后台通知为准；或使用主动查询
	$response = new quickpay_service($ret, quickpay_conf::RESPONSE);
	if ($response->get('respCode') != quickpay_service::RESP_SUCCESS) { //错误处理
	    $err = sprintf("Error: %d => %s", $response->get('respCode'), $response->get('respMsg'));
	    throw new Exception($err);
	}
	
	//后续处理
	$arr_ret = $response->get_args();
	
	echo "后台交易返回：\n" . var_export($arr_ret, true); //此行仅用于测试输出
}
?>
