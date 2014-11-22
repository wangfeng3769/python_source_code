<?php

class Register {

	public function __construct() {
	}

	public function onSmsReceived($msg) {
		if ('zc' == $msg -> content) {
			$password = sprintf("%06d", rand(1, 999999));

			require_once (Frm::$ROOT_PATH . 'system/sms_platform/classes/SmsSender.php');
			$s = new SmsSender();

			try {
				$ret = $this -> register($msg -> sender, $password, true);
			} catch(Exception $e) {
				if ($e -> getCode() > 1000) {
					$s -> send($e -> getMessage(), $msg -> sender);
				} else {
					$s -> send('系统错误，请与客服联系。', $msg -> sender);
				}

				return;
			}

			require_once (Frm::$ROOT_PATH . 'system/classes/SysConfig.php');
			$fmt = SysConfig::getValue('sms_register_fmt');
			$smsContent = sprintf($fmt, $password);
			$s -> send($smsContent, $msg->sender);
		}
	}

	public function register($phonenum, $password, $needModifyPassword = false) {
		$db = Frm::getInstance() -> getDb();

		if (!empty($phonenum)) {
			$sql = "SELECT 1 FROM edo_user WHERE phone = '$phonenum'";
			$ret = $db -> getOne($sql);
			if (!empty($ret)) {
				throw new Exception("该手机号码已被使用", 4002);
			}
		}

		if (empty($password)) {
			$ep = "";
		} else {
			$ep = md5($password);
		}

		$sql = "SELECT version FROM user_protocal ";
		$db = Frm::getInstance() -> getDb();
		$userProtocalVersion = $db -> getOne($sql);

		$now = time();
		$nmp = 0;
		if ($needModifyPassword) {
			$nmp = 1;
		}
		$sql = "INSERT INTO edo_user ( phone , password , ctime , protocal_version , need_modify_password ) VALUES ( '$phonenum' , '$ep' , $now , $userProtocalVersion , $nmp )";

		$trans = $db -> startTransaction();

		$ret = $db -> execSql($sql);
		$userId = $trans -> lastId();

		require_once (Frm::$ROOT_PATH . 'user/conf/listener.php');
		foreach ($listener as $val) {
			require_once ($val['classPath']);
			$obj = new $val['className'];
			$obj -> onUserRegister($userId, $phonenum);
		}

		$trans -> commit();

		return $userId;
	}

}
?>