<?php

class DepositComputer {

	public function getUseDeposit($userId, $carId, $startTime, $endTime) {
		require_once (Frm::$ROOT_PATH . 'order_charge/classes/PriceManager.php');
		$pm = new PriceManager();
		$basePrice = $pm -> getCarBasePrice($carId);
		$kmPrice = $basePrice[0]['price_km'];

		$dateTopPrice = $pm -> getCarTopPriceDayArr($carId, $startTime, $endTime);
		
		//add by golf 对时间租金进行打折
		require_once (Frm::$ROOT_PATH . 'order_charge/classes/PriceManager.php');
		$um=new UserManager();
		$user=$um->getUser($userId);
		$groupId=$user['user_group_id'];
			//获取用户组,获取用户组折扣
		if ($groupId>0) {
			$hourDiscount= $pm->getUserGroupDiscount($groupId);
			$hourDiscount=$hourDiscount['discount'];
		}
		else
		{
			$hourDiscount=100;
		}
		foreach ($dateTopPrice as $k => $v) {
			$dateTopPrice[$k]['price']=$hourDiscount*$v['price']/100;
			
		}
		//end


		$maybeKm = $pm -> getConfigHourKm();
		$maybeDayKm = $maybeKm['day_km'];

		$groupDis = 100;
		$classDis = 100;

		require_once (Frm::$ROOT_PATH . 'TimeJs/orderAlg.php');
		$co = new COrderAlg();
		//($tmStart, $tmEnd, $groupDis, $classDis, $kmPrice, $km, $sepTime, $arrCap, $debug=false)
		$deposit = $co -> GenUseCarPrice($startTime, $endTime, $groupDis, $classDis, $kmPrice, $maybeDayKm, '12:00', $dateTopPrice, 0);

		return $deposit;
	}

	/**
	 * 获取修改费用
	 * @param  [Order对象] $order    [description]
	 * @param  [Order对象] $oldOrder [description]
	 * @return [type]           [description]
	 */
	public function getModifyFee($order, $oldOrder) {
		require_once (Frm::$ROOT_PATH . 'order_charge/classes/PriceManager.php');
		$pm = new PriceManager();
		$dateTopPrice = $pm -> getCarTopPriceDayArr($order -> car_id, $order -> order_start_time, $order -> order_end_time);
		
		require_once (Frm::$ROOT_PATH . 'TimeJs/orderAlg.php');
		$co = new COrderAlg();
		//($tmStart, $tmEnd, $tmStartNew, $tmEndNew, $sepTime, $arrPreHour, $debug=false)
		$modifyFee = $co -> ModifyUseCarPrice($oldOrder -> order_start_time, $oldOrder -> order_end_time, $order -> order_start_time, $order -> order_end_time, '12:00', $dateTopPrice);

		return $modifyFee;
	}

	public function getCancel($order)
	{
		require_once (Frm::$ROOT_PATH . 'order_charge/classes/PriceManager.php');
		$pm = new PriceManager();
		
		//delete by yangbei 2013-06-07 删除获取押金日封顶价格
		// $dateTopPrice = $pm -> getCarTopPriceDayArr($order -> car_id, $order -> order_start_time, $order -> order_end_time);
		//delete by yangbei 2013-06-07 

		//modify by yangbei 2013-06-07 增加获取车辆每小时租金价格

		//获取车辆基本价格
		$basePrice = $pm->getCarBasePrice($order -> car_id);

		require_once (Frm::$ROOT_PATH . 'system/classes/HolidayManager.php');
		$hm = new HolidayManager();
		//获取节假日价格信息
		$holidays = $hm -> getHolidayArr($tStartTime, $tEndTime);
		//取得每天的基本时租金
		$arrPreHour = $pm -> getHourPriceInDate($order -> car_id, $order -> order_start_time, $order -> order_end_time,$basePrice,$holidays);
		//modify by yangbei 2013-06-07 

		require_once (Frm::$ROOT_PATH . 'TimeJs/orderAlg.php');
		$orderAlg = new COrderAlg();
		//modify by yangbei 2013-06-07 修改参数日封顶押金为每小时价格
		// $cancelFee = $orderAlg -> CancelOrder($order->order_start_time, $order->order_end_time, '12:00', $dateTopPrice, false);
		$cancelFee = $orderAlg -> CancelOrder($order->order_start_time, $order->order_end_time, '12:00', $arrPreHour, false);
		//modify by yangbei 2013-06-07 
		return $cancelFee;
	}


