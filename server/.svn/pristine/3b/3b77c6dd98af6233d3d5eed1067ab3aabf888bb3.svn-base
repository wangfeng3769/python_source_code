<?php
class CarManager {
	public function __construct() {
	}
	
	/**
	 *
	 * @param int $carId        	
	 * @return Car
	 */
	public function getCar($carId) {
		$ret = $this->getCarList ( array (
				$carId 
		) );
		return $ret [0];
	}
	/*add by matao 2013.6.24
	  根据车牌号查找车辆ID
	*/
    public function getCarIdByNumber($carNumber) 
    {
    	//$carNumber = trim($carNumber);
    	$sql = "SELECT c.id 
    	FROM edo_cp_car AS c 
    	WHERE c.number='$carNumber'";

		$db = Frm::getInstance ()->getDb ();
		$ret = $db->getOne($sql);
		return $ret;
	}
    /*add by matao*/
	public function getCarList($idArr) {
		if (empty ( $idArr )) {
			throw new Exception ( '', 4000 );
		}
		
		$sqlId = implode ( ',', $idArr );
		//modify by yangbei 增加车辆下线判断
		// $sql = "SELECT c.id , c.status , c.model, c.name , c.gearbox , s.name AS station_name , c.brand , c.icon , 
		// 	c.number , c.mile_factor 
		// 	FROM edo_cp_car c LEFT JOIN edo_cp_station s ON c.station = s.id 
		// 	WHERE c.id IN ($sqlId)";
		$sql = "SELECT c.id , c.status , c.model, c.name , c.gearbox , s.name AS station_name , c.brand , c.icon , 
			c.number , c.mile_factor 
			FROM edo_cp_car c LEFT JOIN edo_cp_station s ON c.station = s.id 
			WHERE c.id IN ($sqlId) AND c.delete_stat=0";	
		
		$db = Frm::getInstance ()->getDb ();
		$ret = $db->getAll ( $sql );
		
		$carArr = array ();
		require_once (Frm::$ROOT_PATH . 'resource/classes/Car.php');
		foreach ( $ret as $row ) {
			$car = new Car ();
			$car->id = $row ['id'];
			$car->status = $row ['status'];
			$car->gearbox = $row ['gearbox'];
			$car->name = $row ['name'];
			$car->number = $row ['number'];
			$car->brand = $row ['brand'];
			$car->model = $row ['model'];
			$car->icon = $row ['icon'];
			$car->station = $row ['station_name'];
			$car->mile = $row ['mile'];
			$car->mileFactor = $row ['mile_factor'];
			
			$carArr [] = $car;
		}
		
		return $carArr;
	}
	public function getCarListByPage($pageNo, $pageSize = 30) {
		if( empty( $pageNo )){
			$pageNo = 1;
		}
		if (! is_numeric ( $pageNo ) || ! is_numeric ( $pageSize ) || $pageSize <= 0) {
			throw new Exception ( '', 4000 );
		}
		
		$start = ($pageNo - 1) * $pageSize;
		if( $start < 0 ){
			$start = 0;
		}
		
		$sqlId = implode ( ',', $idArr );
		$sql = "SELECT c.id , c.status , c.model, c.name , c.gearbox , s.name AS station_name , c.brand , c.icon ,
			c.number , c.mile_factor
			FROM edo_cp_car c LEFT JOIN edo_cp_station s ON c.station = s.id 
			WHERE c.delete_stat=0";
		
		$db = Frm::getInstance ()->getDb ();
		$ret = $db->limitSelect ( $sql, $start, $pageSize );
		
		return $ret;
	}
	
