<?php

class ViolateManager {

	public function __construct() {

	}

	public function hasViolate($userId) {
		$sql = "SELECT 1 FROM edo_cp_car_violate WHERE user_id = $userId";
		$db = Frm::getInstance() -> getDb();
		$ret = $db -> getOne($sql);
		if (empty($ret)) {
			return false;
		}

		return true;
	}

}
?>