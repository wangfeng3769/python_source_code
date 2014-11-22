<?php

//ini_set('display_errors',        1);
error_reporting(E_ALL);
//echo $_GET['detail_data']."<BR>";
$agent = 'C4335319945672464113';
$price = '0.01';

$parameter = array(
	'agent'             => $agent,
	'service'           => "refund_fastpay_by_platform_nopwd",
	'partner'           => "2088201952134393",
	//'partner'         => ALIPAY_ID,
	'_input_charset'    => "utf-8",
    'notify_url'        => "http://www.eduoauto.com/cp/respond.php?code=alipay",
	/* 业务参数 */
	'refune_date' => date("Y-m-d H:i:s") ,
	'batch_no' => '2012090531963' ,
	'batch_num' => 1,
	'subject'           => '2012090531963',
	'out_trade_no'      => '20120905319631',
	'price'             => $price,
	'quantity'          => 1,
	'payment_type'      => 1,
	/* 物流参数 */
	'logistics_type'    => 'EXPRESS',
	'logistics_fee'     => 0,
	'logistics_payment' => 'BUYER_PAY_AFTER_RECEIVE',
	/* 买卖双方信息 */
	'seller_email'      => "kevin.wang@edoauto.com"
);

echo '<a href="https://www.alipay.com/cooperate/gateway.do?'.$param. '&sign='.md5($sign).'&sign_type=MD5" target="blank">支付</a><BR>' ;

////////冻解
$parameter = array(
	'service'           => "bank2freeze",
	'partner'           => "2088201952134393",
	'_input_charset'    => "utf-8",
    'notify_url'        => "http://www.eduoauto.com/alipayResponse.php?code=freeze",
    'return_url'        => "http://www.eduoauto.com/alipayReturn.php?code=freeze",
	'out_order_no'      => '1238',
	'out_order_dt'      => date("Y-m-d H:i:s"),
	'subject'      => '租车',
	'memo'      => '订车保证金',
	'seller_email'      => 'kevin.wang@edoauto.com',
	'buyer_email'      => 'sdfw@jf.com',
	'amount' => '0.01',
	'op_type' => '0008',   //业务保证金,
	'order_from' => '100002'
);

ksort($parameter);
reset($parameter);

$param = '';
$sign  = '';

foreach ($parameter AS $key => $val)
{
	$param .= "$key=" .urlencode($val). "&" ;
	$sign  .= "$key=$val&" ;
}

$param = substr( $param, 0, -1) ;
$sign  = substr( $sign, 0, -1). "iajbv0fwt4hzubq0gr0tzka5jljgl4xf" ;

echo '<a href="https://www.alipay.com/cooperate/gateway.do?'.$param. '&sign='.md5($sign).'&sign_type=MD5" target="blank">支付保证金</a><Br/>' ;


//解冻:
$parameter = array(
	'_input_charset' => 'utf-8', 
    'amount' => '0.01', 
    'gmt_out_order_create' => date("Y-m-d H:i:s"), 
    'old_out_order_no' => '1235' ,
    'op_type' => '0008' , 
    'out_order_no' => '1235' , 
    'out_trade_no' => '223' , 
    'partner' => '2088201952134393' ,
    'service' => 'unfreeze_trade_fee' ,
    'subject' => '解冻订单号为:810005614的交易' ,
    'user_email' => 'zhou.tack@gmail.com'
);

ksort($parameter);
reset($parameter);

$param = '';
$sign  = '';

foreach ($parameter AS $key => $val)
{
	$param .= "$key=" .urlencode($val). "&" ;
	$sign  .= "$key=$val&" ;
}

$param = substr( $param, 0, -1 ) ;
$sign  = substr( $sign, 0, -1 ). "iajbv0fwt4hzubq0gr0tzka5jljgl4xf" ;

echo '<a href="https://www.alipay.com/cooperate/gateway.do?'.$param. '&sign='.md5($sign).'&sign_type=MD5" target="blank">退还保证金</a><BR />' ;


//分润接口
$parameter = array(
    '_input_charset' => 'utf-8' ,
    'buyer_nick' => '周小卫' ,
    'old_out_order_no' => '1236' ,
    'op_type' => '0008' ,
    'order_from' => '100002' ,
    'other_fee' => '0.00' ,
    'out_order_dt' => date( "Y-m-d H:i:s" ) ,
    'out_order_no' => 'B02339' ,
    'out_trade_no' => '3434' ,
    'partner' => '2088201952134393' ,
    'price' => '1.00' ,
    'royalty_parameters' => 'zhou.tack@gmail.com^0.99^易多订车退款' ,
    'royalty_type' => '10' ,
    'seller_nick' => '易多' ,
    'service' => 'unfreeze2fastpay' ,
    'totle_fee' => '1.00' 
) ;

ksort( $parameter ) ;
reset( $parameter ) ;

$param = '' ;
$sign  = '' ;

foreach ($parameter AS $key => $val)
{
	$param .= "$key=" .urlencode($val). "&" ;
	$sign  .= "$key=$val&" ;
}

$param = substr( $param, 0, -1 ) ;
$sign  = substr( $sign, 0, -1 ). "iajbv0fwt4hzubq0gr0tzka5jljgl4xf" ;

echo '<a href="https://www.alipay.com/cooperate/gateway.do?'.$param. '&sign='.md5($sign).'&sign_type=MD5" target="blank">解冻分润</a><BR />' ;

if( $_GET )
{
	print_r( $_GET ) ;
}

?>