	public function getViolateDeposit($userId, $carId) {
		$def = $this -> getDefViolateDeposit($userId, $carId);
		$special = $this -> getSpecialViolateDeposit($userId, $carId);
		$deposit = $def + $special;
		$d = $deposit;
		if (0 == $d) {
			return 0;
		}

		$arr = array();
		$needPay = $this -> needPayViolateDeposit($userId, $carId, $arr);
		if (!$needPay) {
			$d = 0;
		}

		return array('frozen' => $userFreezedDeposit, 'violateDeposit' => $d);
	}

	public function needPayViolateDeposit($userId, $carId, &$arrOrders) {
		require_once (Frm::$ROOT_PATH . 'order_charge/classes/OrderCharge.php');
		$orderCharge = new OrderCharge();
		//取得最早的一个未结算的违章押金的订单，然后取得这个订单及其以后的所有订单，再根据城市，判断是否支付违章押金
		$paiedInfo = $orderCharge -> getEarliestWithoutSettledVD($userId);
		if (!empty($paiedInfo)) {
			//取得用户最早支付的违章押金的订单   之后的 所有订单，并取出这些订单支付及车辆所在城市，用于判断是否支付违章押金
			require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
			$om = new OrderManager();
			$ret = $om -> getUserOrderAfter($userId, $paiedInfo['order_id']);
			if (empty($ret)) {
				return true;
			}

			$carIdArr = array();
			$orderIdArr = array();
			foreach ($ret as $value) {
				$carIdArr[] = $value['car_id'];
				$orderIdArr[] = $value['order_id'];
			}
			require_once (Frm::$ROOT_PATH . 'resource/classes/StationManager.php');
			$stm = new StationManager();
			$cityIdArr = $stm -> getCarArrCityId($carIdArr);

			$depositArr = $orderCharge -> getViolateDepositByOrderArr($orderIdArr);

			$arrOrders = array();
			$cityId = $cityIdArr[$carId];
			foreach ($ret as $row) {
				if ($cityIdArr[$row['car_id']] != $cityId) {
					continue;
				}
				$orderNo = $row['order_no'];
				$payTime = $depositArr[$row['order_id']]['pay_time'];
				$isBigOrder = !empty($depositArr[$row['order_id']]);
				$cityId = $cityIdArr[$row['car_id']];
				$chargeId = $depositArr[$row['order_id']]['id'];
				$amount = $depositArr[$row['order_id']]['amount'];
				//@formatter:off
				$arrOrders[] = array(
					'orderNumber' => $orderNo, 
					'orderStart' => $payTime, 
					'isBigOrder' => $isBigOrder, 
					'chargeId' => $chargeId,
					'amount' => $amount
				);
				//@formatter:on
			}

			require_once (Frm::$ROOT_PATH . 'TimeJs/orderRuleAlg.php');
			$alg = new COrderRuleAlg();
			$needPay = $alg -> PayVioDeposit($arrOrders);

			return $needPay;
		}

		return true;
	}

