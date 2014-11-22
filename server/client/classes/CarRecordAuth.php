<?php

class carRecordAuth {
	public function __construct() {
		require_once (Frm::$ROOT_PATH . 'user/classes/UserManager.php');

		if (!UserManager::isLogined()) {
			header('Location: /client/http/public.php?$item=CarRecordLoginPage');
		} else {
			$moniterArr = array('15606556731', '13910398687', '13488739516', '13436356959','13651314858');
			
			if (!in_array($_SESSION['phone'], $moniterArr)) {
				echo '<meta http-equiv="Content-Type" content="text/html; charset=utf8">';
				echo '您无权监控!';
				exit ;
			}
		}
	}

}
?>