<?php

class OrderEvaluationManager {

	public function __construct() {
	}

	public function getEvaluation($orderNo) {
		$sql = "SELECT level , content FROM order_evaluation WHERE order_no = $orderNo";
		$db = Frm::getInstance() -> getDb();

		return $db -> getRow($sql);
	}

	public function addEvaluation($orderNo, $level, $content) {
		if (!is_numeric($orderNo) || !is_numeric($level)) {
			throw new Exception('', 4000);
		}
		$db = Frm::getInstance() -> getDb();

		$sql = "SELECT 1 FROM order_evaluation WHERE order_no = '$orderNo'";
		$ret = $db -> getOne($sql);

		$sqlContent = str_replace("\'", "\'\'", $content);
		if (empty($ret)) {
			$sql = "INSERT INTO order_evaluation ( order_no , level , content ) VALUES ( '$orderNo' , $level , '$sqlContent')";

			$db -> execSql($sql);
		} else {
			$sql = "UPDATE order_evaluation SET level = $level , content = '$sqlContent' WHERE order_no = '$orderNo'";

			$db -> execSql($sql);
		}
	}

}
