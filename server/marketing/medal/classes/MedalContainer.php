<?php

class MedalContainer {

	/**
	 * 取得用户已经领取的奖章
	 */
	public function getUserGainedMedalArr($userId) {
		require_once (Frm::$ROOT_PATH . 'marketing/medal/classes/MedalDesc.php');

		$sql = "SELECT m.id,m.name,um.medal_id,um.status,m.medaldesc,m.icon,um.time,m.file,m.class_name
			 FROM edo_cp_medal m 
			 LEFT JOIN edo_cp_user2medal um ON m.id = um.medal_id
			 WHERE um.user_id ={$userId} 
			 AND um.status = " . MedalDesc::$STATUS_GAINED;
		$db = Frm::getInstance() -> getDb();
		$res = $db -> getAll($sql);
		if (empty($res)) {
			return array();
		}

		$descArr = array();
		foreach ($res as $row) {
			$descArr[] = $this -> generateMedalDesc($row);
		}

		return $descArr;
	}

	public function getUserMedalArr($userId) {
		require_once (Frm::$ROOT_PATH . 'marketing/medal/classes/MedalDesc.php');

		$sql = "SELECT m.id,m.name,um.medal_id,um.status,m.medaldesc,m.icon,um.time,m.file,m.class_name 
			FROM edo_cp_medal m LEFT JOIN edo_cp_user2medal um ON m.id = um.medal_id 
			WHERE um.user_id ={$userId} ORDER BY um.status ASC , um.time DESC";
		$db = Frm::getInstance() -> getDb();
		$res = $db -> getAll($sql);
		if (empty($res)) {
			return array();
		}

		$descArr = array();
		foreach ($res as $row) {
			$descArr[] = $this -> generateMedalDesc($row);
		}

		return $descArr;
	}

	protected function generateMedalDesc($row) {
		require_once (Frm::$ROOT_PATH . 'marketing/medal/classes/MedalDesc.php');
		$medalDesc = new MedalDesc();
		$medalDesc -> id = $row['id'];
		$medalDesc -> name = $row['name'];
		$medalDesc -> icon = $row['icon'];
		$medalDesc -> desc = $row['medaldesc'];
		$medalDesc -> status = $row['status'];

		include_once ( Frm::$ROOT_PATH . $row['file']);
		$obj = new $row['class_name'];
		$medalDesc -> validDate = date('Y-m-d', strtotime($row['time']) + $obj -> expirenLife);

		return $medalDesc;
	}

	//领取奖章
	public function gainMedal($userId, $medalId) {
		require_once (Frm::$ROOT_PATH . 'marketing/medal/classes/MedalDesc.php');

		$sql = "UPDATE edo_cp_user2medal SET status= " . MedalDesc::$STATUS_GAINED . " WHERE medal_id={$medalId}  AND user_id={$userId}";
		$db = Frm::getInstance() -> getDb();
		$gainMedalRow = $db -> execSql($sql);
	}

}
