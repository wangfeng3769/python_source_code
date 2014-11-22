<?php

class LoginManager {

	function createToken($password) {
		$length = 32;

		$str1 = uniqid();
		$str2 = substr($password, 0, ($length - strlen($str1)));

		return md5($str1 . $str2);
	}

	function saveToken($userID, $password) {
		$token = LoginManager::createToken($password);

		$sql = "UPDATE edo_user SET token = '$token' , token_expire = " . (time() + 24 * 3600 * 30) . " WHERE uid = $userID";
		$db = Frm::getInstance() -> getDb();
		$db -> execSql($sql);

		return $token;
	}

	static public function loginWithUserName($userName, $password) {
		$sql = "SELECT uid , phone, token , need_modify_password,true_name  FROM edo_user WHERE phone = '$userName' AND password = '$password'";
		$db = Frm::getInstance() -> getDb();
		$ret = $db -> getRow($sql);
		if (empty($ret)) {
			throw new Exception('', 4001);
		}

		$token = $ret['token'];
		// if (empty($token)) {
		$token = LoginManager::saveToken($ret['uid'], $password);
		$ret['token'] = $token;
		// }

		return $ret;
	}

	static public function loginWithToken($token) {
		$now = time();
		$sql = " SELECT uid , phone, token ,token_expire , need_modify_password,true_name FROM edo_user WHERE token='$token' AND token_expire > $now";
		$db = Frm::getInstance() -> getDb();
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
