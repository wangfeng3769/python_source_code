<?php

class JsonOutput {

	/**
	 * 外部执行指定类方法的方法
	 *
	 * @param string $className
	 *        	类名
	 * @param string $methodName
	 *        	方法名
	 * @return
	 */
	function output($className, $methodName) {
		global $errorCode;

		// 如果post与get中有相同的键值，post的值将覆盖get
		$argv = array_merge($_GET, $_POST);

		try {
			$obj = new $className();
			$ret = $obj -> $methodName($argv);
			$errno = 2000;
			$errstr = '';
		} catch ( Exception $e ) {
			$errno = $e -> getCode();
			if ($e -> getMessage() == '') {
				require_once (Frm::$ROOT_PATH . 'client/conf/inc_error.php');
				$errstr = $errorCode[$errno];
			} else {
				$errstr = $e -> getMessage();
			}
		}

		header('Content-Type:text/html;charset=utf-8');
		echo json_encode(array("errno" => $errno, "errstr" => $errstr, "content" => $ret));
	}

}
