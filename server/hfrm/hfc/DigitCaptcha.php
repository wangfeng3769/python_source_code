<?php

class DigitCaptcha {

	static protected $SESSION_TAG = 'SESS_CAPTCHA';

	public function DigitCaptcha() {
	}

	public function get($digitCount = 5) {
		$arr = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

		mt_srand((double) microtime() * 1000000);
		shuffle($arr);

		$code = substr(implode('', $arr), 0, $digitCount);

		$_SESSION[self::$SESSION_TAG] = $code;

		return $code;
	}

	public function check($str) {
		$ret = $_SESSION[self::$SESSION_TAG] == $str;
		unset($_SESSION[self::$SESSION_TAG]);

		return $ret;
	}

}
?>