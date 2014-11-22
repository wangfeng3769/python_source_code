<?php

class PriceManager {

	public function getCarBasePrice($carId) {
		$db = Frm::getInstance() -> getDb();
		$sql = "SELECT id , hour_price , hour_km , top_price , top_km , price_km , price_time_out , type FROM edo_cp_car_price_base WHERE car_id = $carId";
		$ret = $db -> getAll($sql);
		//添加小时租金折扣计算,add by golf

		//end
		require_once (Frm::$ROOT_PATH . 'system/classes/HolidayManager.php');
		$arr = array();
		for ($i = 0; $i < count($ret); ++$i) {
			if (HolidayManager::$WORKDAY == $ret[$i]['type']) {
				$arr[HolidayManager::$WORKDAY] = $ret[$i];
			} else if (HolidayManager::$HOLIDAY == $ret[$i]['type']) {
				$arr[HolidayManager::$HOLIDAY] = $ret[$i];
			}
		}

		return $arr;
	}

	/**
	 * 取得所有订价策略
	 * @return array 三维数组 。第一维代表是否节假日，第二维代表封顶时段或特殊时段
	 */
	protected function getAllPricingStrategy($carId, $tStartTime, $tEndTime) {
		$sd = date('Y-m-d', $tStartTime);
		$ed = date('Y-m-d', $tEndTime);

		$arr = array();

		//@formatter:off
		$sql = "SELECT 0 AS istop, car_id, time_from, time_end, price, contain_km, type , '' AS date_from , 
			'' AS date_end FROM edo_cp_car_price_detail" . 
			" WHERE car_id = $carId " . 
			" UNION " . 
			" SELECT 1 AS istop, car_id, time_from, time_end, period_top AS price, contain_km, type, 
			date_from , date_end FROM edo_cp_car_price_top " .
 			" WHERE car_id = $carId AND 
			( ( date_from >= '$sd' AND date_from <= '$ed' ) OR ( date_from <= '$sd' AND date_end >= '$sd' ) )
			 ORDER BY date_from , time_from ";
		//@formatter:on
		$db = Frm::getInstance() -> getDb();
		$ret = $db -> getAll($sql);
		for ($i = 0; $i < count($ret); ++$i) {
			$day = HolidayManager::$WORKDAY;
			$priceStrategyType = self::$PriceStrategySpec;
			if (HolidayManager::$HOLIDAY == $ret[$i]['type']) {
				$day = HolidayManager::$HOLIDAY;
			}
			if (self::$PriceStrategyTop == $ret[$i]['istop']) {
				$priceStrategyType = self::$PriceStrategyTop;
			}

			$arr[$day][$priceStrategyType][] = $ret[$i];
		}

		return $arr;
	}

	public function getConfigHourKm() {
		$db = Frm::getInstance() -> getDb();
		$sql = " SELECT code , value " . " FROM edo_cp_shop_config WHERE code='hour_km' OR code='day_km'";
		$ret = $db -> getAll($sql);

		$kmInfo = array();
		foreach ($ret as $row) {
			if ('hour_km' == $row['code']) {
				$kmInfo['hour_km'] = $row['value'];
			} else {
				$kmInfo['day_km'] = $row['value'];
			}
		}
		return $kmInfo;
	}

	public function getCarArrPriceRange($carIdArr) {
		if (empty($carIdArr)) {
			throw new Exception('', 4000);
		}

		$priceArr = array();

		$sqlId = implode(',', $carIdArr);
		$sql = "SELECT car_id , hour_price , type , price_km FROM edo_cp_car_price_base WHERE car_id IN( $sqlId )";
		$db = Frm::getInstance() -> getDb();
		$ret = $db -> getAll($sql);
		require_once (Frm::$ROOT_PATH . 'system/classes/HolidayManager.php');
		//添加小时租金折扣计算,add by golf

		//end
		foreach ($ret as $row) {
			$priceArr[$row['car_id']]['allTimePrice'][] = $row['hour_price'];
			if (HolidayManager::$WORKDAY == $row['type']) {
				$priceArr[$row['car_id']]['milePrice'] = $row['price_km'];
			}
		}

		$sql = "SELECT car_id , price FROM edo_cp_car_price_detail WHERE car_id IN ( $sqlId )";
		$ret = $db -> getAll($sql);
		foreach ($ret as $row) {
			$priceArr[$row['car_id']]['allTimePrice'][] = $row['price'];
		}

		foreach ($priceArr as &$row) {
			sort($row['allTimePrice']);
			$row['timePriceMin'] = $row['allTimePrice'][0];
			$row['timePriceMax'] = $row['allTimePrice'][count($row['allTimePrice']) - 1];
			unset($row['allTimePrice']);
		}

		return $priceArr;
	}

