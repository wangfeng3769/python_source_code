<?php
set_time_limit(0);

if (!defined('ROOT_PATH')) {
	define('IN_ECS', true);
	require (dirname(__FILE__) . '/../../cp/includes/init.php');
}

require_once (ROOT_PATH . "../client/classes/Location.php");
require_once (ROOT_PATH . "../client/classes/UserManager.php");

require_once (ROOT_PATH . "../client/classes/OrderManager.php");

class CarManager {

	public function CarManager() {
		global $db;
		$this -> mDbAgent = $db;
	}

	public function getNearby($argv) {
		$userId = UserManager::getLoginUserId();

		$startLati = $argv['start_lati'];
		//*1E6;
		$endLati = $argv['end_lati'];
		//*1E6;
		$startLongi = $argv['start_longi'];
		//*1E6;
		$endLongi = $argv['end_longi'];
		//*1E6;
		//modify by yangbei 2013-06-29 增加下线车辆判断
		// $sql = "SELECT c.id , c.name , c.model , p.hour_price AS price_time , p.price_km AS price_distance , s.name AS station , s.latitude , s.longitude , c.icon , 
		// 	f.user_id 
		// 	FROM edo_cp_car c LEFT JOIN ( select * from  edo_cp_car_price_base where type = 0 ) p ON c.id = p.car_id LEFT JOIN edo_cp_station s ON c.station = s.id LEFT JOIN ( SELECT car_id , user_id FROM edo_car_follow WHERE user_id = $userId ) f
		// 	ON c.id = f.car_id 
		// 	WHERE s.latitude > $startLati AND s.latitude < $endLati AND s.longitude > $startLongi 
		// 	AND s.longitude < $endLongi 
		// 	LIMIT 0 , 20 ";
		$sql = "SELECT c.id , c.name , c.model , p.hour_price AS price_time , p.price_km AS price_distance , s.name AS station , s.latitude , s.longitude , c.icon , 
			f.user_id 
			FROM edo_cp_car c LEFT JOIN ( select * from  edo_cp_car_price_base where type = 0 ) p ON c.id = p.car_id LEFT JOIN edo_cp_station s ON c.station = s.id LEFT JOIN ( SELECT car_id , user_id FROM edo_car_follow WHERE user_id = $userId ) f
			ON c.id = f.car_id 
			WHERE s.latitude > $startLati AND s.latitude < $endLati AND s.longitude > $startLongi 
			AND s.longitude < $endLongi AND c.delete_stat=0 
			LIMIT 0 , 20 ";
		//echo 111111111 . $sql;exit;
		$ret = $this -> mDbAgent -> getAll($sql);
		//print_r( $sql );exit;
		/*if( $argv['lati'] > 0 ){
		 $l = new Location();
		 $l->longitude = $argv['longi'];
		 $l->latitude = $argv['lati'];
		 $l->accuracy = $argv['accu'];
		 $l->altitude = $argv['alti'];
		 $l->time = $argv['ltime'];
		 $um->addUserFootprint( $um->getLoginUserId() , $l );
		 }*/

		return $ret;
	}

