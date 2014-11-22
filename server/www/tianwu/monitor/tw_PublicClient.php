<?php
class PublicClient
 {
    public function __construct() 
    {

	}
	public function tw_monitorLoginPage()
	{
		header('Location: /tianwu/monitor/tw_monitor_login.php');
		exit;
	}
	public function monitorLogin($argv)
	{
		require_once (Frm::$ROOT_PATH . 'user/classes/UserManager.php');
		$um = new UserManager();
		$loginRet = $um -> login($argv['user_name'], $argv['password'], '');
	
		header( 'Location: /tianwu/monitor/tw_monitor.php?item=carList' );
		exit;	
	}
}