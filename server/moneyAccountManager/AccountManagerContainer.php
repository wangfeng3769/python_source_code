<?php
require_once (dirname(__FILE__) . '/../hfrm/Frm.php');
require_once (Frm::$ROOT_PATH . 'moneyAccountManager/AccountManager.php');
require_once (Frm::$ROOT_PATH . 'moneyAccountManager/CashAccountManager.php');

class AccountManagerContainer {
	private static $_instance = null;

	private function __construct() {
	}

	public static function getInstance() {
		if (self::$_instance == null) {
			self::$_instance = new AccountManagerContainer();
		}

		return self::$_instance;
	}

	public function getAccountManager($obj) {
		$manager = null;
		switch ($obj) {
			case 'eduo' :
				$manager = new CashAccountManager();
				break;

			default :
				break;
		}

		return $manager;
	}

}

// $className = 'CashAccountManager';

// $a=AccountManagerContainer::getInstance();
// $a = $a->getAccountManager($className);

// $a->balanceInquiry();
