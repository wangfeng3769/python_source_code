<?php

class DrivingLicenceManager {

	public function getDrivingLicenceExpire($userId) {
		$sql = " SELECT validfor FROM edo_user WHERE uid = " . $userId;
		$db = Frm::getInstance() -> getDb();
		return $db -> getOne($sql); 
	}

}
?>