	/**
	 * 获取里指定点最近的站点
	 *
	 * @param [type] $lng
	 *        	[description]
	 * @param [type] $lat
	 *        	[description]
	 * @return [type] [description]
	 */
	public function getNearestStation($lng, $lat, $limit = 1) {
		$db = Frm::getInstance ()->getDb ();
		
		$sql = " SELECT * ,(abs({$lng}-longitude)+abs($lat-latitude)) as min 
				FROM edo_cp_station s ORDER BY min ";
		$ret = $db->limitSelect ( $sql, 0, $limit );
		
		$stations = array ();
		foreach ( $ret as $v ) {
			$stations [$v ['id']] = $v;
		}
		return $stations;
	}
	public function getStationsCars($stationIds) {
		$where = '0';
		foreach ( $stationIds as $v ) {
			$where .= ' OR s.id=' . $v;
		}
		
		$sql = "  SELECT c.*,s.name as station_name,s.longitude,s.latitude,s.city,s.radius
				FROM  edo_cp_station s 
				LEFT JOIN edo_cp_car c ON c.station=s.id 
				WHERE c.station<>4 AND s.is_show=1 AND (" . $where . ")";
		$db = Frm::getInstance ()->getDb ();
		
		$ret = $db->getAll ( $sql );
		$carArr = array ();
		// 组织成stationId=>carArr的形式
		foreach ( $ret as $v ) {
			// $carArr[$v['station']] = empty($carArr[$v['station']]) ?
			// array('station_name'=>$v['station_name'],'longitude'=>$v['longitude'],'latitude'=>$v['latitude'],'city'=>$v['city'],'radius'=>$v['radius'])
			// : ;
			if (! isset ( $carArr [$v ['station']] )) {
				$carArr [$v ['station']] = array (
						'station_name' => $v ['station_name'],
						'longitude' => $v ['longitude'],
						'latitude' => $v ['latitude'],
						'city' => $v ['city'],
						'radius' => $v ['radius'] 
				);
			}
			$carArr [$v ['station']] [$v ['id']] = $v;
			$carIds [] = $v ['id'];
		}
		
		// 增加车辆价格信息显示
		require_once (Frm::$ROOT_PATH . 'order_charge/classes/PriceManager.php');
		$pm = new PriceManager ();
		$carsPirceArr = $pm->getCarArrPriceRange ( $carIds );
		
		foreach ( $carArr as $k => $c ) {
			foreach ( $c as $key => $value ) {
				if (is_numeric ( $key ) == $key) {
					$carArr [$k] [$key] ['milePrice'] = $carsPirceArr [$key] ['milePrice'];
					$carArr [$k] [$key] ['timePriceMin'] = $carsPirceArr [$key] ['timePriceMin'];
					$carArr [$k] [$key] ['timePriceMax'] = $carsPirceArr [$key] ['timePriceMax'];
				}
			}
		}
		
		return $carArr;
	}

	function getCarIdsByCarGroupId($carGroupId)
	{
		$db = Frm::getInstance ()->getDb ();
		
		$sql = " SELECT c.id FROM edo_cp_car c WHERE c.group='$carGroupId' AND c.delete_stat=0";
		$ret = $db->getAll($sql);

		$carIds=array();
		foreach ($ret as $v) {
			$carIds[]=$v['id'];

		}

		return $carIds;
	}

	function getTwCarGroupIdByTitle($title)
	{
		$db = Frm::getInstance ()->getDb ();
		
		$sql = " SELECT c.car_group_id FROM edo_cp_car_group c WHERE c.car_group_name LIKE '%$title%' ";
		$carGroupId = $db->getOne($sql);

		return $carGroupId;
	}

	function isCarInTwGroup($carId)
	{	
		$twCarGroupId=$this->getTwCarGroupIdByTitle('天物');
		if (empty($twCarGroupId)) {
			return false;
		}
		$twCarIds=$this->getCarIdsByCarGroupId($twCarGroupId);

		if (in_array($carId, $twCarIds)) {
			return true;
		}
		return false;
	}

	function twGetCarsIdByTitle($title)
	{
		$carGroupId=$this->getTwCarGroupIdByTitle($title);
		return $this->getCarIdsByCarGroupId($carGroupId);
	}
}
?>