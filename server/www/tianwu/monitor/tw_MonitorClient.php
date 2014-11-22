<?php
require_once dirname(__FILE__) . '/../../../hfrm/Frm.php';
require_once Frm::$ROOT_PATH . 'www/tianwu/monitor/tw_MonitorAuth.php';

class MonitorClient extends MonitorAuth
//class MonitorClient
{

	public function __construct() 
	{
		parent::__construct();
	}

	public function showCarOnMap($argv) 
    //public function showCarOnMap($lng,$lat) 
	{
		$lng = $argv['lng'];
		$lat = $argv['lat'];

		require_once (Frm::$ROOT_PATH . 'cp/admin/templates/monitor_car_position.html');

		exit ;
	}

	public function carList($argv) 
	{

		require_once Frm::$ROOT_PATH . 'www/tianwu/monitor/tw_CarManager.php';
		$cm = new TwCarManager();
		$carList = $cm -> getCarList();
    //print_r($carList);
	//$adminStateDesc = array('禁用', '启用', '即将上线');
		$orderStateDesc = array('故障', '未使用', '等待密码', '等待取钥匙', '开始用车', 
			                    '归还钥匙，等待还车', '确认还车', '没有刷卡锁门');

		$carIdArr = array();
		foreach ($carList as &$car)
		{
			$carIdArr[] = $car['id'];
		}

    //print_r($carIdArr);
        $orderList = $cm -> getOrderingListByCarList($carIdArr);

    //print_r($orderList);

        $carOrderList=array();
        foreach ($orderList as $v) {
        	$carOrderList[$v['car_id']] = $v;
        }
    //print_r($carOrderList);

        $carOrderArr = array();
       	foreach ($carList as $v) 
       	{
       		$carOrderArr[$v['id']]=$v;
       		$carOrderArr[$v['id']]['order']=$carOrderList[$v['id']];
    //print_r($carOrderArr[$v['id']]['order']);
       	}
    //print_r($carOrderArr);

		require_once Frm::$ROOT_PATH . 'resource/classes/CarDeviceManager.php';
		$cdm = new CarDeviceManager();
		$deviceList = $cdm -> getDeviceListByCarList($carIdArr);
    //print_r($deviceList);

        $dShowArr = array();
        require_once (Frm::$ROOT_PATH . 'ecm/client/ECM.php');
		$ecm = new ECM();
		foreach ($deviceList as $carId => $obj) 
		{	
			$dInfoArr = $ecm -> getDeviceInfo(array($obj -> id));
            $carOrderArr[$carId]['machine_stat']=$dInfoArr[$obj -> id]['sysstatue'];
    //print_r($carOrderArr[$carId]);echo "<br>";
		}

    //print_r($carOrderArr);
       
		require_once Frm::$ROOT_PATH . 'www/tianwu/monitor/tw_car_monitor_list.php';
    //header( 'Location: /tw/tw_car_monitor_list.php');
		exit;
	}

}
