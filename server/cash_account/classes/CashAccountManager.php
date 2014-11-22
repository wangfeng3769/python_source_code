<?php

class CashAccountManager {

	public function __construct() {

	}

	public function hasArrearage($userId) {
		$sql = "SELECT amount FROM edo_cash_account WHERE user_id = $userId";
		$db = Frm::getInstance() -> getDb();
		$amount = $db -> getOne($sql);

		return $amount < 0;
	}

	function create($userId, $amount = 0) {
		$sql = "INSERT INTO edo_cash_account ( user_id , amount , freeze_money ) VALUES ( $userId , $amount , 0 ) ";
		$db = Frm::getInstance() -> getDb();
		$db -> execSql($sql);
	}

}
