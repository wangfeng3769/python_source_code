<?php

class MemberAuth {

	public function __construct() {
		require_once (Frm::$ROOT_PATH . 'user/classes/UserManager.php');
		if (!UserManager::isLogined()) {
			if (!empty($_REQUEST['access_token'])) {
				$um=new UserManager();
				$ret=$um->login('','',$_REQUEST['access_token']);
				if (empty($ret['accessToken'])) {
					throw new Exception('', 4060);
				}
			}
			else
			{
				throw new Exception('', 4060);	
			}
		}
	}

}
?>