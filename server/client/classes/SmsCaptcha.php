<?php
set_time_limit(0);

define('IN_ECS', true);
if (!defined('ROOT_PATH')) {
	require_once (dirname(__FILE__) . '/../../cp/includes/init.php');
}

require_once (ROOT_PATH . "../client/classes/DigitCaptcha.php");
require_once (ROOT_PATH . "../client/SmsPlatform/SmsSender.php");

class SmsCaptcha extends DigitCaptcha {

	static public $SESSION_RECEIVER = 'smsCaptchaReceiver';

	public function SmsCaptcha() {
		parent::DigitCaptcha();
	}

	public function get($argv) {
		global $CAPTCHA_TEXT;

		$receiver = $argv['phone'];

		$cc = parent::get();
		$sms = sprintf($CAPTCHA_TEXT, $cc);

		$_SESSION[self::$SESSION_RECEIVER] = $receiver;

		$sender = new SmsSender();
		return $sender -> send($sms, $receiver);
	}

	public function check($captcha, $phone) {
		if ($_SESSION[self::$SESSION_RECEIVER] == $phone) {
			return parent::check($captcha);
		}

		return false;
	}
	
}
?>