<?php

class Log {

	static protected $sMe = null;

	protected function Log() {
	}

	static public function e($msg, $code, $file, $line) {
		if (null == self::$sMe) {
			self::$sMe = new Log();
		}

		//记录日志
	}

}
?>