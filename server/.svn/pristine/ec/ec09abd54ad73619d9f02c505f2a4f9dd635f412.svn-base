<?php
set_time_limit(0);

define('IN_ECS', true);
require_once (dirname(__FILE__) . '/../../cp/includes/init.php');

class SysConfig {

	/**
	 * @var cls_mysql
	 */
	private $db = null;
	private $ecs = null;
	private $sns = null;

	public function SysConfig() {
		global $db, $ecs, $sns;
		$this -> db = $db;
		$this -> ecs = $ecs;
		$this -> sns = $sns;
	}

	public function getAllValue($type) {
		$sql = "SELECT param , value FROM " . $this -> ecs -> table("sys_config") . " WHERE type = '$type'";
		$ret = $this -> db -> getAll($sql);

		$arr = array();
		foreach ($ret as $value) {
			$arr[$value['param']] = $value['value'];
		}

		return $arr;
	}

}
?>