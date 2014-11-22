<?php

//��ѯ�ӿ�ʾ��

require_once('../quickpay_service.php');

//��Ҫ����Ĳ���
$param['transType']     = $transType;   //��������
$param['orderNumber']   = $orderNumber; //������
$param['orderTime']     = $orderTime;   //����ʱ��

//�ύ��ѯ
$query  = new quickpay_service($param, quickpay_conf::QUERY);
$ret    = $query->post();

//���ز�ѯ���
$response = new quickpay_service($ret, quickpay_conf::RESPONSE);
if ($response->get('respCode') != quickpay_service::RESP_SUCCESS) { //������
    $err = sprintf("Error: %d => %s", $response->get('respCode'), $response->get('respMsg'));
    throw new Exception($err);
}

//��������
$arr_ret = $response->get_args();
echo "��ѯ���󷵻أ�\n" .  var_export($arr_ret, true);

$queryResult = $arr_ret['queryResult'];

//�������ݿ�
if ($queryResult == quickpay_service::QUERY_SUCCESS) {
    echo "�����ɹ�";
}
else if ($queryResult == quickpay_service::QUERY_FAIL) {
    echo "����ʧ��";
}
else if ($queryResult == quickpay_service::QUERY_WAIT) {
    echo "��һ��ʱ���ٲ��";
}
else {
    echo "���ײ�����";
}

?>