	public function getPreferenceCarList() {
		$userId = UserManager::getLoginUserId();
		$sql = "SELECT c.id ,c.name, c.model , cpb.hour_price , cpb.price_km , s.name AS station , c.icon 
				FROM edo_cp_car c 
				LEFT JOIN edo_cp_station s ON c.station = s.id 
				LEFT JOIN edo_cp_car_price_base cpb ON c.id=cpb.car_id
				WHERE c.id IN ( SELECT car_id FROM edo_car_follow WHERE user_id = $userId )
				AND ( cpb.type=0 OR cpb.type IS NULL ) AND c.delete_stat=0";
		$carInfo = $this -> mDbAgent -> getAll($sql);
		$carIDs = array();
		foreach ($carInfo as $v) {
			$carIDs[] = $v['id'];
		}

		// formatOrderTime
		$orders = $this -> getOrderInfoWithCarToday($carIDs);
		//格式化$orders为car_id=>orders('0'=>array('start_time'=>2342341234,'end_time'=>23131231))
		$ordersByCarID = array();
		foreach ($orders as $k => $v) {
			$ordersByCarID[$v['car_id']][] = $v;
		}

		$om = new OrderManager();
		$orderTimeArr = $om -> formatOrderTime($orders);
		foreach ($carInfo as $k => $v) {
			if (empty($ordersByCarID[$v['id']])) {
				$carInfo[$k]['orderTime'] = array();
			} else {
				$carInfo[$k]['orderTime'] = $om -> formatOrderTime($ordersByCarID[$v['id']]);
			}
		}
		//处理价格单位,转化为元
		foreach ($carInfo as $k => $v) {
			$carInfo[$k]['carId'] = $carIDs[$k]['carId'];
			$carInfo[$k]['hour_price'] = $carInfo[$k]['hour_price'] / 100;
			$carInfo[$k]['price_km'] = $carInfo[$k]['price_km'] / 100;
		}
		// print_r($carInfo);exit();
		return $carInfo;
	}

	public function follow($argv) {
		$userId = UserManager::getLoginUserId();
		if (!is_numeric($userId) || $userId < 0) {
			//throw new Exception();
			throw new Exception("", 4001);
			// return ERR_NOT_LOGIN;
		}

		$carId = $argv['car_id'];
		if (!is_numeric($carId) || $carId < 0) {
			throw new Exception("", 4000);
			// return ERR_PARAM;
		}

		$sql = "INSERT INTO edo_car_follow ( user_id , car_id ) VALUES ( $userId , {$argv['car_id']} )";
		if ($this -> mDbAgent -> query($sql)) {
			return ERR_OK;
		} else {
			//throw new Exception();
			throw new Exception("", 5002);
			// return ERR_DATABASE;
		}
	}

	/**
	 * 获取车辆当天的订单的时间信息
	 * @param  array $carIDs [description]
	 * @return array
	 */
	public function getOrderInfoWithCarToday($carIDs) {
		global $ecs;
		$where = ' 0 ';
		//今天的结束就是明天的开始
		$tommorow = strtotime(date('Y-m-d', time())) + (3600 * 24);
		$startTime = $tommorow - 3600 * 24;
		if (!empty($carIDs)) {
			foreach ($carIDs as $v) {
				$where .= " or o.car_id=" . $v;
			}
		}
		$sql = " SELECT o.order_id,o.car_id,o.order_start_time as start_time,o.order_end_time as end_time " . " FROM " . $ecs -> table('car_order') . " o " . " WHERE o.order_end_time>" . $startTime . " AND o.order_end_time<" . $tommorow . " AND (" . $where . ")" . " AND " . OrderManager::getAvailableOrderWhereSQL() . " ORDER BY o.order_start_time";
		return $this -> mDbAgent -> getAll($sql);
	}

	// function expiryDrivingLicence7Day()
	// {
	// 	$expireTime = $this->getDrivingLicenceExpire();
	// 	if ($expireTime < time()+7*24*3600)
	// 	{

	// 		return true;
	// 	}
	// 	return false;
	// }

	/**
	 * 车辆是否可用
	 * @param  [type]  $carID [description]
	 * @return boolean        [description]
	 */
	function isAvailableCar($carID) {
		global $ecs;

		$sql = " SELECT status FROM " . $ecs -> table('car') . " WHERE id=" . $carID;
		$carStatus = $this -> mDbAgent -> getOne($sql);
		//1:锁门;2:开门;3:行驶中;4:禁用
		if ($carStatus == 4) {
			return false;

		}
		return true;
	}

	function carValidation($carID) {
		if (!$this -> isAvailableCar($carID)) {
			throw new Exception("", 4016);
		}
	}

	function howManyFollowsOfcar($carID)
	{
		global $sns;
		$sql  =  " SELECT COUNT(1) FROM ".$sns->table('car_follow'). 
				 " WHERE car_id=".$carID.
				 " GROUP BY car_id ";
		$followsSum=$this->mDbAgent->getOne($sql);
		return empty($followsSum) ? 0 : $followsSum;		
	}

}
?>