<?php

//前台支付接口示例

require_once('../quickpay_service.php');

/*
参数定义如下：
$strOrdNumber：订单的ID号，string类型，例如："2013520148963174"
$strOrdTime：订单的时间，string类型，格式为YYYYmmhhddHHMMSS，例如："20130114153919"
$dOrdAmount：订单金额，int类型，数值表示为金额*100，例如：100.50元，输入格式应为10050
$strFNUrl：前台回调页面参数，具体为URL，此URL作为回调后调用的地址，地址为相对地址，例如："PayAPI/UPPop/web/Notify.php"
$strBNUrl：前台回调页面参数，具体为URL，此URL作为回调后调用的地址，地址为相对地址，例如："PayAPI/UPPop/web/Notify.php"
*/

function Pay($strOrdNumber, $strOrdTime, $dOrdAmount, $strFNUrl, $strBNUrl, $TransType=quickpay_conf::CONSUME)
{
	$param['transType']             = $TransType;  		//交易类型，CONSUME or PRE_AUTH
	
	$param['orderAmount']           = $dOrdAmount; 		//交易金额
	$param['orderNumber']           = $strOrdNumber; 	//订单号，必须唯一
	$param['orderTime']             = $strOrdTime;   	//交易时间, YYYYmmhhddHHMMSS
	$param['orderCurrency']         = quickpay_conf::CURRENCY_CNY;  //交易币种，CURRENCY_CNY=>人民币
	
	$param['customerIp']            = $_SERVER['REMOTE_ADDR'];  //用户IP
	$param['frontEndUrl']           = "http://www.eduoauto.com/PayAPI/UPPop/web/front_notify.php?notify=".$strFNUrl;    //前台回调URL
	$param['backEndUrl']            = "http://www.eduoauto.com/PayAPI/UPPop/web/back_notify.php?notify=".$strBNUrl;			//后台回调URL
	
	/* 可填空字段
	   $param['commodityUrl']          = "http://www.example.com/product?name=商品";  //商品URL
	   $param['commodityName']         = '商品名称';   //商品名称
	   $param['commodityUnitPrice']    = 11000;        //商品单价
	   $param['commodityQuantity']     = 1;            //商品数量
	//*/
	
	//其余可填空的参数可以不填写
	$pay_service = new quickpay_service($param, quickpay_conf::FRONT_PAY);
	$html = $pay_service->create_html();
	
	header("Content-Type: text/html; charset=" . quickpay_conf::$pay_params['charset']);
	echo $html; //自动post表单
}
?>
