<?php
define('IN_ECS', true);
require_once(dirname(__FILE__) . '/includes/init.php');

$a1=microtime();
require(ROOT_PATH.'secondBalance.php');
$b1=microtime();
echo $b1-$a1;
echo "<br/>";



$a2=microtime();
require(ROOT_PATH.'../client/classes/SecondBalance.php');
$b2=microtime();
echo $b2-$a2;
echo PHP_EOL;


die();


define('IN_ECS', true);
require_once(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH.'../client/classes/Ordermanager.php');
require_once(ROOT_PATH.'../client/classes/UserManager.php');

require_once(ROOT_PATH.'../client/classes/LoginManager.php');
// var_dump(LoginManager::loginWithToken('de2dd6d902c8255abd7cce822fc60dd6'));

$a = new OrderManager();
$argv['start_time']=strtotime("2012-10-26 18:45:00");
$argv['end_time']=strtotime("2012-10-29 20:45:00");


$argv['time_from']=strtotime("2012-10-26 18:45:00");
$argv['time_end']=strtotime("2012-10-28 20:45:00");
$argv['car_id']=2;
$argv['user_id']=2;

	/**
	 * 支付,'CashAccountManager','CreditAccountManager','AlipayAccountManager' 三种方式
	 * @param  int $argv['orderNo']     订单号
	 * @param  int $argv['useType']     支付方式
	 * @return [type]       [description]
	 */
// $a->payFor(array("order_id"=>172,"useType"=>"eduo"));

require_once(ROOT_PATH.'../client/classes/SecondBalance.php');
$s = new SecondBalance();

$now = strtotime("2012-11-02 08:33:22");

$orderInfo = $s->getDistanceDayOrderInfo($now,7,3);


print_r($orderInfo);
// echo UserManager::getUserViolateDeposit($argv['user_id']);
// echo $a->getViolateDeposit($argv);
// echo $a->balance_old($argv);
// var_dump($a->carListByOrderTime($argv));
// var_dump($a->thisCarHasOrder($argv));
// var_dump($a->carListByOrderTime($argv));
// $um = new UserManager();
// $e=$um->getUserViolate($argv['user_id']);


// $today = time();
// $next_day = $today+1*24*3600;
// $argv['user_id']=1;
// $argv['car_id']=1;
// $argv['time_from']=$today;
// $argv['time_end']=$next_day;
// echo time();
// echo date('Y-m-d H:i:s',1350049020);
// echo '<br/>';
// $tommorow = strtotime(date('Y-m-d',time()))+(3600*24);
// echo $tommorow;
//echo $a->get_count_day($argv);
//print_r($a->get_day_top_price($argv));
//$today = strtotime($today );
// $a->balance($argv);


//$a = new OrderManager();


