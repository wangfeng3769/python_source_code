<?php
if (!defined('ROOT_PATH'))
{
	define('IN_ECS',true);
	require_once (dirname(__FILE__).'/../../cp/includes/init.php');
}

class Timer{
	
	private $mDbAgent;
	private $sns;
	function __construct()
	{
		global $db;
		global $sns;
		$this->mDbAgent = $db;
		$this->sns=$sns;
	}

	function __validator($exceTime,$className,$methodName,$phpURI)
	{
		// if ($exceTime < time()) {
		// 	throw new Exception("执行时间是过去时间");
		// 	return false;
		// }
		require_once($phpURI);
		if (!class_exists($className)) {
			throw new Exception("该类不存在");
			return false;
		}
		$obj = new $className;
		if (!method_exists($obj, $methodName)) {
			throw new Exception("计划执行的方法不存在");
			return false;
		}
		return true;
	}

	/**
	 * 添加定时任务
	 * @param [int] $exceTime   [description]
	 * @param [string] $className  [description]
	 * @param [string] $methodName [description]
	 * @param [string] $phoURI     [description]
	 * @param [array] $param      [description]
	 */
	function addTask($exceTime,$className,$methodName,$phpURI,$param,$taskType="")
	{
		
		if ($this->__validator($exceTime,$className,$methodName,$phpURI)) {
			//插入数据库
			$data = array('exec_time'=>$exceTime,'php_uri'=>$phpURI,'task_type'=>$taskType);


			// $param['className']=$className;
			// $param['methodName']=$methodName;
			// $paramKey = array_keys($param);
			// $paramValue = array_values($param);

			// $keyString = implode(',', $paramKey);
			// $valueString = implode(',', $paramValue);

			// $data['param_key']=$keyString;
			// $data['param_value']=$valueString;
			 
			$data['param']=serialize(array("param"=>$param,"className"=>$className,"methodName"=>$methodName));
			$table = $this->sns->table('timer');
			$this->mDbAgent->autoExecute($table,$data);
		}

	}

	/**
	 * 将参数集成到一个数组里,序列化成string
	 * @param  array $arr 定时任务方法的参数
	 * @return string      序列化字符串
	 */
	private function serialParam($arr)
	{

	}

	/**
	 * 删除任务
	 * @param  [array] $ids [任务ID]   如:(1,2,3,4,5,6)
	 * @return [type]     [description]
	 */
	function deleteTaskBatch($ids)
	{
		if (empty($ids)) {
			//$ids为空
			throw new Exception("任务ID数组为空");
		}

		$where = ' WHERE 0 ';
		foreach ($ids as $v) {
			if (ceil($v)!=$v) {
				//传入id不是整数
				throw new Exception("任务ID数组中,元素不是整数");
			}
			$where .= ' OR id='.$v;
		}

		$sql = " DELETE FROM ".$this->sns->table('timer').$where;
		$this->mDbAgent->query($sql);
	}

	/**
	 * 删除一条计划任务
	 * @param  [int] $id [任务ID]
	 * @return [type]     [description]
	 */
	function deleteTask($id)
	{
		if (ceil($id)!=$id) {
			throw new Exception("删除任务的 id不可为空");
		}
		$sql = " DELETE FROM ".$this->sns->table('timer')." WHERE id=".$id;
		$this->mDbAgent->query($sql);
	}
}


// $className = "ClientManager";
// $methodName = "getLastVersion";
// $phpURI=ROOT_PATH."../client/classes/ClientManager.php";
// $param['time_from']=strtotime("2012-11-26 18:45:00");
// $param['time_end']=strtotime("2012-11-28 20:45:00");
// $param['car_id']=2;
// $param['user_id']=2;

// $param=array();
// $exceTime = strtotime("2012-11-12 18:24");

// require($phpURI);

// $a = new Timer();
// $a->deleteTaskBatch(array('5','6'));
// $a->addtask($exceTime,$className,$methodName,$phpURI,$param,'test');
// $a->deleteTask(10);
