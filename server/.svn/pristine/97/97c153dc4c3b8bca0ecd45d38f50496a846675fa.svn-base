<?php

//查询接口示例

require_once('../quickpay_service.php');

//需要填入的部分
$param['transType']     = $transType;   //交易类型
$param['orderNumber']   = $orderNumber; //订单号
$param['orderTime']     = $orderTime;   //订单时间

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
    echo "操作成功";
}
else if ($queryResult == quickpay_service::QUERY_FAIL) {
    echo "操作失败";
}
else if ($queryResult == quickpay_service::QUERY_WAIT) {
    echo "过一段时间再查吧";
}
else {
    echo "交易不存在";
}

?>
