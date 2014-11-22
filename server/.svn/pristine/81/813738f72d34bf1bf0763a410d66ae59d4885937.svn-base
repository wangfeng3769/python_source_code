<?php

function getEquipments()
{
	global $db, $ecs;
	$res = $db->getAll("select * from ".$ecs->table('equipment')." order by id");
	return $res;
}


?>