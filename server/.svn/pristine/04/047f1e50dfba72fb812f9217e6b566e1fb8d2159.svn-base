<?php

class CashAccountAgent {

	public function __construct() {
	}

	public function onRefund($extId, $amount) {
		require_once (Frm::$ROOT_PATH . 'moneyAccountManager/CashAccountManager.php');
		$ca = new CashAccountManager();
		$ca -> moneyOperate(CashAccountManager::$MONEY_OP_TYPE_FREEZE_COMPLETE, -1, $extId, $amount);

		return array('rspCode' => '000', 'logId' => $extId);
	}

}
?>