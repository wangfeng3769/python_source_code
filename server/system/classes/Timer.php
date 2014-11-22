<?php

class Timer {

	function __construct() {
	}

	protected function validator($exceTime, $className, $methodName, $phpURI) {
		include_once (Frm::$ROOT_PATH . $phpURI);
		if (!class_exists($className)) {
			throw new Exception("该类不存在", 4000);
		}
		$obj = new $className;
		if (!method_exists($obj, $methodName)) {
			throw new Exception("计划执行的方法不存在", 4000);
		}
	}

	/**
	 * 添加定时任务
	 * @param [int] $exceTime   [description]
	 * @param [string] $className  [description]
	 * @param [string] $methodName [description]
	 * @param [string] $phoURI     [description]
	 * @param [array] $param      [description]
	 */
	function addTask($execTime, $className, $methodName, $phpURI, $param, $taskType = "") {
		$this -> validator($execTime, $className, $methodName, $phpURI);
		$param = serialize(array("param" => $param, "className" => $className, "methodName" => $methodName));
		$sql = "INSERT INTO edo_timer ( exec_time , php_uri , task_type , param ) VALUES (
				$execTime , '$phpURI' , '$taskType' , '$param')";

		$db = Frm::getInstance() -> getDb();
		$db -> execSql($sql);
	}

	/**
	 * 删除任务
	 * @param  [array] $ids [任务ID]   如:(1,2,3,4,5,6)
	 * @return [type]     [description]
	 */
	function deleteTaskBatch($ids) {
		if (empty($ids)) {
			//$ids为空
			throw new Exception("任务ID数组为空", 4000);
		}

		$where = ' WHERE 0 ';
		foreach ($ids as $v) {
			if (ceil($v) != $v) {
				//传入id不是整数
				throw new Exception("任务ID数组中,元素不是整数", 4000);
			}
			$where .= ' OR id=' . $v;
		}

		$sql = " DELETE FROM edo_timer " . $where;
		$db = Frm::getInstance() -> getDb();
		$db -> execSql($sql);
	}

	/**
	 * 删除一条计划任务
	 * @param  [int] $id [任务ID]
	 * @return [type]     [description]
	 */
	function deleteTask($id) {
		if (ceil($id) != $id) {
			throw new Exception("删除任务的 id不可为空", 4000);
		}
		$sql = " DELETE FROM edo_timer WHERE id=" . $id;
		$db = Frm::getInstance() -> getDb();
		$db -> execSql($sql);
	}

}
