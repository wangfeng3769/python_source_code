<?php

require_once (Frm::$ROOT_PATH . 'hfrm/hfc/DigitCaptcha.php');

class SmsCaptcha extends DigitCaptcha {

	static public $SESSION_RECEIVER = 'smsCaptchaReceiver';

	public function SmsCaptcha() {
		parent::DigitCaptcha();
	}

	public function get($contentFmt, $receiver) {
		$cc = parent::get();

		$sms = sprintf($contentFmt, $cc);

		$_SESSION[self::$SESSION_RECEIVER] = $receiver;

		require_once (Frm::$ROOT_PATH . 'system/sms_platform/classes/SmsSender.php');
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