<?php

function getUserGroup()
{
	global $db, $sns;
	$group = $db->getAll("select * from ". $sns->table("user_group")." order by user_group_id" );
	return $group ;
}
function getUserType()
{
	global $db, $sns;
	// $type = $db->getAll("select * from ". $sns->table("user_type")." order by id" );
	
	return array(array("id"=>0,"name"=>"个人"),array("id"=>1,"name"=>"学生"));
}


?>