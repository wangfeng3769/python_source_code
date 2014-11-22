<?php
/*
* @Title 易宝支付EPOS范例
* @Description 属性文件，可配置商户编号，密钥和测试，正式地址
* @V3.0
* @Author  wenhua.cheng
*/
class EPosConfig {
	
	// 日志文件路径
	static public $logName='YeePay_EPOS.log';

	// 业务类型
	static public $p0_Cmd='EposSale';

	// 交易币种
	static public $p4_Cur='CNY';

	// 是否需要应答
	static public $pr_NeedResponse=1;


	// 商户编号
	static public $p1_MerId='10011846597';

	// 商户密钥,用于生成hmac(hmac的说明详见文档)key为测试
	static public $merchantKey='pr0J15ThJ1j135N2iI4WYXT98SW0N76B6G8539wmy7887qq04IR2l696t658';



	// 正式地址
	static public $actionURL='https://www.yeepay.com/app-merchant-proxy/command.action';

	// 测试地址
	//$actionURL='http://tech.yeepay.com:8080/robot/debug.action';
}

?> 