	protected function getDefViolateDeposit($userId, $carId) {
		$db = Frm::getInstance() -> getDb();

		/*通过汽 车查找城市id*/
		$sql = " SELECT s.city FROM edo_cp_car c LEFT JOIN edo_cp_station s ON c.station = s.id " . " WHERE c.id=$carId";
		$city_id = $db -> getOne($sql);

		/*获取城市默认违章押金*/
		$sql = " SELECT deposit FROM edo_cp_deposit_violate_city  WHERE city_id=" . $city_id;
		$city_deposit = $db -> getOne($sql);

		//取得用户所在的群组
		$sql = " SELECT ugl.user_group_id as group_id,u.user_type as user_type_id
				 FROM edo_user u LEFT JOIN edo_user_group_link ugl  ON ugl.uid=u.uid " . " WHERE u.uid=" . $userId;
		$user_data = $db -> getRow($sql);
		/*确定查找路径*/

		$sql = " SELECT group_id,discount,adjust_money FROM edo_cp_deposit_violate_group";
		$group_deposit = $db -> getAll($sql);

		$sql = " SELECT user_type_id,discount,adjust_money FROM edo_cp_deposit_violate_user_type";
		$user_type_deposit = $db -> getAll($sql);

		$violateCounterRs = array();

		foreach ($group_deposit as $v) {
			if ($v['group_id'] == $user_data['user_group_id']) {
				$violateCounterRs[] = $city_deposit * ($v['discount'] / 100) + $v['adjust_money'];
			} else if ($v['group_id'] == -1) {
				$violateCounterRs[] = $city_deposit * ($v['discount'] / 100) + $v['adjust_money'];
			}
		}

		foreach ($user_type_deposit as $v) {
			if ($v['user_type_id'] == $user_data['user_type_id']) {
				$violateCounterRs[] = $city_deposit * ($v['discount'] / 100) + $v['adjust_money'];
			} else if ($v['user_type_id'] == -1) {
				$violateCounterRs[] = $city_deposit * ($v['discount'] / 100) + $v['adjust_money'];
			}
		}

		//取最小的一个押金
		sort($violateCounterRs);
		$defViolateDeposit = $violateCounterRs[0];

		return $defViolateDeposit;
	}

	protected function getSpecialViolateDeposit($userId, $carId) {
		$db = Frm::getInstance() -> getDb();

		/*通过汽 车查找城市id*/
		$sql = " SELECT s.city FROM edo_cp_car  c LEFT JOIN edo_cp_station s ON c.station = s.id " . " WHERE c.id=" . $carId;
		$city_id = $db -> getOne($sql);

		/*获取会员折扣信息*/
		$sql = " SELECT ugl.user_group_id as group_id,u.user_type as user_type_id
				 FROM edo_user u LEFT JOIN edo_user_group_link ugl ON ugl.uid=u.uid " . " WHERE u.uid=" . $userId;
		$user_data = $db -> getRow($sql);

		/*获取该城市所有分组类型*/
		$sql = " SELECT * FROM edo_cp_deposit_violate WHERE city_id=" . $city_id;
		$arr_target = $db -> getAll($sql);
		// print_r($arr_target);

		//是否完整适配
		foreach ($arr_target as $v) {
			if ((int)$v['group_id'] == (int)$user_data['group_id'] && (int)$v['user_type_id'] == (int)$user_data['user_type_id']) {
				$res = $v;

				break;
			}
		}
		//如果没有完整适配,按群组优先级继续适配
		if (empty($res)) {
			foreach ($arr_target as $v) {
				if ((int)$v['group_id'] == (int)$user_data['group_id'] && (int)$v['user_type_id'] == -1) {
					$res = $v;
					break;
				}
			}
		}
		if (empty($res)) {
			foreach ($arr_target as $v) {
				if ((int)$v['group_id'] == -1 && (int)$v['user_type_id'] == (int)$user_data['user_type_id']) {
					$res = $v;
					break;
				}
			}
		}
		if (empty($res)) {
			foreach ($arr_target as $v) {
				if ((int)$v['group_id'] == -1 && (int)$v['user_type_id'] == -1) {
					$res = $v;
					break;
				}
			}
		}

		return $res['adjust_money'];
	}

}
?>