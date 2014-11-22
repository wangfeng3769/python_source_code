<?php
/*************************************************/
/*
 * 在这儿配置接口的属性 /************************************************
 */

require_once (ROOT_PATH . "../client/conf/inc_error.php");
require_once (ROOT_PATH . "../client/classes/UserManager.php");
/**
 * **********************************************
 */
/*
 * 接口属性配置结束 /************************************************
 */
class Output {
	private $unLoginMethods = array (
			'UserManager' => array (
					'login',
					'captchaRegister',
					'captchaFindpassword' 
			),
			'ClientManager' => array (
					'getLastVersion' 
			),
			'SmsCaptcha' => array (
					'get' 
			),
			'ModifyPhone' => array (
					'getCaptcha' 
			),
			'MsgProcessor' => array (
					'process' 
			) 
	);
	private $initClass = null;
	private $classNameArr = null;
	function __construct() {
		global $initClass;
		global $classNameArr;
		$this->initClass = $initClass;
		$this->classNameArr = $classNameArr;
	}
	
	/**
	 * 判断要执行的类方法是否在配置中,是返回该对象,否则返回false
	 *
	 * @param string $className
	 *        	类名
	 * @param string $methodName
	 *        	方法名
	 * @return $obj 类
	 */
	function __preValidat($className, $methodName) {
		$classInfo = $this->classInfo;
		if (empty ( $className ) || empty ( $methodName )) {
			return false;
		}
		
		$classInfo = $this->classNameArr [$className];
		if (empty ( $classInfo )) {
			return false;
		}
		
		if (! in_array ( $methodName, $classInfo ['methodArr'] )) {
			return false;
		}
		require_once ($classInfo ['classFile']);
		if (! class_exists ( $className )) {
			return false;
		}
		$obj = new $className ();
		if (! method_exists ( $obj, $methodName )) {
			return false;
		}
		
		return $obj;
	}
	
	/**
	 * 执行配置文件中初始化方法
	 *
	 * @return [type] [description]
	 */
	function initClass($argv) {
		// 初始化,执行登陆操作?
		//
		$initClass = $this->initClass;
		if (! empty ( $initClass )) {
			require_once ($initClass ['classFile']);
			$initObj = new $initClass ['class'] ();
			$initObj->$initClass ['method'] ( $argv );
		}
	}
	
	/**
	 * 检查参数是否正确,是否需要登陆执行,抛出Exception
	 *
	 * @param [type] $className
	 *        	[description]
	 * @param [type] $methodName
	 *        	[description]
	 * @return $obj 返回该类对象
	 */
	function validateWithException($className, $methodName) {
		$obj = $this->__preValidat ( $className, $methodName );
		
		if (! $obj) {
			throw new Exception ( '', 4000 );
		}
		
		// $um = new UserManager();
		// $um->allwaysLogin($_GET);
		
		if ($this->__isNeedLogin ( $className, $methodName )) {
			if (! $this->__hasLogin ()) {
				throw new Exception ( '', 4060 );
			}
		}
		
		return $obj;
	}
	function __isNeedLogin($className, $methodName) {
		$unLoginMethods = $this->unLoginMethods;
		if (! empty ( $unLoginMethods ) && ! empty ( $unLoginMethods [$className] )) {
			
			if (in_array ( $methodName, $unLoginMethods [$className] )) {
				return false;
			} else {
				return true;
			}
		} else {
			return true;
		}
	}
	
	/**
	 * 判断用户是否有登陆
	 *
	 * @return boolean
	 *
	 *
	 */
	function __hasLogin() {
		$um = new UserManager ();
		return $um->isLogined ();
	}
	
	/**
	 * 外部执行指定类方法的方法
	 *
	 * @param string $className
	 *        	类名
	 * @param string $methodName
	 *        	方法名
	 * @return
	 *
	 *
	 *
	 *
	 *
	 */
	function executeJson() {
		global $errorCode;
		try {
			$ret = $this->execute ();
			$errno = 2000;
			$errstr = '';
		} catch ( Exception $e ) {
			$errno = $e->getCode ();
			if ($e->getMessage () == '') {
				$errstr = $errorCode [$errno];
			} else {
				$errstr = $e->getMessage ();
			}
		}
		
		header ( 'Content-Type:text/html;charset=utf-8' );
		echo json_encode ( array (
				"errno" => $errno,
				"errstr" => $errstr,
				"content" => $ret 
		) );
	}
	
	/**
	 * 外部执行指定类方法的方法
	 *
	 * @param string $className
	 *        	类名
	 * @param string $methodName
	 *        	方法名
	 * @return array
	 */
	function execute() {
		$className = $_GET ['class'];
		$methodName = $_GET ['item'];
		// 如果post与get中有相同的键值，post的值将覆盖get
		$argv = array_merge ( $_GET, $_POST );
		
		$obj = $this->validateWithException ( $className, $methodName );
		$ret = $obj->$methodName ( $argv );
		return $ret;
	}
}
