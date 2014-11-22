<?php

class HolidayManager {

	static public $WORKDAY = 0;
	static public $HOLIDAY = 1;

	public function getHolidayArr($tStartTime, $tEndTime) {
		$sd = date('Y-m-d', $tStartTime);
		$ed = date('Y-m-d', $tEndTime);

		$db = Frm::getInstance() -> getDb();
		$sql = "SELECT holiday FROM edo_cp_vacation WHERE holiday >= '$sd' AND holiday <= '$ed' ";
		$ret = $db -> getAll($sql);

		$arr = array();
		for ($i = 0; $i < count($ret); ++$i) {
			$arr[] = $ret[$i]['holiday'];
		}

		return $arr;
	}

}
