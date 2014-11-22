<?php

/**
 *  获得该车当天的时价
 */
function getHourPriceOfCar( $car_id )
{
	$today = date('Y-m-d',time());
	$sql = 	" SELECT COUNT(*) ".
			" FROM ".$GLOBALS['ecs']->table('vacation').
			" WHERE holiday=".$today;
	if ($GLOBALS['db']->getOne($sql) > 0) 
	{
		$is_holiday = 1;
	}
	else
	{
		$is_holiday = 0;
	}
	$sql = 	" SELECT MAX(price) ".
			" FROM ".$GLOBALS['ecs']->table('car_price_detail').
			" WHERE car_id=".$car_id.
			" AND type=".$is_holiday;
	$price = $GLOBALS['db']->getOne($sql);
	return $price ;
}

function getStations()
{
	global $db, $ecs ;
	$res = $db->getAll("select * from ".$ecs->table("station")." order by id" ); 
	
	return $res;
}