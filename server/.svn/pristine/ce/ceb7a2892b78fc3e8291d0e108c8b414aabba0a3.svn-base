<?php
if (!defined('ROOT_PATH'))
{
  define('IN_ECS',true);
  require_once (dirname(__FILE__).'/../cp/includes/init.php');
}
require_once(ROOT_PATH."../client/classes/SecondSettlement.php");

$a = new SecondSettlement();
$now = strtotime( date( 'Y-m-d' ) . " 08:00" );

$orderInfo = $a->getDistanceDayOrderInfo($now,7,3);


print_r($orderInfo);
