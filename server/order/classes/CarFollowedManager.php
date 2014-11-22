<?php
class CarFollowedManager {

	public function __construct() {

	}

	public function follow($userId, $carId) {
		if ($this -> isFollowed($userId, $carId)) {
			return;
		}

		$sql = "INSERT INTO edo_car_follow ( user_id , car_id ) VALUES ( $userId , $carId )";
		$db = Frm::getInstance() -> getDb();
		$db -> execSql($sql);
	}

	public function getCarArrFollowStatus($carIdArr, $userId) {
		if (empty($carIdArr)) {
			throw new Exception('', 4000);
		}
		if ($userId <= 0) {
			throw new Exception('', 4000);
		}

		$sqlCarId = implode(',', $carIdArr);
		$sql = "SELECT f.car_id
			FROM edo_car_follow f 
			WHERE f.user_id = $userId AND car_id IN ($sqlCarId)";

		$db = Frm::getInstance() -> getDb();
		$ret = $db -> getAll($sql);

		$followArr = array();
		foreach ($ret as $row) {
			$followArr[$row['car_id']] = 1;
		}

		return $followArr;
	}

	public function getFollowedCarIdList($userId) {
		if ($userId <= 0) {
			throw new Exception('', 4000);
		}

		$sql = "SELECT f.car_id
			FROM edo_car_follow f 
			WHERE f.user_id = $userId";

		$db = Frm::getInstance() -> getDb();
		$ret = $db -> getAll($sql);

		$idArr = array();
		foreach ($ret as $row) {
			$idArr[] = $row['car_id'];
		}

		return $idArr;
	}

	protected function isFollowed($userId, $carId) {
		$sql = "SELECT 1 FROM edo_car_follow WHERE user_id = $userId AND car_id = $carId";
		$db = Frm::getInstance() -> getDb();
		$ret = $db -> getOne($sql);
		if (empty($ret)) {
			return false;
		}

		return true;
	}

}
?>