	/**
	 * 某时间区间的每日封顶价,时间区间往后多加两天
	 * @param  [type] $carId      [description]
	 * @param  [type] $tStartTime [unixTime]
	 * @param  [type] $tEndTime   [unixTime]
	 * @return [type]             date为key,价格为value
	 */
	public function getCarTopPriceDayArr($carId, $tStartTime, $tEndTime) {

		$step = 3600 * 24;
		$tEndTime += $step + 3600 * 24 + 1;

		require_once (Frm::$ROOT_PATH . 'system/classes/HolidayManager.php');
		$hm = new HolidayManager();
		$holidays = $hm -> getHolidayArr($tStartTime, $tEndTime);

		$ret = $this -> getCarBasePrice($carId);

		$topPriceArr = array();
		foreach ($ret as $v) {
			if ($v['type'] == 1) {
				$topPriceArr[1] = $v['top_price'];
			} else {
				$topPriceArr[0] = $v['top_price'];
			}
		}

		$topPriceDayArr = array();
		for ($i = $tStartTime; $i < $tEndTime; $i += $step) {
			$sDate = date('Y-m-d', $i);
			if (in_array($sDate, $holidays)) {
				$topPriceDayArr[] = array('date' => $sDate, 'price' => $topPriceArr[1]);

			} else {
				$topPriceDayArr[] = array('date' => $sDate, 'price' => $topPriceArr[0]);
			}
		}
		return $topPriceDayArr;
	}

	/**
	 * 取得每天的基本时租金。按给定的起始时间把每天的数据都列出来，并包含开始时间的前一天和
	 */
	public function getHourPriceInDate($carId, $startTime, $endTime, $basePrice, $holidayArr) {
		$tStartDate = strtotime(date('Y-m-d', $startTime)) - 86400;
		$tEndDate = strtotime(date('Y-m-d', $endTime)) + 86400;
		//算法函数需要加2天
		$tDate = $tStartDate;
		$retArr = array();
		while ($tDate <= $tEndDate) {
			$sDate = date('Y-m-d', $tDate);
			if (in_array($sDate, $holidayArr)) {
				$retArr[] = array('date' => $sDate, 'price' => $basePrice[HolidayManager::$HOLIDAY]['hour_price']);
			} else {
				$retArr[] = array('date' => $sDate, 'price' => $basePrice[HolidayManager::$WORKDAY]['hour_price']);
			}

			$tDate += 86400;
		}

		return $retArr;
	}

	public function getPeriodTopInDate($carId, $startTime, $endTime, $holidayArr) {
		$pricing = $this -> getAllPricingStrategy($carId, $startTime, $endTime);

		$tStartDate = strtotime(date('Y-m-d', $startTime)) - 86400;
		$tEndDate = strtotime(date('Y-m-d', $endTime)) + 86400;
		$tDate = $tStartDate;
		$retArr = array();
		while ($tDate <= $tEndDate) {
			$sDate = date('Y-m-d', $tDate);
			$oneP = null;
			if (in_array($sDate, $holidayArr)) {
				$oneP = $pricing[HolidayManager::$HOLIDAY][1];

			} else {
				$oneP = $pricing[HolidayManager::$WORKDAY][1];
			}

			$tDate += 86400;

			if (empty($oneP)) {
				continue;
			}
			$retArr[] = array('date' => $sDate, 'timeS' => $oneP['time_from'], 'timeE' => $oneP['time_end'], 'price' => $oneP['period_top']);
		}

		return $retArr;
	}

	public function getUserGroupDiscount($groupId)
	{
		if (empty($groupId)) {
			return array();
		}
		
		$db = Frm::getInstance() -> getDb();
		$sql = "SELECT * FROM user_group_discount  WHERE group_id=$groupId";
		$ret = $db -> getRow($sql);
		if (empty($ret)) {
			return array('discount'=>100);
		}
		return $ret;
	}

	//批量设置用户群组折扣
	public function setUserGroupsHourRentDiscount($groupIds,$discountArr)
	{
		$db = Frm::getInstance() -> getDb();
	}

	//用户群组折扣设置
	public function setUserGroupHourRentDiscount($groupId,$discount)
	{
		$this->setUserGroupsHourRentDiscount(array($groupId),array($discount));
	}

}
?>