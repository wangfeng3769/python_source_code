<?php
/**
 * 车辆位置管理。该位置，用来在地图上找车时显示，也用于鸣笛找车时显示。
 */
class CarPosManager {

	public function __construct() {

	}

	public function getNearbyCarArr($lng, $lat, $width, $height) {
		require_once (Frm::$ROOT_PATH . 'resource/classes/CarDeviceManager.php');
		foreach (CarDeviceManager::$BAIDU_MAP_CONFIG as $level => $row) {
			$halfLng = $width / 2 * $row['lng'];
			$startLng = $lng - $halfLng;
			$endLng = $lng + $halfLng;

			$halfLat = $height / 2 * $row['lat'];
			$startLat = $lat - $halfLat;
			$endLat = $lat + $halfLat;

			$ret = $this -> getCarListByRange($startLng, $startLat, $endLng, $endLat);
			if (!empty($ret)) {
				return $ret;
			}
		}

		return array();
	}

	public function getCarListByRange($startLng, $startLat, $endLng, $endLat) {
		$sql = "SELECT p.FCar_id , p.FLongitude , p.FLatitude FROM T_Car_Pos p
			WHERE p.flatitude > $startLat AND p.flatitude < $endLat AND p.flongitude > $startLng 
			AND p.flongitude < $endLng ";
		$db = Frm::getInstance() -> getDb();
		$ret = $db -> getAll($sql);

		//邬国轩 增加特定车辆群组过滤功能
		require_once (Frm::$ROOT_PATH . 'resource/classes/CarGroupManager.php');
		$cgm=new CarGroupManager();
		$bannerCarIds=$cgm->getBanerCarIds(UserManager::getLoginUserId());

		foreach ($ret as $k => $v) {
			if ( in_array($v['FCar_id'], $bannerCarIds)) {
				unset($ret[$k]);
			}
		}
		//邬国轩 增加特定车辆群组过滤功能


		if (empty($ret)) {
			return array();
		}

		$carIdArr = array();
		$carIdIndexPosArr = array();
		foreach ($ret as $row) {
			$carIdArr[] = $row['FCar_id'];
			$carIdIndexPosArr[$row['FCar_id']] = $row;
		}

		require_once (Frm::$ROOT_PATH . 'resource/classes/CarManager.php');
		$cm = new CarManager();
		$carArr = $cm -> getCarList($carIdArr);

		foreach ($carArr as $car) {
			$car -> longitude = $carIdIndexPosArr[$car -> id]['FLongitude'];
			$car -> latitude = $carIdIndexPosArr[$car -> id]['FLatitude'];
		}

		return $carArr;
	}

	function getCarParkPosition($carId)
	{
		$sql = "SELECT p.FCar_id as car_id, p.FLongitude as longitude , p.FLatitude  as latitude, p.FRadius as radius,c.name FROM T_Car_Pos p
				LEFT JOIN edo_cp_car c ON c.id=p.FCar_id
			WHERE p.FCar_id=".$carId;
			
		$db = Frm::getInstance() -> getDb();
		$ret = $db -> getRow($sql);
		if (empty($ret)) {
			return array();
		}
		return $ret;
	}
}
?>