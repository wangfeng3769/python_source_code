<?php
define('IN_ECS', true);

if (!defined('ROOT_PATH')) {
	require_once (dirname(__FILE__) . '/../../cp/includes/init.php');
}

class LoginManager {

	function createToken($password) {
		global $db;
		global $sns;
		$length = 32;

		$str1 = uniqid();
		$str2 = substr($password, 0, ($length - strlen($str1)));

		return md5($str1 . $str2);
	}

	function saveToken($userID, $password) {
		global $db;
		global $sns;

		$token = LoginManager::createToken($password);
		// $userID = UserManager::getLoginUserId();
		while (LoginManager::hasTokenInDB($token)) {
			$token = LoginManager::createToken($password);
		}
		$db -> autoExecute($sns -> table('user'), array('token' => $token, 'token_expire' => time() + 24 * 3600 * 30), 'update', "uid=" . $userID);

		return $token;
	}

	function hasTokenInDB($token) {
		global $db;
		global $sns;

		$sql = " SELECT COUNT(1) " . " FROM " . $sns -> table('user') . " WHERE token='$token'";
		$tokenNum = $db -> getOne($sql);
		if ($tokenNum > 0) {
			return true;

		}

		return false;
	}

	static public function loginWithUserName($userName, $password) {
		global $db;
		global $sns;

		$sql = "SELECT uid , phone, token,true_name  FROM " . $sns -> table('user') . " WHERE phone = '$userName' AND password = '$password'";
		$ret = $db -> getRow($sql);
		if (empty($ret)) {
			throw new Exception('', 4001);
		}

		$token = LoginManager::saveToken($ret['uid'], $password);
		$ret['token'] = $token;

		return $ret;
	}

	static public function loginWithToken($token) {
		global $db;
		global $sns;

		$now = time();
		$sql = " SELECT uid , phone, token ,token_expire,true_name" . " FROM " . $sns -> table('user') . " WHERE token='$token' AND token_expire > $now";
		$ret = $db -> getRow($sql);

		//判断token是否存在
		if (empty($ret)) {
			throw new Exception('', 4020);
		}

		if (self::isExpired($ret['token_expire'])) {
			throw new Exception('', 4060);
		}
		return $ret;
	}

	static public function isExpired($token_expire) {
		if ($token_expire < time()) {

			return true;
		} else {

			return false;
		}

	}

}
