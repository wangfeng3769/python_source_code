<?php
set_time_limit(0);

define('IN_ECS', true);
require_once(dirname(__FILE__) . '/../../cp/includes/init.php');

class ClientManager{
	
	public function getLastVersion(){
		global $db , $ecs;

		$sql = "SELECT version_code , version_name , version_url , version_desc FROM " .$ecs->table( "client_soft" ).
		" ORDER BY version_code DESC ";
		$limitQuery = $db->getRow($sql ) ;

		return $limitQuery;
	}
}
?>