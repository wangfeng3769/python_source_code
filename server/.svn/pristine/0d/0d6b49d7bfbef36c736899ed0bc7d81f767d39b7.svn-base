<?php
require_once dirname(__FILE__) . '/../../hfrm/Frm.php';
require_once Frm::$ROOT_PATH . 'client/classes/MonitorAuth.php';

class MonitorClient extends MonitorAuth {

    /*add by matao 2013.6.24 start*/
	const GPS = 'gps';
	const CarId = 'cid';
	const PATH='ecm/CarInfo/';
    /*add by matao 2013.6.24 end*/

	public function __construct() {
		parent::__construct();
	}

	public function showCarOnMap($argv) {
		$lng = $argv['lng'];
		$lat = $argv['lat'];

		require_once (Frm::$ROOT_PATH . 'cp/admin/templates/monitor_car_position.html');

		exit ;
	}

	public function carList($argv) {
		require_once Frm::$ROOT_PATH . 'resource/classes/CarManager.php';
		$cm = new CarManager();
		$carList = $cm -> getCarListByPage($argv['page_no']);

		$adminStateDesc = array('禁用', '启用', '即将上线');
		$orderStateDesc = array('故障', '未使用', '等待密码', '等待取钥匙', '开始用车', '归还钥匙，等待还车', '确认还车', '没有刷卡锁门');
		$carIdArr = array();
		foreach ($carList as &$car) {
			$carIdArr[] = $car['id'];

			$car['admin_stat'] = $adminStateDesc[$car['status']];
		}

		require_once Frm::$ROOT_PATH . 'resource/classes/CarDeviceManager.php';
		$cdm = new CarDeviceManager();
		$deviceList = $cdm -> getDeviceListByCarList($carIdArr);
		$dShowArr = array();
		foreach ($deviceList as $carId => $obj) {
			require_once (Frm::$ROOT_PATH . 'ecm/client/ECM.php');
			$ecm = new ECM();
			$dInfoArr = $ecm -> getDeviceInfo(array($obj -> id));

			$dShowArr[$carId]['order'] = $orderStateDesc[$dInfoArr[$obj -> id]['sysstatue']];
			$dShowArr[$carId]['key'] = ($obj -> cts & 0x01) ? '有' : '无';
			$dShowArr[$carId]['oil_card'] = ($obj -> cts & 0x02) ? '有' : '无';
			$dShowArr[$carId]['door'] = ($obj -> cts & (1<<10)) ? '关' : '开';
			$dShowArr[$carId]['oil'] = $dInfoArr[$obj -> id]['oil'];
			//add by yangbei 2013-07-04 增加车机版本更新
			if (null!=$dInfoArr[$obj -> id]['svs']) {
				$deviceList[$carId] -> software_version = $dInfoArr[$obj -> id]['svs'];
			}
			//add by yangbei 2013-07-04 增加车机版本更新
			$voltage = $obj -> cts & (11<<5);
			$dShowArr[$carId]['voltage'] = '正常';
			if (1<<5 == $voltage) {
				$dShowArr[$carId]['voltage'] = '低';
			} else if (10<<5 == $voltage) {
				$dShowArr[$carId]['voltage'] = '无';
			}

			$dShowArr[$carId]['last_heartbeat_time'] = $dInfoArr[$obj -> id]['RecTime'];
			$dShowArr[$carId]['offline'] = time() - strtotime($dInfoArr[$obj -> id]['RecTime']) >= 60;

			$dShowArr[$carId]['VssRecTime'] = $dInfoArr[$obj -> id]['VssRecTime'];
			$dShowArr[$carId]['NoElectric'] = time() - strtotime($dInfoArr[$obj -> id]['VssRecTime']) >=  259200;//三天
		}

		require_once Frm::$ROOT_PATH . 'cp/admin/templates/car_monitor_list.php';

		exit ;
	}

	public function detail($argv){
		$carId = $argv['car_id'];
		
		require_once Frm::$ROOT_PATH . 'resource/classes/CarManager.php';
		$cm = new CarManager();
		$row = $cm->getCar($carId);

		$adminStateDesc = array('禁用', '启用', '即将上线');
		$orderStateDesc = array('故障', '未使用', '等待密码', '等待取钥匙', '开始用车', '归还钥匙，等待还车', '确认还车', '没有刷卡锁门');


		require_once Frm::$ROOT_PATH . 'resource/classes/CarDeviceManager.php';
		$cdm = new CarDeviceManager();
		$deviceList = $cdm -> getDeviceListByCarList(array($carId));
		$dShowArr = array();
		foreach ($deviceList as $carId => $obj) {
			require_once (Frm::$ROOT_PATH . 'ecm/client/ECM.php');
			$ecm = new ECM();
			$dInfoArr = $ecm -> getDeviceInfo(array($obj -> id));

			$dShowArr[$carId]['order'] = $orderStateDesc[$dInfoArr[$obj -> id]['sysstatue']];
			$dShowArr[$carId]['key'] = ($obj -> cts & 0x01) ? '有' : '无';
			$dShowArr[$carId]['oil_card'] = ($obj -> cts & 0x02) ? '有' : '无';
			$dShowArr[$carId]['door'] = ($obj -> cts & (1<<10)) ? '关' : '开';
			$dShowArr[$carId]['oil'] = $dInfoArr[$obj -> id]['oil'];

			$voltage = $obj -> cts & (11<<5);
			$dShowArr[$carId]['voltage'] = '正常';
			if (1<<5 == $voltage) {
				$dShowArr[$carId]['voltage'] = '低';
			} else if (10<<5 == $voltage) {
				$dShowArr[$carId]['voltage'] = '无';
			}

			$dShowArr[$carId]['last_heartbeat_time'] = $dInfoArr[$obj -> id]['RecTime'];
			$dShowArr[$carId]['offline'] = time() - strtotime($dInfoArr[$obj -> id]['RecTime']) >= 60;

			$dShowArr[$carId]['VssRecTime'] = $dInfoArr[$obj -> id]['VssRecTime'];
			$dShowArr[$carId]['NoElectric'] = time() - strtotime($dInfoArr[$obj -> id]['VssRecTime']) >=  259200;//三天
		}

		require_once Frm::$ROOT_PATH . 'cp/admin/templates/car_monitor_detail.php';

		exit ;
	}

