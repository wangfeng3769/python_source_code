<?php

class ShowOrderManager {

	public function __construct() {
	}

	public function getShowOrderContent($orderId) {
		if (!is_numeric($orderId)) {
			throw new Exception('', 4000);
		}

		$sql = " SELECT o.order_id ,u.uname,s.name as station_name,c.name as car_name,
			c.icon as car_icon ,o.order_start_time,o.order_end_time,o.order_real_cost,u.uid 
			FROM  edo_cp_car_order o LEFT JOIN edo_cp_car c ON c.id=o.car_id 
			LEFT JOIN edo_user u ON u.uid = o.user_id LEFT JOIN edo_cp_station s ON s.id = c.station 
			WHERE order_id = $orderId";
		$db = Frm::getInstance() -> getDb();
		$res = $db -> getRow($sql);

		require_once (Frm::$ROOT_PATH . 'openPlatForm/includes/OpenPlatManager.php');
		$opm = new OpenPlatManager();
		$openPlatInfo = $opm -> getOpenPlatInfo($res['uid']);
		$res['openPlatInfo'] = $openPlatInfo;

		$res['order_start_time'] = date('Y-m-d H:i', $res['order_start_time']);
		$res['order_end_time'] = date('Y-m-d H:i', $res['order_end_time']);

		return $res;
	}

}
?>
