<?php
set_time_limit(0);
if (!defined('ROOT_PATH')) {
	define('IN_ECS', true);
	require (dirname(__FILE__) . '/../../cp/includes/init.php');
}

class MsgBox {

	public function MsgBox() {
	}

	public function sendMsg($sender, $receiver, $msg) {
		global $db, $sns;

		$now = time();
		$sql = "INSERT INTO " . $sns -> table("message") . " ( from_uid , to_uid , content , ctime ) VALUES ( $sender , $receiver , '$msg' , $now)";
		$ret = $db -> query($sql);
	}

}
