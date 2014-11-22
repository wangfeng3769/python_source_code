<?php

require_once dirname(__FILE__) . '/../../hfrm/Frm.php';
require_once Frm::$ROOT_PATH . 'client/classes/CarRecordAuth.php';

class carRecordClient extends carRecordAuth {

	public function __construct() {
		parent::__construct();
	}
	public function carRecordList($argv) {
		//echo "调用数据操作接口";
		require_once (Frm::$ROOT_PATH . 'client/classes/CarCmdRecord.php');
		$car_cmd_record = new car_cmd_record();
		$arr = $car_cmd_record->get_cmd_record();

		require_once Frm::$ROOT_PATH . 'cp/admin/templates/record.php';
		//echo "调用数据显示页面";
		exit ;
	}


}
