<?php
error_reporting(0);

if (!function_exists('HandleError')) {
	function HandleError() {
		$error = error_get_last();
		if (!empty($error)) {
			$t = $error['type'];
			if (E_ERROR & $t || E_PARSE & $t || E_CORE_ERROR & $t || E_COMPILE_ERROR & $t || E_RECOVERABLE_ERROR & $t || E_USER_ERROR & $t) {
				$log = $error['message'] . '\r\nFile:' . $error['file'] . '; Line:' . $error['line'] . "\r\n";
				$fp = fopen('/home/hoheart/log.log', 'a+');
				fwrite($fp, $log);
				fclose($fp);
			}
		}
	}

}

register_shutdown_function('HandleError');

class Frm {

	static public $ROOT_PATH = null;

	static protected $sMe = null;

	protected $mConf = null;
	protected $mDb = null;

	protected function Frm() {
	}

	/**
	 *
	 * @return Frm
	 */
	static public function getInstance() {
		if (null == self::$sMe) {
			self::$sMe = new Frm();
		}

		return self::$sMe;
	}

	/**
	 *
	 * @return Conf
	 */
	public function getConf() {
		if (null == $this -> mConf) {
			require_once (self::$ROOT_PATH . 'hfrm/Conf.php');
			$this -> mConf = new Conf(self::$ROOT_PATH);
		}

		return $this -> mConf;
	}

	/**
	 *
	 * @return DatabaseAgent
	 */
	public function getDb() {
		if (null == $this -> mDb) {
			require_once (self::$ROOT_PATH . 'hfrm/DatabaseAgent.php');

			$node = $this -> getConf() -> getNode('database');
			$this -> mDb = new DatabaseAgent(self::$ROOT_PATH, $node);
		}

		return $this -> mDb;
	}

}

Frm::$ROOT_PATH = str_replace('hfrm/Frm.php', '', str_replace('\\', '/', __FILE__));
?>