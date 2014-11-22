<?php
set_time_limit(0);
if (!defined('ROOT_PATH')) {
	define('IN_ECS', true);
	require (dirname(__FILE__) . '/../../cp/includes/init.php');
}

class RentComputer {

	static private $Workday = 0;
	static private $Holiday = 1;

	static private $PriceStrategyTop = 1;
	static private $PriceStrategySpec = 0;

	/**
	 * @var cls_mysql
	 */
	private $mDbAgent = null;
	private $mEcs = null;

	/**
	 * 不完整天数组，其实就是记录开始和结束那两天。如果这两天的价格和超过某一天的封顶价，则这一天要按封顶价算(*时间比例)。
	 *
	 * @var array
	 */
	protected $mImperfectDayArr = array();

	public function RentComputer() {
		global $db, $ecs;

		$this -> mDbAgent = $db;
		$this -> mEcs = $ecs;
	}

	public function computeRent($order) {
		$rent = $this -> computeRentDetail($order);

		return $rent['kmRent'] + $rent['timeRent'];
	}

	/**
	 * @param Order $order
	 */
	public function computeRentDetail($order) {
		$tEndTime = $order -> realEndTime;
		if ($order -> endTime > $tEndTime) {
			$tEndTime = $order -> endTime;
		}

		$vacationArr = $this -> getOrderVacation($order -> startTime, $tEndTime);
		//计算封顶价时要用到
		$basePrice = $this -> getCarBasePrice($order -> carId);
		//一次选出来，避免在循环里多次查询数据库
		$priceStrategy = $this -> getAllPricingStrategy($order -> carId, $order -> startTime, $tEndTime);

		$timeRent = $this -> computeOrderTimeRent($order, $order -> startTime, $tEndTime, $vacationArr, $basePrice, $priceStrategy);

		return array("kmRent" => ($order -> mile - $timeRent['containKm']) * $basePrice[self::$Workday]['price_km'], "timeRent" => $timeRent['rent']);
	}

	protected function getOrderVacation($tStartTime, $tEndTime) {
		$sd = date('Y-m-d', $tStartTime);
		$ed = date('Y-m-d', $tEndTime);

		$sql = "SELECT holiday FROM " . $this -> mEcs -> table("vacation") . " WHERE holiday >= '$sd' AND holiday <= '$ed' ";
		$ret = $this -> mDbAgent -> getAll($sql);

		$arr = array();
		for ($i = 0; $i < count($ret); ++$i) {
			$arr[] = $ret[$i]['holiday'];
		}

		return $arr;
	}

