<?php
if (!defined('ROOT_PATH')) {
	define('IN_ECS', true);
	require_once (dirname(__FILE__) . '/../../cp/includes/init.php');
}

require_once (ROOT_PATH . "../client/classes/Timer.php");
class TimerJob {

	private $mDbAgent;
	private $sns;

	function __construct() {
		global $db;
		global $sns;

		$this -> mDbAgent = $db;
		$this -> sns = $sns;
	}

	/**
	 * 获取需要执行的任务的数据
	 * @return [type] [description]
	 */
	private function getTask() {
		$now = time();

		$sql = " SELECT * FROM " . $this -> sns -> table('timer') . " WHERE exec_time < $now ";
		$ret = $this -> mDbAgent -> getAll($sql);
		return $ret;
	}

	/**
	 * 运行一条任务
	 * id 自增
	 * exec_time 将要在此时刻执行
	 * php_uri 要使用的php文件
	 * param_key 回调时的类名方法名,方法参数的key
	 * param_value 回调时的类名方法名,方法参数的value
	 * task_type
	 * @return [type] [description]
	 */
	private function runTask($argv) {
		include_once ($argv['php_uri']);
		// print_r($argv['param']);
		$param = unserialize($argv['param']);

		$className = $param['className'];
		$methodName = $param['methodName'];
		unset($param['className']);
		unset($param['methodName']);

		$param = $param['param'];
		if (!is_array($param)) {
			$param = array($param);
		}

		//构造动态执行的字符串
		$n = 0;
		$paramStr = "";
		$pLen = count($param);

		foreach ($param as $k => $v) {

			// echo "\$param".$n."=\$v;";
			eval("\$param" . $n . "=\$v;");
			if ($pLen - 1 == $n) {
				$paramStr .= "\$param" . $n;
			} else {
				$paramStr .= "\$param" . $n . ",";
			}
			$n += 1;

		}
		// print_r($param0);
		$paramStr = "(" . $paramStr . ");";

		//先删除，后执行，免得执行失败后面的任务都执行不了了
		$timer = new Timer();
		$timer -> deleteTask($argv['id']);

		if (!class_exists($className)) {
			return;
		}

		$taskObj = new $className;

		if (!method_exists($taskObj, $methodName)) {
			return;
		}

		// echo "$taskObj->$methodName".$paramStr;
		eval("\$a=\$taskObj->\$methodName" . $paramStr);
		print_r($a);
		// print_r($taskObj->$methodName($param));
	}

	// /**
	//  * 数据库的数据是,号分割的,拼成key->value的数组
	//  * @param  [type] $keyString   [description]
	//  * @param  [type] $valueString [description]
	//  * @return [type]              [description]
	//  */
	// private function formatParam($keyString,$valueString)
	// {
	// 	$keyArray= explode(',',$keyString);
	// 	$valueArray= explode(',',$valueString);
	// 	return array_combine($keyArray,$valueArray);
	// }

	/**
	 * 运行所有任务时间小于现在时间的任务,并且删除任务记录
	 * @return [type] [description]
	 */
	public function run() {

		$tasks = $this -> getTask();
		foreach ($tasks as $v) {
			$this -> runtask($v);
		}

	}

}

$a = new TimerJob();
$a -> run();
