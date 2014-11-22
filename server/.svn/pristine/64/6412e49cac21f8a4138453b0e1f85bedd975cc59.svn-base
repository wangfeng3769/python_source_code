<?php

class CarDeviceManager {

	/**
	 * 百度地图各个缩放比例对应的 ：每像素对应的经纬度。
	 */
	//@formatter:off
	static public $BAIDU_MAP_CONFIG = array(18 => array('lng' => 8, 'lat' => 6), 17 => array('lng' => 17, 'lat' => 13), 16 => array('lng' => 35, 'lat' => 26), 15 => array('lng' => 71, 'lat' => 52), 14 => array('lng' => 143, 'lat' => 105), 13 => array('lng' => 287, 'lat' => 210), 12 => array('lng' => 574, 'lat' => 422), 11 => array('lng' => 1149, 'lat' => 844), 10 => array('lng' => 2299, 'lat' => 1682), 9 => array('lng' => 4599, 'lat' => 3368), 8 => array('lng' => 9198, 'lat' => 7408), 7 => array('lng' => 18397, 'lat' => 14667), 6 => array('lng' => 36794, 'lat' => 25547), 5 => array('lng' => 73589, 'lat' => 58117), 4 => array('lng' => 147178, 'lat' => 72682), 3 => array('lng' => 63581058, 'lat' => 53023354)//整个中国的跨度
	);
	//@formatter:on

	static protected $DEVICE_SQL_PARAM = " d.id , d.car_id , d.longitude , d.latitude , d.sys_state , d.cts , d.vss_counter , d.software_version ";

	public function __construct() {

	}

	public function getNearbyDevice($lng, $lat, $width, $height) {
		foreach (self::$BAIDU_MAP_CONFIG as $level => $row) {
			$halfLng = $width / 2 * $row['lng'];
			$startLng = $lng - $halfLng;
			$endLng = $lng + $halfLng;

			$halfLat = $height / 2 * $row['lat'];
			$startLat = $lat - $halfLat;
			$endLat = $lat + $halfLat;

			$ret = $this -> getDeviceListByRange($startLng, $startLat, $endLng, $endLat);
			if (!empty($ret)) {
				return $ret;
			}
		}

		return array();
	}

	public function getDeviceListByRange($startLng, $startLat, $endLng, $endLat) {
		$sql = "SELECT " . self::$DEVICE_SQL_PARAM . "
			FROM car_device d
			WHERE d.latitude > $startLat AND d.latitude < $endLat AND d.longitude > $startLng 
			AND d.longitude < $endLng ";
		$db = Frm::getInstance() -> getDb();
		$ret = $db -> getAll($sql);
		if (empty($ret)) {
			return $ret;
		}

		$dl = array();
		foreach ($ret as $row) {
			$dl[] = $this -> generateCarDevice($row);
		}

		return $dl;
	}

	public function bindCar($deviceId, $carId) {
		if (!is_numeric($deviceId) || !is_numeric($carId)) {
			throw new Exception('', 4000);
		}

		$sql = "UPDATE car_device SET car_id = $carId WHERE id = $deviceId";
		$db = Frm::getInstance() -> getDb();
		$db -> execSql($sql);
	}

	public function getDeviceByCar($carId) {
		$arr = $this -> getDeviceListByCarList(array($carId));
		return $arr[$carId];
	}

	public function getDeviceListByCarList($carIdArr) {
		if (empty($carIdArr)) {
			return array();
		}

		$sqlCarId = implode(',', $carIdArr);
		$sql = "SELECT " . self::$DEVICE_SQL_PARAM . "
			FROM car_device d
			WHERE car_id IN ( $sqlCarId ) ";
		$db = Frm::getInstance() -> getDb();
		$ret = $db -> getAll($sql);
		if (empty($ret)) {
			return array();
		}

		$dArr = array();
		foreach ($ret as $row) {
			$dArr[$row['car_id']] = $this -> generateCarDevice($row);
		}

		return $dArr;
	}

	public function onDeviceStatusChange($deviceId, $arr) {
		if (!empty($arr['longitude'])) {
			$lon = $this -> changeGps($arr['longitude']);
			$lat = $this -> changeGps($arr['latitude']);

			$baiduGps = $this -> changeGps2BaiduGps($lon, $lat);
			$arr['longitude'] = (int)($baiduGps['lng'] * 1000000);
			$arr['latitude'] = (int)($baiduGps['lat'] * 1000000);
		}

		$db = Frm::getInstance() -> getDb();
		$db -> autoExecute('car_device', $arr, 'UPDATE', 'id=' . $deviceId);
	}

	protected function changeGps($df) {
		$fdf = floatval( $df );
		$d = floor ( $fdf / 100 );
		$f = $fdf - $d * 100;

		return $d + ($f / 60);
	}

	protected function changeGps2BaiduGps($lon, $lat) {
		$url = "http://api.map.baidu.com/ag/coord/convert?from=0&to=4&x=$lon&y=$lat";
		$ret = file_get_contents($url);
		$obj = json_decode($ret);

		return array('lng' => base64_decode($obj->x), 'lat' => base64_decode($obj->y));
	}

	protected function generateCarDevice($row) {
		require_once (Frm::$ROOT_PATH . 'resource/classes/CarDevice.php');
		$d = new CarDevice();
		$d -> id = $row['id'];
		$d -> carId = $row['car_id'];
		$d -> sysState = $row['sys_state'];
		$d -> longitude = $row['longitude'];
		$d -> latitude = $row['latitude'];
		$d -> cts = $row['cts'];
		$d->vss_counter = $row['vss_counter'];
		$d->software_version = $row['software_version'];

		return $d;
	}

}
?>