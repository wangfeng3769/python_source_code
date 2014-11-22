<?php

class CardManager {

	public function __construct() {
	}

	public function getCardUser($cardId) {
		$sql = "SELECT user_id FROM edo_cp_vip_card c LEFT JOIN edo_cp_vip_card_issue i ON c.id = i.vip_card_id 
			WHERE c.card_number = '$cardId'";
		$db = Frm::getInstance() -> getDb();
		$userId = $db -> getOne($sql);

		if (empty($userId)) {
			$userId = $cardId;
		}

		require_once (Frm::$ROOT_PATH . 'user/classes/UserManager.php');
		$um = new UserManager();
		return $um -> getUser($userId);
	}

}
?>