	public function getCarBasePrice($carId) {
		$sql = "SELECT id , hour_price , hour_km , top_price , top_km , price_km , price_time_out , type FROM " . $this -> mEcs -> table("car_price_base") . " WHERE car_id = $carId";
		$ret = $this -> mDbAgent -> getAll($sql);

		$arr = array();
		for ($i = 0; $i < count($ret); ++$i) {
			if (self::$Workday == $ret[$i]['type']) {
				$arr[self::$Workday] = $ret[$i];
			} else if (self::$Holiday == $ret[$i]['type']) {
				$arr[self::$Holiday] = $ret[$i];
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
			'' AS date_end FROM " . $this -> mEcs -> table("car_price_detail") . 
			" WHERE car_id = $carId " . 
			" UNION " . 
			" SELECT 1 AS istop, car_id, time_from, time_end, period_top AS price, contain_km, type, 
			date_from , date_end FROM " . $this -> mEcs -> table("car_price_top") . 
			" WHERE car_id = $carId AND 
			( ( date_from >= '$sd' AND date_from <= '$ed' ) OR ( date_from <= '$sd' AND date_end >= '$sd' ) )
			 ORDER BY date_from , time_from ";
		//@formatter:on
		$ret = $this -> mDbAgent -> getAll($sql);
		for ($i = 0; $i < count($ret); ++$i) {
			$day = self::$Workday;
			$priceStrategyType = self::$PriceStrategySpec;
			if (self::$Holiday == $ret[$i]['type']) {
				$day = self::$Holiday;
			}
			if (self::$PriceStrategyTop == $ret[$i]['istop']) {
				$priceStrategyType = self::$PriceStrategyTop;
			}

			$arr[$day][$priceStrategyType][] = $ret[$i];
		}

		return $arr;
	}

	/**
	 * 计算时间租金
	 * $order Order
	 */
	protected function computeOrderTimeRent($order, $tStartTime, $tEndTime, $vacationArr, $basePrice, $priceStrategy) {
		$imperfectDayArr = array();
		$rent = array('rent' => 0, 'containKm' => 0);

		$sStartDate = date('Y-m-d', $tStartTime);
		$sEndDate = date('Y-m-d', $tEndTime);
		$st = $tStartTime;
		$tDayTerminal = 0;
		while ($tEndTime > $tDayTerminal) {
			//1.取出下一天
			$sDay = date('Y-m-d', $st);
			$tDayTerminal = strtotime("$sDay 23:59:59");
			$et = $tDayTerminal > $tEndTime ? $tEndTime : $tDayTerminal;

			$isHoliday = in_array($sDay, $vacationArr);
			$dayType = $isHoliday ? self::$Holiday : self::$Workday;

			$r = $this -> computeOneDayTimeRent($st, $et, $basePrice[$dayType], $priceStrategy[$dayType]);

			//2.取得日封顶价进行判断
			$topPrice = $basePrice[$dayType]['top_price'];
			if ($r['rent'] > $topPrice) {
				$rent['rent'] += $topPrice;
				$rent['containKm'] += $basePrice[$dayType]['top_km'];
			} else {
				//3.记录不完整天(只记录没有超过封顶价的)。如果当前是开始或结束那天，肯定是不完整天（也只有这两天是）
				if ($sDay == $sStartDate || $sDay == $sEndDate) {
					$tImperfactDayStart = $tStartTime;
					$tImperfactDayEnd = $tEndTime;
					if ($sDay == $sStartDate) {
						if ($sDay != $sEndDate) {
							$tImperfactDayEnd = $tDayTerminal + 1;
						}
					} else {//$sDay == $sEndDate
						$tImperfactDayStart = strtotime($sDay);
					}

					//@formatter:off
					$imperfectDayArr[] = array('st' => $tImperfactDayStart, 'et' => $tImperfactDayEnd, 'rent' => $r,
					'isHoliday' => $isHoliday, 'topPrice' => $basePrice[$dayType]['top_price'],
					'topKm' => $basePrice[$dayType]['top_km']);
				} else {//是租了一整天
					$rent['rent'] += $r['rent'];
					$rent['containKm'] += $r['containKm'];
				}
			}

			//4.如果还有下一天，取出下一天
			$st = $tDayTerminal + 1;
		}

		$imperfectDayRent = $this -> computeImperfectDayRent($imperfectDayArr);
		$rent['rent'] += $imperfectDayRent['rent'];
		$rent['containKm'] += $imperfectDayRent['containKm'];

		return $rent;
	}

	/**
	 * 计算指定时间段的时间租金，指定时间段必须在一天内
	 * @param $priceStrategy 二维数组 ， 一维表示封顶价或特殊时段价
	 */
	protected function & computeOneDayTimeRent(&$st, $et, $basePrice, $priceStrategy) {
		if (date('Y-m-d', $st) != date('Y-m-d', $et)) {
			return -1;
		}

		$rent = array('rent' => 0, 'containKm' => 0);

		$topStt = $priceStrategy[self::$PriceStrategyTop];
		$specStt = $priceStrategy[self::$PriceStrategySpec];

		while ($et - $st > 59) {//$st是精确到分的
			//2.判断是否在封顶时间段内
			$hst = date('G:i', $st);
			$stDate = date('Y-m-d', $st);
			$inTop = false;
			for ($i = 0; $i < count($topStt); ++$i) {
				//判断是否在封顶时间段有效期内
				$sTopDayEnd = $topStt[$i]['time_end'];
				if( 0 == $sTopDayEnd ){
					$sTopDayEnd = 24;
				}

				if ($stDate >= $topStt[$i]['date_from'] && $stDate <= $topStt[$i]['date_end']) {
					if ($this -> timeCompare($hst, $topStt[$i]['time_from']) >= 0 && $this -> timeCompare($hst,$sTopDayEnd) < 0) {
						$inTop = true;

						break;
					}
				}
			}
			if ($inTop) {
				$r = $this -> computeTopTimeSlice($st, $et, $basePrice, $topStt[$i], $specStt);
			} else {
				//取下一个封顶时间段
				$topTimeSlice = null;
				for ($i = 0; $i < count($topStt); ++$i) {
					if ($this -> timeCompare($hst, $topStt[$i]['time_from']) < 0) {
						$topTimeSlice = $topStt[$i];
						break;
					}
				}
				$r = $this -> computeCommonTimeSlice($st, $et, $basePrice, $topTimeSlice, $specStt);
			}
			$rent['rent'] += $r['rent'];
			$rent['containKm'] += $r['containKm'];
		}

		return $rent;
	}

	/**
	 * 计算封顶时间段内的价格
	 */
	protected function & computeTopTimeSlice(&$st, $et, $basePrice, $topSlice, $specStt) {
		$rent = array('rent' => 0, 'containKm' => 0);

		//整个封顶时间段里包含了很多特殊时间段和基本时间段，要把这些时间段都计算出来和封顶时间段比较
		while (true) {
			$r = $this -> computeCommonTimeSlice($st, $et, $basePrice, $topSlice, $specStt);
			$rent['rent'] += $r['rent'];
			$rent['containKm'] += $r['containKm'];

			$het = date('G:i', $st);
			//5.判断是否超过封顶时间段结束时间
			$sEndTopHour = $topSlice['time_end'];
			if( 0 == $sEndTopHour ){
				$sEndTopHour = 24;
			}
			if ($this -> timeCompare($het, $sEndTopHour) >= 0 || "0:00" == $het || $het == date( 'G:i' , $et ) ) {
				break;
			}
		}

		//6.判断是否超过封顶时间价
		if ($rent['rent'] > $topSlice['price']) {
			$rent['rent'] = $topSlice['price'];
			$rent['containKm'] = $topSlice['contain_km'];
		}

		return $rent;
	}

	/**
	 * 计算非封顶时间段价格。该时间段包括基本价时间段和特殊价时间段
	 *
	 * @param int $st 目前要计算的开始时间
	 * @param int $et 订单结束时间
	 * @param array $basePrice
	 * @param array $topStt 最近的一个封顶时间段。如果在封顶时间段内，就是这个封顶时间段，如果不在，就是下一个封顶时间段
	 * @param array $specStt
	 */
	protected function & computeCommonTimeSlice(&$st, $et, $basePrice, $topStt, $specStt) {
		$rent = array('rent' => 0, 'containKm' => 0);

		$hst = date('G:i', $st);
		$het = date('G:i', $et);

		//1.判断是否在特殊时间段内，如果在，调整结束时间。如果不在，取下一个特殊时间，再调整结束时间
		$specIndex = $this -> procEndTimeInSpec($st, $et, $hst, $het, $specStt);

		//2.如果在封顶时间段内，取封顶时间段的结束时间。否则，取下一个封顶时间段的开始时间
		if (!empty($topStt)) {
			if ($this -> timeCompare($hst, $topStt['time_from']) < 0) {//开始时间在封顶时间开始之前
				if ($this -> timeCompare($het, $topStt['time_from']) > 0) {
					//3.如果结束时间在封顶时间段内，结束时间置为封顶开始时间
					$het = $topStt['time_from'] . ":00";
				}
			} else {//开始时间在封顶时间开始之后(在封顶时间段内)
				//前面已经判断，所以，到这儿，封顶时间结束肯定在开始时间之后，即$st在封顶时间段内
				$tTopTimeSliceEnd = (int)($topStt['time_end']);
				if( 0 == $tTopTimeSliceEnd ){
					$tTopTimeSliceEnd = 24;
				}

				if( $this->timeCompare( $het , $tTopTimeSliceEnd ) > 0 ){
					if( 24 == $tTopTimeSliceEnd ){
						$het = "23:59";
					}else{
						$het =  $tTopTimeSliceEnd . ":00";
					}
				}
			}
		}

		if ("23:59" == $het) {
			$et = strtotime(date('Y-m-d', $et) . " " . $het) + 60;
		} else {
			$et = strtotime(date('Y-m-d', $et) . " " . $het);
		}

		//3.按特殊或基本价计算价格和
		$unitPrice = $basePrice['hour_price'];
		$unitKm = $basePrice['hour_km'];
		if ($specIndex > -1) {
			$unitPrice = $specStt[$specIndex]['price'];
			$unitKm = $specStt[$specIndex]['contain_km'];
		}
		$total = ($et - $st) / 3600;
		$rent['rent'] = $unitPrice * $total;
		$rent['containKm'] = $unitKm * $total;

		//4.
		$st = $et;

		return $rent;
	}

	/**
	 * 判断是否在特殊时间段内，如过在，调整结束时间。如果不在，取下一个特殊时间，再调整结束时间
	 *
	 * @param int $st
	 * @param int $et
	 * @param string $hst
	 * @param string $het
	 * @param array $specStt
	 * @return int
	 */
	protected function procEndTimeInSpec($st, $et, $hst, &$het, $specStt) {
		$specIndex = $this -> getSpecTimeSlice($st, $specStt);
		if ($specIndex > -1) {//在特殊时间段内
			if ($this -> timeCompare($het, $specStt[$specIndex]['time_end']) > 0) {
				if( 0 == $specStt[$specIndex]['time_end'] ){
					$het = "23:59";
				}else{
					$het = $specStt[$specIndex]['time_end'] . ":00";
				}
			}
		} else {
			$nextSpecIndex = $this->getNextSpecTimeSlice( $st , $specStt );
			if ($nextSpecIndex > -1) {
				$nextTimeFrom = $specStt[$nextSpecIndex]['time_from'];
				//下一个特殊时间，其开始时间肯定大于$hst
				if ($this -> timeCompare($het, $nextTimeFrom) > 0 && $this -> timeCompare($hst, $nextTimeFrom) < 0) {
					$het = $nextTimeFrom . ":00";
				}
			}
		}

		return $specIndex;
	}

	protected function getSpecTimeSlice($st, $specStt) {
		$hst = date('G:i', $st);

		for ($i = 0; $i < count($specStt); ++$i) {
			if ($this -> timeCompare($hst, $specStt[$i]['time_from']) >= 0 ){
				if( $this -> timeCompare($hst, $specStt[$i]['time_end']) < 0 || ( $specStt[$i]['time_from'] > $specStt[$i]['time_end'] && 0 == $specStt[$i]['time_end'] ) ) {
					return $i;
				}
			}
		}

		return -1;
	}

	protected function getNextSpecTimeSlice( $st , $specStt){
		$hst = date('G:i', $st);

		for ($i = 0; $i < count($specStt); ++$i) {
			if ($this -> timeCompare($hst, $specStt[$i]['time_from']) < 0 ) {
				if( 0 == $i ){
					return $i;
				}else if( $this -> timeCompare($hst, $specStt[$i-1]['time_end']) >= 0 ){//大于上一个特殊时间的结束时间
					return $i;
				}
			}
		}

		return -1;
	}

	/**
	 * 比较"7:42"与整点时间（"9"）的大小。
	 *
	 * @param string $t1 没有前导零的分钟与秒数的表示
	 * @param string $t2 整点表示
	 */
	protected function timeCompare($t1, $t2) {
		$arr = explode(":", $t1);
		if ($arr[0] == $t2) {
			if ($arr[1] == "00") {
				return 0;
			} else {
				return $arr[1] > "00" ? 1 : -1;
			}
		} else {
			return $arr[0] > $t2 ? 1 : -1;
		}
	}

	protected function computeImperfectDayRent($imperfectDayArr) {
		$rent = array('rent' => 0, 'containKm' => 0);

		if (1 == count($imperfectDayArr)) {
			return $imperfectDayArr[0]['rent'];
		} else {
			$total = $imperfectDayArr[0]['rent']['rent'] + $imperfectDayArr[1]['rent']['rent'];
			for ($i = 0; $i < count($imperfectDayArr); ++$i) {
				if ($total > $imperfectDayArr[$i]['topPrice']) {
					$percent = ($imperfectDayArr[$i]['et'] - $imperfectDayArr[$i]['st']) / 86400;
					$percentTopPrice = $imperfectDayArr[$i]['topPrice'] * $percent;
					if( $percentTopPrice < $imperfectDayArr[$i]['rent']['rent'] ){
						$rent['rent'] += $imperfectDayArr[$i]['topPrice'] * $percent;
						$rent['containKm'] += $imperfectDayArr[$i]['topKm'] * $percent;
					}else{
						$rent['rent'] += $imperfectDayArr[$i]['rent']['rent'];
						$rent['containKm'] += $imperfectDayArr[$i]['rent']['containKm'];
					}
				} else {
					$rent['rent'] += $imperfectDayArr[$i]['rent']['rent'];
					$rent['containKm'] += $imperfectDayArr[$i]['rent']['containKm'];
				}
			}
		}

		return $rent;
	}

	/**
	 * 取得已冻结的违章押金
	 */
	public function getFrozenIllegalDeposit() {
		$userId = UserManager::getLoginUserId();

		global $sns, $db;
		// $sql = "SELECT vd.money FROM " . $sns -> table("violate_deposit_log") . " vd WHERE vd.user_id = $userId AND vd.status = 0";
		//0表示未结算
		$sql = " SELECT sum(money)
	            FROM " . $sns -> table('violate_deposit_log') . 
	            " WHERE transaction_number NOT IN ( " .
	            " SELECT transaction_number FROM " . $sns -> table('violate_deposit_log') .
	            " WHERE status = 1 )  AND user_id=$userId ".
	            " GROUP BY user_id ";
	            return $db -> getOne($sql);
	}

}
?>