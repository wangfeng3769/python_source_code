<?php

class IdentificationManager {

	static public $STATUS_ID_PASS = 0x01;
	static public $STATUS_OTHERS_PASS = 0x02;

	public function __construct() {
	}

	/**
	 * 是否已经上传了实名审核资料
	 * @return boolean [description]
	 */
	function hasUploadApproveData($userID) {
		$sql = " SELECT 1 FROM edo_cp_identity_approve WHERE user_id=" . $userID;
		$db = Frm::getInstance() -> getDb();
		$ret = $db -> getOne($sql);

		return !empty($ret);
	}

	/**
	 * 是否通过审核
	 * @return boolean [description]
	 */
	public function isPassApprove($userID) {
		$approve_stat = $this -> getApproveStat($userID);

		if (self::$STATUS_ID_PASS & $approve_stat && self::$STATUS_OTHERS_PASS & $approve_stat) {
			return true;
		}

		return false;
	}

	public function getIDInfo($userId) {
		$sql = "SELECT id , approve_stat , identity_num FROM edo_cp_identity_approve WHERE user_id = $userId";
		$db = Frm::getInstance() -> getDb();
		$row = $db -> getRow($sql);

		return $row;
	}

	/**
	 * 获取审核状态
	 * @return [type] [description]
	 */
	protected function getApproveStat($userID) {
		$sql = " SELECT approve_stat FROM edo_cp_identity_approve WHERE user_id=" . $userID;
		$db = Frm::getInstance() -> getDb();
		$approve_stat = $db -> getOne($sql);

		return $approve_stat;
	}

}
?>