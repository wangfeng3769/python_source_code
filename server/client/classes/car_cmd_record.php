<?php
	class car_cmd_record{
		
		static public $MAX_UNDONE_COUNT = 2;
		static public $MAX_MODIFY_TIMES = 3;

		public function __construct() {

		}
		public function get_cmd_record(){
		$sql = "SELECT car_id,send_time,addon,state
				FROM  edo_cp_car_cmd_record 
				ORDER BY  edo_cp_car_cmd_record.send_time desc 
				";
		$db = Frm::getInstance() ->getDb();
		$ret = $db -> limitSelect($sql,0,29);
		if (empty($ret)) {
			return null;
		}
		return $ret;
		}
	}
	
?>