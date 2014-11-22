<?php

class StationManager {

	public function __construct() {
	}

	public function getCarArrCityId($carIdArr) {
		$cityIdArr = array();

		if (empty($carIdArr)) {
			return $cityIdArr;
		}

		$sqlId = implode(',', $carIdArr);
		$sql = "SELECT c.id , s.city FROM edo_cp_station s LEFT JOIN edo_cp_car c ON s.id = c.station WHERE c.id IN ( $sqlId )";

		$db = Frm::getInstance() -> getDb();
		$ret = $db -> getAll($sql);
		if (empty($ret)) {
			return $cityIdArr;
		}

		foreach ($ret as $value) {
			$cityIdArr[$value['id']] = $value['city'];
		}

		return $cityIdArr;
	}

}
?>