	public function openDoor($argv) {
		require_once Frm::$ROOT_PATH . 'ecm/client/CarAgent.php';
		$ca = new CarAgent();
		$ca -> openDoor($argv['id']);
	}

	public function closeDoor($argv) {
		require_once Frm::$ROOT_PATH . 'ecm/client/CarAgent.php';
		$ca = new CarAgent();
		$ca -> closeDoor($argv['id']);
	}

	public function ring($argv) {
		require_once Frm::$ROOT_PATH . 'ecm/client/CarAgent.php';
		$ca = new CarAgent();
		$ca -> ring($argv['id']);
	}

	public function reset($argv) {
		require_once Frm::$ROOT_PATH . 'ecm/client/CarAgent.php';
		$ca = new CarAgent();
		$ca -> reset($argv['id']);
	}

	/*add by matao 2013.6.24 start
	按条件查找车辆轨迹*/

	public function getLocation($argv)
	{
		$carNumber=trim($argv['carNumber']);
		$startTime = strtotime($argv['startTime']);
		$endTime = strtotime($argv['endTime']);
		$startFile=strtotime(substr($argv['startTime'],0,10));
        $endFile=strtotime(substr($argv['endTime'],0,10));

		require_once Frm::$ROOT_PATH . 'resource/classes/CarManager.php';
		$cm = new CarManager();
		$carId=$cm -> getCarIdByNumber($carNumber);
//echo $carId;
        $filePath=Frm::$ROOT_PATH .self::PATH."$carId";
         
        $positions = array();  

        if(!is_dir($filePath))  return;
		foreach(scandir($filePath) as $file)
		{          
            $fileName=substr($file,0,10);

//echo "{";echo $fileName;echo "}"; 

            if (!preg_match('/^[\d]{4}-[\d]{2}-[\d]{2}$/',$fileName))
            {
//echo "[";echo $file;echo "]";
            	continue;
            }
		  	$strTime=strtotime($fileName);
		    if($file!='.'  && $file!='..' && $strTime >= $startFile && $strTime <= $endFile)
		    {
//echo $filePath."/".$file;
		      	$log = file($filePath."/".$file); 
	               // print_r($log);
				foreach ($log as $key => $content) 
				{   
					$haveGps = strstr($content, self::GPS);
					//获取经纬度
            		//echo $haveGps;
            		$time=strtotime(substr($content,0,19));
            		//获取时间子串	
					if ($haveGps!=false && $time>=$startTime && $time<=$endTime) 
					{	
						$toJson = strstr($content,'{');
						$JsonData= json_decode($toJson);
						$cid = $JsonData->cid;
						if($cid==$carId)
						{
							$gps=$JsonData->gps;
							$lng = $this -> changeGps($gps[0]);
							$lat = $this -> changeGps($gps[1]); 
            		        $arr['longitude'] = $lng;
            		        $arr['latitude'] = $lat;
            		        
							array_push($positions, $arr);   
						}
					}
				}
		    }
		}
//print_r($positions);
        return $positions;	
	}



		/*	$log = file('2.log');

			//$log = file(self::FilePatch);
			$positions = array();
			foreach ($log as $key => $content) 
			{   
				$haveGps = strstr($content, self::GPS);
				//获取经纬度
				
                echo $haveGps;
                $time=strtotime(substr($content,0,19));
                //获取时间子串

				if ($haveGps!=false && $time>=$startTime && $time<=$endTime) 
				{	
					$toJson = strstr($content,'{');
					$JsonData= json_decode($toJson);
					$cid = $JsonData->cid;
					if($cid==$carId)
					{
						$gps=$JsonData->gps;

						$lng = $this -> changeGps($gps[0]);
					    $lat = $this -> changeGps($gps[1]);
                        
                        $arr['longitude'] = $lng;
                        $arr['latitude'] = $lat;
                        
					    array_push($positions, $arr);   
					}
				}
			}*/
            
        
        //将GPS数据转换成标准GPS数据
		protected function changeGps($df) 
		{
			$fdf = floatval( $df );
			$d = floor ( $fdf / 100 );
			$f = $fdf - $d * 100;  
			return $d + ($f / 60);
		}

		/*add by matao 2013.6.24 end*/
}
