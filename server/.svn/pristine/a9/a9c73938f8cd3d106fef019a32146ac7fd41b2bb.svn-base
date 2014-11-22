<?php
class MonitorAuth {
	public function __construct() {
		require_once (Frm::$ROOT_PATH . 'user/classes/UserManager.php');
		if (!UserManager::isLogined())
		{
			header('Location: /tianwu/monitor/tw_public.php?item=tw_monitorLoginPage');
		} 
		else {
			$moniterArr = array('13910398687','13778233766','18612254429','13502092929');
			if (!in_array($_SESSION['phone'], $moniterArr))
			{
				echo '<meta http-equiv="Content-Type" content="text/html; charset=utf8">';
				echo '您无权监控!';
				exit ;
			}
		}
	}

}
?>