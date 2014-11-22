<?php

//查询接口示例

require_once('../quickpay_service.php');

/*
返回值信息
0:成功
1:失败
2:超时
3:不存在
*/

function query($dTransType, $strOrdNumber, $strOrdTime)
{
	//需要填入的部分
	$param['transType']     = $dTransType;   //交易类型
	$param['orderNumber']   = $strOrdNumber; //订单号
	$param['orderTime']     = $strOrdTime;   //订单时间
	
	$nRet = 3;
	
	//提交查询
	$query  = new quickpay_service($param, quickpay_conf::QUERY);
	$ret    = $query->post();
	
	//返回查询结果
	$response = new quickpay_service($ret, quickpay_conf::RESPONSE);
	if ($response->get('respCode') != quickpay_service::RESP_SUCCESS) { //错误处理
	    $err = sprintf("Error: %d => %s", $response->get('respCode'), $response->get('respMsg'));
	    throw new Exception($err);
	}
	
	//后续处理
	$arr_ret = $response->get_args();
	echo "查询请求返回：\n" .  var_export($arr_ret, true);
	
	$queryResult = $arr_ret['queryResult'];
	
	//更新数据库
	if ($queryResult == quickpay_service::QUERY_SUCCESS) {
//	    echo "操作成功";
		$nRet = 0;
	}
	else if ($queryResult == quickpay_service::QUERY_FAIL) {
//	    echo "操作失败";
		$nRet = 1;
	}
	else if ($queryResult == quickpay_service::QUERY_WAIT) {
//	    echo "过一段时间再查吧";
		$nRet = 2;
	}
/*	else {
	    echo "交易不存在";
	}
*/
	return $nRet;
}
?>
