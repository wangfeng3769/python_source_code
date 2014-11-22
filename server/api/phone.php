<?php
define('IN_ECS', true);
require_once "../cp/includes/init.php";
include('includes/cls_json.php');
require('../core/sociax.php');

$act = $_GET['act'];


if($act=="getCarOrders")
{
	$end_date = (date("Y-m-d", strtotime("+3 day")));
	$end_time = strtotime($end_date);

	$car_id = $_GET['car_id'];
	$orders = $db->getAll("select order_id,car_id,start_time,end_time   from ".$ecs->table("order_info")." where car_id=".$car_id." and start_time>".time()." and end_time<".$end_time);
	$res = array();
	foreach($orders as $k=>$v)
	{
		array_push( $res, array("start_time"=>$v["start_time"],"end_time"=>$v["end_time"]));
	}

	$json   = new JSON ;
	echo $json->encode($res) ;
}
elseif($act=="login")
{
	$action = new PublicAction();

	$action->doLogin(array("email"=>$_GET["user"],"password"=>$_GET["password"]));
}
elseif($act=="createOrder")
{
	$start_time = strtotime($_GET['start_time']) ;
	$end_time = strtotime($_GET['end_time']) ;
	require_once( "../client/classes/UserManager.php" );
	$user_id = UserManager::getLoginUserId();
	$sql="insert into ".$ecs->table("order_info")." set car_id='$_GET[car_id]',user_id='$user_id',
	start_time='$start_time', end_time='$end_time'" ;
	$db->query($sql);
	$orderid = $db->insert_id();
	$card = $db->getRow("select c.card_number from ".$ecs->table("vip_card_issue")." i
					left join ".$ecs->table("vip_card")." c on i.vip_card_id=c.id where i.user_id=".$user_id." order by c.id desc limit 1 ");
	//echo "select c.card_number from ".$ecs->table("vip_card_issue")." i 				left join ".$ecs->table("vip_card")." c on i.vip_card_id=c.id where i.user_id=".$user_id." order by c.id desc limit 1 ";exit;
	//print_r( $card );exit;
	$nst = $start_time - strtotime( "2009-01-01 00:00:00" );//不加8了，因为$start_time从客户端提交上来时，已经加了
	$lst = unpack( "N" , pack( "V" , $nst ) );
	$xst = sprintf( "%08X" , $lst[1] );

	$net = $end_time - strtotime( "2009-01-01 00:00:00" );
	$let = unpack( "N" , pack( "V" , $net) );
	$xet = sprintf( "%08X" , $let[1] );

	$order = array( array( 'odn'=>$orderid , 'cn'=> "" + $card["card_number"] , 'pwd'=>"123456" , 'bt'=>$xst , 'et'=>$xet ));

	require_once( "../client/classes/CarAgent.php" );
	$agent = new CarAgent();
	$agent->updateOrder( array( 'car_id'=>$_GET['car_id'] , 'order'=>$order ) );

	echo 1;
}
?>