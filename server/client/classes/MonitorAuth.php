<?php
class MonitorAuth {
	public function __construct() {
		require_once (Frm::$ROOT_PATH . 'user/classes/UserManager.php');
		if (!UserManager::isLogined()) {
			header('Location: /client/http/public.php?item=monitorLoginPage');
		} else {
			$moniterArr = array('15606556731','13901350906', '18618213420', '18701434897', '13651314858', '13611222499', '15911087898', '13436356959', '13488739516', '15901059953', '13910398687', '15010509719', '13810287622','18612254429','18632295507','18501176560','15116278681','13552511281');
			if (!in_array($_SESSION['phone'], $moniterArr)) {
				echo '<meta http-equiv="Content-Type" content="text/html; charset=utf8">';
				echo '您无权监控!';
				exit ;
			}
		}
	}

}
?>