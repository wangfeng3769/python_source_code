<?php

class ArrOutput {
	/**
	 * 外部执行指定类方法的方法
	 *
	 * @param string $className
	 *        	类名
	 * @param string $methodName
	 *        	方法名
	 * @return
	 */
	// private $_instance = null;
	//('MemberClient','PublicClient');
	public $accessType = null;
	// private __construct()
	// {

	// }
	// static public getInstance()
	// {
	// 	if (self::$_instance == null) {
	// 		self::$_instance = new ArrOutput();
	// 	}

	// 	return self::$_instance;
	// }
	
	function output($methodName,$argv='') {
		// 如果post与get中有相同的键值，post的值将覆盖get
		$argv = array_merge($_GET, $_POST);
		try {
			$className=$this->accessType;
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


		if ($errno!=2000) {
			session_start();
			header("Content-type: text/html; charset=utf-8");
			if ($_REQUEST['is_ajax']) {
				$ret=array('errCode'=>$errno,'errStr'=>$errstr);
				echo json_encode($ret);
			}
			else
			{
				$_SESSION['actionErrorNo']=$errno;
				$tpl='<script>window.location.href="/error.php"</script>';
				echo $tpl;
			}
			exit;
		}
		
		return array("errno" => $errno, "errstr" => $errstr, "content" => $ret);
		// return $ret;
	}

}
