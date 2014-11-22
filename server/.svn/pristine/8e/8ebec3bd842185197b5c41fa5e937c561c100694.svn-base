<?php 
	/**
	 * 选择输出方式
	 * @param  [type] $className  [description]
	 * @param  string $type       [description]
	 * @return [type]             [description]
	 */
	function selectOutput($outputClassName,$accessType)
	{	
		static $opObj = array();
		$outputArr=array('JsonOutput','ArrOutput');
		$typeArr=array('MemberClient','PublicClient');
		
		//获取输出对象,单例
		if ($opObj[$outputClassName]===NULL) {
			if (in_array($outputClassName, $outputArr)) {
				require_once (Frm::$ROOT_PATH . 'client/classes/'.$outputClassName.'.php');
				$opObj[$outputClassName]=new $outputClassName();
			}
		}
		// if (!empty($opObj[$outputClassName]->accessType) && $opObj[$outputClassName]->accessType!=$accessType) {
			
		// }
		//暂时支持数组类输出,修改或者新增JsonOutput类才可以支持JsonOutput输出
		$opObj[$outputClassName]->accessType=$accessType;
		require_once (Frm::$ROOT_PATH . 'client/classes/'.$accessType.'.php');

		return $opObj[$outputClassName];
		
	}
 ?>