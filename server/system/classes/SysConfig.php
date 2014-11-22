<?php

class SysConfig {

	static public function getOrderConfig() {
		$sql = " SELECT code,value " . " FROM edo_cp_shop_config WHERE code='order_interval' OR code='order_max_start_time' OR code='order_max_time'  OR code='order_book_time' OR code='order_pay_timeout' ";

		$db = Frm::getInstance() -> getDb();
		$ret = $db -> getAll($sql);
		$shopConfig = array();
		foreach ($ret as $v) {

			if ($v['code'] == 'order_interval') {
				$shopConfig['order_interval'] = $v['value'];
			} elseif ($v['code'] == 'order_max_start_time') {
				$shopConfig['order_max_start_time'] = $v['value'];
			} elseif ($v['code'] == 'order_max_time') {
				$shopConfig['order_max_time'] = $v['value'];
			} elseif ($v['code'] == 'order_book_time') {
				$shopConfig['order_book_time'] = $v['value'];
			} else if ($v['code'] == 'order_pay_timeout') {
				$shopConfig['order_pay_timeout'] = $v['value'];
			}
		}
		return $shopConfig;
	}

	static public function getValue($param) {
		$sql = "SELECT value FROM edo_cp_shop_config WHERE code = '$param'";
		$db = Frm::getInstance() -> getDb();
		return $db -> getOne($sql);
	}

}
