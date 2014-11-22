<?php
set_time_limit(0);
if (!defined('ROOT_PATH')) {
	define('IN_ECS', true);
	require (dirname(__FILE__) . '/../../cp/includes/init.php');
}

// include_once (ROOT_PATH . '/includes/cls_json.php');
// require_once (ROOT_PATH . '/admin/includes/lib_main.php');
require_once (ROOT_PATH . '../client/classes/RentComputer.php');

require_once (ROOT_PATH . '../client/classes/UserManager.php');

require_once (ROOT_PATH . "../client/classes/CarManager.php");

require_once (ROOT_PATH . "../moneyAccountManager/UnionPayMobileManager.php");

require_once (ROOT_PATH . "../medal_container/MedalContainer.php");

define("SQL_USER_PARAM", " u.user_id , u.user_name , u.sex , u.alias , u.signature , u.status , u.type , birthday , address_id ");

class Order {
	public $orderId;
	public $carId;
	public $userId;
	public $startTime;
	public $endTime;
	public $realEndTime;
	public $medalId;
	public $mile;
}

class OrderManager {

	/**
	 * @var cls_mysql
	 */
	private $mDbAgent = null;
	private $ecs = null;
	private $sns = null;
	/**
	 * 当前用户最近的一个订单
	 */
	protected $mRecentOrder = null;

	static public $STATUS_NOT_PAY = 0;
	static public $STATUS_ORDERED = 1;
	static public $STATUS_STARTED = 2;
	static public $STATUS_COMPLETED = 3;
	static public $STATUS_CANCELED = 4;
	static public $STATUS_PAY_TIMEOUT = 5;
	static public $STATUS_REVERT_TIMEOUT = 6;
	static public $STATUS_MODIFY = 7;
	public function OrderManager() {
		global $db, $ecs, $sns;
		$this -> mDbAgent = $db;
		$this -> ecs = $ecs;
		$this -> sns = $sns;
	}

	/*获取相差天数
	 * @param array $argv[time_from](int)	$argv[time_end](int)
	 * @return int
	 */
	protected function get_count_day($argv) {
		//		$day_from = date('Y-m-d', $argv['start_time']);
		//		$day_end = date('Y-m-d', $argv['end_time']);

		/*int 时间戳*/
		$day_from = strtotime(date('Y-m-d', strtotime($argv['start_time'])));
		$day_end = strtotime(date('Y-m-d', strtotime($argv['end_time'])));
		$distance = ceil(($day_end - $day_from) / (24 * 3600)) + 1;

		return $distance;
	}

	/**
	 * 判断整个订单节假日所占的时间多还是工作日所占的时间多，如果节假日所占的时间多，就按节假日的日封顶价算预算
	 * @param int $day  		时间区段
	 * @param array $argv[time_from]	$argv[time_end]
	 * @return array
	 */
	protected function is_holiday($argv) {
		$table = $this -> ecs -> table('vacation');
		//选出所有的假日
		$tStartTime = strtotime($argv['start_time']);
		$tEndTime = strtotime($argv['end_time']);
		$sStartDate = date('Y-m-d', $tStartTime);
		$sEndDate = date('Y-m-d', $tEndTime);
		$sql = " SELECT holiday " . " FROM " . $table . " WHERE holiday >= '$sStartDate'" . " AND  holiday 	<= '$sEndDate'";
		$tmp_arr_holiday = $this -> mDbAgent -> getAll($sql);
		$arr_holiday = array();
		foreach ($tmp_arr_holiday as $v) {
			$arr_holiday[] = $v['holiday'];
		}

		/*单位是秒*/
		$holiday_count = 0;

		$sCurrentDate = $sStartDate;
		$tCurrentDate = strtotime($sCurrentDate);
		$st = $tStartTime;
		$et = $tEndTime;
		while ($st < $tEndTime) {//循环订单时间所在的每一天，把节假日的时间全部加起来
			$tCurrentDayEndTime = strtotime($sCurrentDate . "23:59:59");
			if ($et > $tCurrentDayEndTime) {
				$et = $tCurrentDayEndTime + 1;
			}

			if (in_array($sCurrentDate, $arr_holiday)) {
				$holiday_count += $et - $st;
			}

			$tCurrentDate = strtotime($sCurrentDate . " 23:59:59") + 1;
			$sCurrentDate = date('Y-m-d', $tCurrentDate);
			if ($st < $tCurrentDate) {
				$st = $tCurrentDate;
			}
			$et = $tEndTime;
		}

		if ($holiday_count < ($tEndTime - $tStartTime - $holiday_count)) {
			return false;
		} else {
			return true;
		}
	}

	/*获取某车的封顶价格
	 * 取得现在时刻以后的订单信息。把订单按每天存放好。如果开始时间是今天之前，今天该订单的开始时间就是0；
	 * 如果结束时间是今天之后的，今天该订单的结束时间就是23:59。
	 *
	 * @param array $argv[car_id]
	 * @param array $argv[time_from]	$argv[time_end]
	 * @return array
	 */
	protected function get_day_top_price($argv) {

		$table = $this -> ecs -> table('car_price_base');
		$sql = " SELECT top_price,price_km,type  " . " FROM " . $table . " WHERE car_id=" . $argv['car_id'];
		$ret = $this -> mDbAgent -> getAll($sql);

		$topPrice = array();
		foreach ($ret as $row) {
			if ($this -> is_holiday($argv) && 1 == $row['type']) {//1代表节假日
				$topPrice['top_price'] = $row['top_price'];
			}

			if (0 == $row['type']) {
				$topPrice['top_price'] = $row['top_price'];
				$topPrice['price_km'] = $row['price_km'];
			}
		}

		return $topPrice;
	}

	/*计算使用押金 预付款~
	 * 取得现在时刻以后的订单信息。把订单按每天存放好。如果开始时间是今天之前，今天该订单的开始时间就是0；
	 * 如果结束时间是今天之后的，今天该订单的结束时间就是23:59。
	 *
	 * @param array $argv[user_id] (int)
	 * @param array $argv[car_id] (int)
	 * @param array $argv[time_from]	$argv[time_end] (int)
	 * @return array
	 */
	public function balance($argv) {
		/*
		 ob_start();
		 print_r($argv);
		 print_r($_SESSION);
		 trace(ob_get_clean());
		 */

		$this -> submitCheck($argv);
		//判断oldOrderID和medalID是否符合规则
		if ($argv['old_order_id'] > 0) {
			// if ($this -> orderIsHadUsedMedal($argv['old_order_id']) && $argv['medal_id'] > 0) {
			// 	throw new Exception("", 4043);
			// }
			$this -> canModifyStartTime($argv);

		}

		if (empty($argv['user_id'])) {
			$user_id = UserManager::getLoginUserId();
			$argv['user_id'] = $user_id;
		} else {
			$user_id = $argv['user_id'];
		}

		/*1,计算默认使用押金数  默认使用押金 = 日封顶价格 * 天数 + 每日系统预估里程数 * 天数 * 公里价格*/
		// * @param array $argv[time_from](int)	$argv[time_end](int)
		$top_price_and_price_km = $this -> get_day_top_price($argv);
		//日封顶价格

		$day_count = $this -> get_count_day($argv);

		// echo $day_count;
		//天数
		// print_r($top_price_and_price_km);
		//获取系统预估总里程数
		$sql = " SELECT value " . " FROM " . $this -> ecs -> table('shop_config') . " WHERE code='day_km'";
		$sys_count_km = $this -> mDbAgent -> getOne($sql);

		$timeRent = $top_price_and_price_km['top_price'] * $day_count;
		$kmRent = $sys_count_km * $day_count * $top_price_and_price_km['price_km'];

		$default_use_deposit = $timeRent + $kmRent;
		// echo PHP_EOL;
		// echo $default_use_deposit;
		/*2,计算调整使用押金数*/

		//获取会员调整使用押金百分比 station_id 站点ID	group_id 会员组ID	 user_type_id
		$sql = " SELECT ugl.user_group_id as group_id ,u.user_type as user_type_id" . " FROM " . $this -> sns -> table('user') . ' u ' . " LEFT JOIN " . $this -> sns -> table('user_group_link') . ' ugl ' . " ON ugl.uid=u.uid " . " WHERE u.uid=" . $user_id;

		$user_data = $this -> mDbAgent -> getRow($sql);

		// $user_data['user_type_id']=2;
		// $user_data['group_id']=2;
		// echo 'user_data:';
		// print_r($user_data);
		$sql = " SELECT c.station as station_id" . " FROM " . $this -> ecs -> table('car') . ' c ' . " WHERE c.id=" . $argv['car_id'];
		$station_id = $this -> mDbAgent -> getOne($sql);
		// print_r($station_id);
		//查找对应调整百分比
		//选出使用押金，使用押金的优先级：站点必有,群组优先
		$sql = " SELECT * " . " FROM " . $this -> ecs -> table('deposit') . " WHERE station_id=" . $station_id;

		// " AND group_id = ".$user_data['group_id'].
		// " AND user_type_id=".$user_data['user_type_id'];

		$arr_target = $this -> mDbAgent -> getAll($sql);
		// echo 'arr_target:';
		// print_r($arr_target);
		//是否完整适配
		foreach ($arr_target as $v) {
			if ($v['group_id'] == $user_data['group_id'] && $v['user_type_id'] == $user_data['user_type_id']) {

				$res = $v;
			}

		}
		//如果没有完整适配,按类别->群组优先级继续适配
		//群组指定 类别所有
		if (empty($res)) {
			foreach ($arr_target as $v) {
				if ((int)$v['group_id'] == (int)$user_data['group_id'] && (int)$v['user_type_id'] == -1) {
					$res = $v;
				}
			}
		}
		////群组所有 类别指定
		if (empty($res)) {
			foreach ($arr_target as $v) {
				if ((int)$v['group_id'] == -1 && (int)$v['user_type_id'] == (int)$user_data['user_type_id']) {
					$res = $v;
				}
			}
		}

		//群组所有 类别所有
		if (empty($res)) {
			foreach ($arr_target as $v) {
				if ((int)$v['group_id'] == -1 && (int)$v['user_type_id'] == -1) {
					$res = $v;
				}
			}
		}

		// echo 'res:';
		// print_r($res);
		$adjust_percent = $res['deposit'];

		if (empty($adjust_percent)) {
			$adjust_percent = 1;
		} else {
			$adjust_percent = $adjust_percent / 100;
		}

		// echo ($adjust_percent);
		/*计算出总价格*/
		$timeRent = $timeRent * $adjust_percent;
		$kmRent = $kmRent * $adjust_percent;

		$budget_deposit = $timeRent + $kmRent;

		$userAccountViolateDeposit = UserManager::getUserViolateDeposit();

		$orderViolateDeposit = $this -> getViolateDeposit($argv);
		if ($userAccountViolateDeposit - $orderViolateDeposit >= 0) {
			$illegal_deposit = 0;
		} else {
			$illegal_deposit = $orderViolateDeposit - $userAccountViolateDeposit;
		}

		$maybeCost = self::estimatedCostForUser($argv);

		// var_dump($budget_deposit);
		//参数：$_SESSION['user_id'] ,根据这参数获得该会员的使用押金$budget_deposit
		$res = array("frozen" => $userAccountViolateDeposit, "budget_deposit" => $budget_deposit, "illegal_deposit" => $illegal_deposit, "timeRent" => $timeRent, "kmRent" => $kmRent, "maybeCost" => $maybeCost);
		// make_json_result($res);
		//

		//奖章计价
		if ($argv['medal_id'] > 0) {
			require_once (ROOT_PATH . "../medal_container/MedalContainer.php");

			$order = new Order();
			// $order -> orderId = ;
			$order -> carId = $argv['car_id'];
			$order -> userId = $argv['user_id'];
			$order -> startTime = strtotime($argv['start_time']);
			$order -> endTime = strtotime($argv['end_time']);

			$mc = MedalContainer::getInstance();
			$m = $mc -> getMedalObj($argv['medal_id']);
			/**   calculateRent
			 * 优惠计算
			 * @param  [array] $caculateRent ['timeRent'],[kmRent]
			 * @param  [object] $order 		['order']
			 * @param  int $userID       [description]
			 * @param  enum $useType         'deposit','settlement'
			 * @return [type]               [description]
			 */
			$m -> calculateRent($res, $order, 'deposit');
			$res['budget_deposit'] = $res['timeRent'] + $res['kmRent'];

		}

		$oldOrderId = $argv['old_order_id'];
		if ($oldOrderId > 0) {
			$this -> getModifyFee($oldOrderId, $res);
		}

		return $res;
	}

	function getModifyFee($oldOrderId, &$budget, $userID = "") {

		if (empty($userID)) {
			$userID = UserManager::getLoginUserId();
		}

		if ($oldOrderId > 0) {

			$oldOrder = $this -> getOrder($oldOrderId);

			$oldOrder['order_real_cost'] = self::getAllOldOrderCost($oldOrderId);

			$userConsumeLev = UserManager::getUserConsumeLevel();

			// $modifyFee = $oldOrder['modify_times'] < $userConsumeLev['free_times'] ? 0 : $budget['budget_deposit'] * $userConsumeLev['revise_fee'] * 0.01;
			$modifyFee = 0;

			$budget['modify_fee'] = $modifyFee;

			$budget['old_order'] = array('budget_deposit' => $oldOrder['order_real_cost'], 'status' => $oldOrder['order_stat']);
			if (0 == $oldOrder['order_stat']) {
				$budget['budget_deposit'] = $budget['budget_deposit'] + $oldOrder['order_real_cost'];
				// + $modifyFee;
			} else if (1 == $oldOrder['order_stat'] || 2 == $oldOrder['order_stat']) {
				$newRent = $budget['budget_deposit'] - $oldOrder['order_real_cost'];
				$budget['budget_deposit'] = $newRent < 0 ? 0 : $newRent;
				//+ $modifyFee;
			} else {
				throw new Exception('The status of this is not allowed to modification.', 5000);
			}
		}
	}

	/**
	 * 系统预估订单使用费用,供客户参考\显示用
	 * @param  [type] $argv [description]
	 * @return [type]       [description]
	 */
	function estimatedCostForUser($argv) {
		$order = new Order();
		$order -> orderId = 0;
		$order -> carId = $argv['car_id'];
		$order -> userId = $userID = UserManager::getLoginUserId();
		$order -> startTime = strtotime($argv['start_time']);
		$order -> endTime = strtotime($argv['end_time']);
		$order -> realEndTime = $order -> endTime;
		// $order -> medalId = $argv['medal_id'];
		$kmInfo = self::getConfigHourKm();
		$order -> mile = $this -> getMaybeMile($order -> startTime, $order -> endTime, $kmInfo);

		$rentComputer = new RentComputer();
		$rent = $rentComputer -> computeRent($order);

		return $rent;
	}

	function getMaybeMile($tStart, $tEnd, $kmInfo) {
		$st = $tStart;
		$et = $tEnd;
		$dayKm = $kmInfo['day_km'];
		$hourKm = $kmInfo['hour_km'];

		$kmCount = 0;
		while ($st < $tEnd) {
			$tDayEnd = strtotime(date('Y-m-d', $st)) + 86400;
			if ($et > $tDayEnd) {
				$et = $tDayEnd;
			}

			$hourKmCount = (($et - $st) / 3600) * $hourKm;
			if ($hourKmCount > $dayKm) {
				$kmCount += $dayKm;
			} else {
				$kmCount += $hourKmCount;
			}

			$st = $tDayEnd;
			$et = $tEnd;
		}

		return $kmCount;
	}

	public function settlement($orderId) {
		// @fomatter:off
		$sql = "SELECT o.order_id , o.car_id , o.user_id , o.order_start_time , o.order_end_time , o.real_end_time ,
			o.medal_id , o.start_mileage , o.end_mileage , c.mile_factor ,o.medal_id
			FROM " . $this -> ecs -> table("car_order") . " o LEFT JOIN " . $this -> ecs -> table("car") . " c ON o.car_id = c.id 
			WHERE order_id = $orderId";
		// @fomatter:on
		$row = $this -> mDbAgent -> getRow($sql);

		$medalId = $row['medal_id'];

		$order = new Order();
		$order -> orderId = $row['order_id'];
		$order -> carId = $row['car_id'];
		$order -> userId = $row['user_id'];
		$order -> startTime = $row['order_start_time'];
		$order -> endTime = $row['order_end_time'];
		$order -> realEndTime = $row['real_end_time'];
		$order -> medalId = $row['medal_id'];
		$order -> mile = CarAgent::getMileOrFactor($row['start_mileage'], $row['end_mileage'], $row['mile_factor']);

		$rentComputer = new RentComputer();
		$rentDetail = $rentComputer -> computeRentDetail($order);
		$rent = $rentDetail['kmRent'] + $rentDetail['timeRent'];
		// $oldOrderId = $argv['old_order_id'];
		// if ($oldOrderId > 0) {
		// 	$oldOrder = $this -> getOrder($argv['old_order_id']);

		// 	$rent = $rent - $oldOrder['order_real_cost'];
		// }

		/*奖章计价,日后增加,代码预留*/
		// $medal = $medalContainer -> getMedal($medalId);
		// if (null != $medal) {
		// 	$o = new Order();
		// 	$o -> setRent($rent);
		// 	$newRent = $medal -> calculateRent($o);
		// }
		//奖章计价
		if ($medalId > 0) {

			$mc = MedalContainer::getInstance();
			$m = $mc -> getMedalObj($medalId);
			/**   calculateRent
			 * 优惠计算
			 * @param  [array] $caculateRent ['timeRent'],[kmRent]
			 * @param  [object] $order 		['order']
			 * @param  int $userID       [description]
			 * @param  enum $useType         'deposit','settlement'
			 * @return [type]               [description]
			 */
			$m -> calculateRent($rentDetail, $order, 'settlement');
			$rent = $rentDetail['timeRent'] + $rentDetail['kmRent'];
		}

		$sql = "SELECT payment_trans_num FROM " . $this -> ecs -> table("car_order") . " WHERE order_id = {$order->orderId}";
		$transNum = $this -> mDbAgent -> getOne($sql);

		$accountContainer = AccountManagerContainer::getInstance();
		$account = $accountContainer -> getAccountManager("eduo");
		$accountId = $account -> getAccountId($order -> userId);
		$account -> moneyOperate("freezeComplete", $accountId, $transNum, $rent);
	}

	/**
	 * 对比时间间隔 是否超过24小时
	 * @param  int $aTime
	 * @param  int $bTime
	 * @return boolean
	 */
	function isCancelFree($aTime, $bTime) {
		return timeCompare($aTime, $bTime, 24 * 60 * 60);
	}

	/**
	 * 对比时间间隔是否 超出指定限制
	 * @param  int $aTime
	 * @param  int $bTime
	 * @param  int $limit
	 * @return boolean
	 */
	function timeCompare($aTime, $bTime, $limit) {
		if (!is_numeric($limit) || !is_numeric($aTime) || !is_numeric($bTime)) {
			return false;
		}
		if (abs($aTime - $bTime) > $limit) {
			return false;
		} else {
			return true;
		}
	}

	public function getOrder($orderId) {
		global $ecs;

		//@formatter:off
		$sql = "SELECT order_id,car_id,order_start_time AS start_time,order_end_time AS end_time,
			order_real_cost,modify_times ,payment_trans_num , user_id , order_stat ,medal_id from " . $ecs -> table("car_order") . 
			" WHERE order_id = $orderId";
			//@formatter:on
		$order = $this -> mDbAgent -> getRow($sql);
		return $order;
	}

	/*计算违章押金 预付款~
	 * ；会员->城市->群组->调整
	 *           ->类别
	 * @param array $argv[user_id] (int)
	 * @param array $argv[car_id] (int)
	 * @return array
	 */
	public function getViolateDeposit($argv) {
		$violateDeposit = $this -> getDefViolateDeposit($argv) + $this -> specialViolateDeposit($argv);
		// echo 'defViolateDeposit = '.$this->getDefViolateDeposit($argv).'+'.$this->specialViolateDeposit($argv);

		return $violateDeposit;
	}

	/*计算默认违章押金
	 * ；会员->城市->群组->调整
	 *           ->类别
	 * @param array $argv[user_id] (int)
	 * @param array $argv[car_id] (int)
	 * @return array
	 */
	protected function getDefViolateDeposit($argv) {
		/*通过汽 车查找城市id*/
		$sql = " SELECT s.city FROM " . $this -> ecs -> table('car') . ' c ' . " LEFT JOIN " . $this -> ecs -> table('station') . ' s ' . " ON c.station = s.id " . " WHERE c.id=" . $argv['car_id'];
		$city_id = $this -> mDbAgent -> getOne($sql);

		/*获取城市默认违章押金*/
		$sql = " SELECT deposit FROM " . $this -> ecs -> table('deposit_violate_city') . " WHERE city_id=" . $city_id;
		$city_deposit = $this -> mDbAgent -> getOne($sql);

		//取得用户所在的群组
		$sql = " SELECT ugl.user_group_id as group_id,u.user_type as user_type_id
				 FROM " . $this -> sns -> table('user') . ' u ' . " LEFT JOIN " . $this -> sns -> table('user_group_link') . ' ugl ' . " ON ugl.uid=u.uid " . " WHERE u.uid=" . $argv['user_id'];
		$user_data = $this -> mDbAgent -> getRow($sql);
		/*确定查找路径*/

		$sql = " SELECT group_id,discount,adjust_money FROM " . $this -> ecs -> table('deposit_violate_group');
		$group_deposit = $this -> mDbAgent -> getAll($sql);

		$sql = " SELECT user_type_id,discount,adjust_money FROM " . $this -> ecs -> table('deposit_violate_user_type');
		$user_type_deposit = $this -> mDbAgent -> getAll($sql);

		$violateCounterRs = array();

		foreach ($group_deposit as $v) {
			if ($v['group_id'] == -1) {
				$violateCounterRs[] = $city_deposit * ($v['discount'] / 100) + $v['adjust_money'];
			} else if ($v['group_id'] == $user_data['user_group_id']) {
				$violateCounterRs[] = $city_deposit * ($v['discount'] / 100) + $v['adjust_money'];
			}
		}

		foreach ($user_type_deposit as $v) {
			if ($v['user_type_id'] == -1) {
				$violateCounterRs[] = $city_deposit * ($v['discount'] / 100) + $v['adjust_money'];
			} else if ($v['user_type_id'] == $user_data['user_type_id']) {
				$violateCounterRs[] = $city_deposit * ($v['discount'] / 100) + $v['adjust_money'];
			}
		}

		sort($violateCounterRs);
		$defViolateDeposit = $violateCounterRs[0];
		return $defViolateDeposit;

	}

	protected function specialViolateDeposit($argv) {
		/*通过汽 车查找城市id*/
		$sql = " SELECT s.city FROM " . $this -> ecs -> table('car') . ' c ' . " LEFT JOIN " . $this -> ecs -> table('station') . ' s ' . " ON c.station = s.id " . " WHERE c.id=" . $argv['car_id'];
		$city_id = $this -> mDbAgent -> getOne($sql);

		/*获取会员折扣信息*/
		$sql = " SELECT ugl.user_group_id as group_id,u.user_type as user_type_id
				 FROM " . $this -> sns -> table('user') . ' u ' . " LEFT JOIN " . $this -> sns -> table('user_group_link') . ' ugl ' . " ON ugl.uid=u.uid " . " WHERE u.uid=" . $argv['user_id'];
		$user_data = $this -> mDbAgent -> getRow($sql);

		/*获取该城市所有分组类型*/
		$sql = " SELECT * FROM " . $this -> ecs -> table('deposit_violate') . " WHERE city_id=" . $city_id;
		$arr_target = $this -> mDbAgent -> getAll($sql);
		// print_r($arr_target);
		// print_r($user_data);
		//是否完整适配
		foreach ($arr_target as $v) {
			if ((int)$v['group_id'] == (int)$user_data['group_id'] && (int)$v['user_type_id'] == (int)$user_data['user_type_id']) {
				$res = $v;
			}

		}
		//如果没有完整适配,按群组优先级继续适配
		if (empty($res)) {
			foreach ($arr_target as $v) {
				if ((int)$v['group_id'] == (int)$user_data['group_id'] && (int)$v['user_type_id'] == -1) {
					$res = $v;
				}
			}
		}
		if (empty($res)) {
			foreach ($arr_target as $v) {
				if ((int)$v['group_id'] == -1 && (int)$v['user_type_id'] == (int)$user_data['user_type_id']) {
					$res = $v;
				}
			}
		}
		if (empty($res)) {
			foreach ($arr_target as $v) {
				if ((int)$v['group_id'] == -1 && (int)$v['user_type_id'] == -1) {
					$res = $v;
				}
			}
		}

		return $res['adjust_money'];
		//$defViolateDeposit = $this->defViolateDeposit($argv);

	}

	public function create($argv) {

		$start_time = strtotime($argv['start_time']);
		$end_time = strtotime($argv['end_time']);
		$carId = $argv['car_id'];
		$oldOrderId = $argv['old_order_id'];

		$modifyTimes = 0;
		$medalId = 0;
		if ($oldOrderId > 0) {
			$oldOrderInfo = $this -> getOrder($oldOrderId);
			$medalId = $oldOrderInfo['medal_id'] == 0 ? $argv['medal_id'] : $oldOrderInfo['medal_id'];
			$modifyTimes = $oldOrderInfo['modify_times'] + 1;
		} else {
			$medalId = $argv['medal_id'] > 0 ? $argv['medal_id'] : 0;
		}

		// $rent = $this -> balance(array('car_id' => $carId, 'time_from' => $argv['start_time'], 'time_end' => $argv['end_time'], 'old_order_id' => $oldOrderId));
		$rent = $this -> balance($argv);
		require_once (ROOT_PATH . "../client/classes/UserManager.php");
		$user_id = UserManager::getLoginUserId();
		$illegalDeposit = $rent['illegal_deposit'];
		$orderPrice = $rent['budget_deposit'];

		//修订费
		$modifyFee = isset($rent['modify_fee']) ? $rent['modify_fee'] : 0;

		$orderTime = time();
		$sql = "insert into " . $this -> ecs -> table("car_order") . " set car_id='$_GET[car_id]',user_id='$user_id',
			order_start_time='$start_time', order_end_time='$end_time',add_time='" . $orderTime . "' , 
			old_order_id='$oldOrderId' ,order_real_cost=$orderPrice ,violate_real_rent=$illegalDeposit
			,modify_times=$modifyTimes,modify_fee=$modifyFee,medal_id=$medalId ";

		$this -> mDbAgent -> query($sql);
		$orderid = $this -> mDbAgent -> insert_id();
		$order_sn = date("Y") . date("m") . date("d") . $_GET['car_id'] . $user_id . $orderid;

		//更新订单号

		$this -> mDbAgent -> query("update " . $this -> ecs -> table("car_order") . " set order_no='$order_sn' where order_id='$orderid'");
		//更新奖章使用状态为"已使用"
		if ($medalId > 0) {
			$useTime = time();
			MedalContainer::useMedal($user_id, $medalId, $useTime);
		}
		if ($oldOrderId > 0) {
			//老订单状态修改为"已修改"
			self::setOrderStatus($oldOrderInfo['order_id'], self::$STATUS_MODIFY);
			//新订单
		}
		/**********************/
		/*添加订单定时任务*/
		//setOrderTask($orderID, $time,$methodName,$remark="")
		//半小时后检查有没付款
		$this -> setOrderTask($orderid, ((int)time() + 1800), "setPayforTimeOut", "支付超时");
		/******************/

		$upp = new UnionPayMobileManager();
		$strOrderTime = date('YmdHis', $orderTime);
		$uppayOrderInfo = $upp -> getUPPayOrderInfo($strOrderTime, $orderid, $illegalDeposit + $orderPrice + $modifyFee);
		return array('orderId' => $orderid, 'orderSn' => $order_sn, 'rent' => $rent, 'uppayOrderInfo' => $uppayOrderInfo);
	}

	public function getMyOrderList($argv) {
		global $ecs;

		$userId = UserManager::getLoginUserId();
		$sql = "SELECT o.order_id , o.order_no , o.order_start_time , o.order_end_time , o.order_org_cost , 
				o.car_id , c.name , st.name AS station , c.icon , o.order_stat ,w.open_login_num, o.add_time ,
				i.num as invoice_num , c.brand , c.gearbox
				FROM " . $ecs -> table("car_order") . " o 
				LEFT JOIN " . $ecs -> table("car") . " c ON c.id = o.car_id " . " 
				LEFT JOIN edo_cp_station st ON c.station = st.id
				LEFT JOIN " . " 
				(SELECT COUNT(1) AS num , order_id  FROM " . $this -> ecs -> table("invoice_log") . " 
				GROUP BY order_id)" . ' i ' . " ON i.order_id=o.order_id " . " 
				LEFT JOIN 
				( SELECT user_id, COUNT(1) as open_login_num FROM " . $this -> sns -> table("open_login") . " 
				WHERE status=1 GROUP BY user_id )  w " . " ON w.user_id = o.user_id " . " 
				WHERE o.user_id = $userId AND o.order_stat IN ( 0 , 1 , 2 , 3 , 4 , 5 , 6 , 9 )
				ORDER BY o.add_time DESC LIMIT 0 , 20 ";

		$orderRet = $this -> mDbAgent -> getAll($sql);
		if (empty($orderRet)) {
			return array();
		}

		$orderNoArr = array();
		$carIdArr = array();
		foreach ($orderRet as $key => &$row) {
			$row['order_start_time'] = date('Y-m-d H:i', $row['order_start_time']);
			$row['order_end_time'] = date('Y-m-d H:i', $row['order_end_time']);
			$row['add_time'] = date('Y-m-d H:i:s', $row['add_time']);
			$orderNoArr[] = '\'' . $row['order_no'] . '\'';
			$carIdArr = $row['car_id'];
		}

		$sql = "SELECT c.amount , c.order_no ,  FROM order_charge c 
			WHERE order_no IN (" . implode(',', $orderNoArr) . ") AND ( item = 1 OR item = 2 OR item = 3 )
			AND status IN ( 0 , 1 )
			ORDER BY order_no ";
		$arr = array();
		$ret = $this -> mDbAgent -> getAll($sql);
		if (!empty($ret)) {
			foreach ($ret as $value) {
				$arr[$value['order_no']][] = array( $value['item'] , $value['amount'] );
			}
		}

		foreach ($orderRet as &$value) {
			$value['deposit'] = $arr[$value['order_no']];
		}

		return $orderRet;

	}

	/**
	 * 获取某车辆的订单信息
	 * @param  int $argv['car_id'] 车辆id
	 * @param  string $argv['start_time'] 车辆id
	 * @return array       [description]
	 */
	public function getOrderInfo($argv) {
		global $ecs;

		if (isset($argv['start_time'])) {
			$startTime = strtotime($argv['start_time']);

		} else {
			$startTime = strtotime(date('Y-m-d', time()));
		}
		$car_id = $argv['car_id'];
		//结束时间大于现在的，都是未完成的订单，对下个订单有影响。
		$sql = "select o.order_id,o.car_id,o.order_start_time as start_time,o.order_end_time as end_time
				from " . $ecs -> table("car_order") . " o " . " where o.car_id=" . $car_id . " and order_end_time>" . $startTime . " AND " . self::getAvailableOrderWhereSQL() . " ORDER BY o.order_start_time";

		$orders = $this -> mDbAgent -> getAll($sql);
		return $orders;

	}

	/**
	 * 取得现在时刻以后的订单信息。把订单按每天存放好。如果开始时间是今天之前，今天该订单的开始时间就是0；
	 * 如果结束时间是今天之后的，今天该订单的结束时间就是23:59。
	 *
	 * @param  int $argv['car_id'] 车辆id
	 * @param  string $argv['start_time'] 车辆id
	 * @return array
	 */
	public function getOrderTimeInDay($argv) {
		$orders = $this -> getOrderInfo($argv);
		return $this -> formatOrderTime($orders);

	}

	/**
	 * 格式化订单时间结构,提供前端js使用
	 *
	 * @param  array $orders (order_id,car_id,start_time,end_time)
	 * @return array         [description]
	 */
	public function formatOrderTime($orders) {
		$dayArr = array();
		$now = time();
		foreach ($orders as $k => $v) {
			$order = array('start_time' => '', 'end_time' => '');

			//开始时间
			$st = $v['start_time'];
			$sd = (int)(($st - strtotime(date('Y-m-d 00:00:00'))) / 86400);
			//计算开始时间距今天的天数
			if ($sd < 0) {//订单已经于今天之前开始了
				$order['start_time'] = "00:00";
			} else {
				//订单之前的空时间如果不加空，就造成数组不连续，json转换时就变成对象了
				for ($i = 0; $i < $sd; ++$i) {
					if (!array_key_exists($i, $dayArr)) {
						$dayArr[$i] = array();
					}
				}
				//该订单的开始时间那一天，结束时间只能由后面计算
				$order['start_time'] = date('H:i', $st);
			}

			//结束时间
			$et = $v['end_time'];
			$ed = (int)(($et - strtotime(date('Y-m-d 00:00:00'))) / 86400);
			for ($i = $sd; $i < $ed; ++$i) {
				if (0 == $i) {//订单开始的那一天
					if (0 == $ed) {
						$order['end_time'] = date('H:i', $et);
					} else {
						$order['end_time'] = '23:59';
					}

					//订单开始的那一天
					$dayArr[$sd < 0 ? 0 : $sd][] = $order;
				} else {
					//订单完全跨越的那些天
					$dayArr[$i][] = array('start_time' => '00:00', 'end_time' => '23:59');
				}
			}

			//该订单最后一天
			if ($i == $ed) {
				if ($sd == $i) {
					$order['end_time'] = date('H:i', $et);
					$dayArr[$sd < 0 ? 0 : $sd][] = $order;
				} else {
					$dayArr[$ed][] = array('start_time' => '00:00', 'end_time' => date('H:i', $et));
				}
			}
		}
		return $dayArr;

	}

	/**
	 * 取得当前登录用户最近一次可用（已预定、执行中和还车超时）的订单。AdvanceTimeOfGetCar指定的。
	 */
	public function getMyRecentAvailableOrder() {
		if (null == $this -> mRecentOrder) {
			global $db, $ecs;

			$now = time() + AdvanceTimeOfGetCar;
			$uid = UserManager::getLoginUserId();
			//取只要没结束的订单
			//@formatter:off
			$sql = "SELECT car_id , order_start_time , order_end_time FROM " . $ecs -> table("car_order") . 
				" WHERE user_id = '$uid' AND ( order_stat = 1 OR order_stat = 2 OR order_stat = 6 ) " .
				" AND order_start_time <= $now ORDER BY order_start_time LIMIT 1";
			//@formatter:on
			$res = $db -> getRow($sql);

			$this -> mRecentOrder = $res;
		}

		return $this -> mRecentOrder;
	}

	public function getInvoiceInfo($argv) {
		$userId = UserManager::getLoginUserId();

		$sql = " SELECT u.invoice_title, u.invoice_address FROM " . $this -> sns -> table("user") . ' u ' . " WHERE uid = $userId";
		$res = $this -> mDbAgent -> getRow($sql);
		$res['order_id'] = $argv['orID'];
		return $res;
	}

	public function askForInvoice($argv) {
		$userId = UserManager::getLoginUserId();

		if ($argv['isFavor'] == 'true') {
			//更新用户默认发票抬头,地址
			$user_invoice = array('invoice_title' => $argv['invoice_title'], 'invoice_address' => $argv['invoice_address']);
			$this -> mDbAgent -> autoExecute($this -> sns -> table('user'), $user_invoice, "update", "uid=" . $userId);

		}

		//插入发票信息,判断是否已插入过
		$sql = " SELECT COUNT(1) FROM  " . $this -> ecs -> table('invoice_log') . " WHERE order_id=" . $argv['order_id'];
		if ($this -> mDbAgent -> getOne($sql) < 1) {
			$invoice_log = array('user_id' => $userId, 'order_id' => $argv['order_id'], 'invoice_address' => $argv['invoice_address'], 'invoice_title' => $argv['invoice_title']);
			$this -> mDbAgent -> autoExecute($this -> ecs -> table('invoice_log'), $invoice_log);
		}

		//print_r($argv);
	}

	public function getShowOrderContent($argv) {

		if (ceil($argv['orID']) == $argv['orID']) {
			$sql = " SELECT o.order_id ,u.uname,s.name as station_name,c.name as car_name,c.icon as car_icon ,o.order_start_time,o.order_end_time,o.order_real_cost " . " FROM  " . $this -> ecs -> table('car_order') . ' o ' . " LEFT JOIN " . $this -> ecs -> table('car') . ' c ' . " ON c.id=o.car_id " . " LEFT JOIN " . $this -> sns -> table('user') . ' u ' . " ON u.uid=o.user_id " . " LEFT JOIN " . $this -> ecs -> table('station') . ' s ' . " ON s.id=c.station " . " WHERE order_id=" . $argv['orID'];
			$res = $this -> mDbAgent -> getRow($sql);

			$res['order_start_time'] = date('Y-m-d H:i', $res['order_start_time']);
			$res['order_end_time'] = date('Y-m-d H:i', $res['order_end_time']);
			return $res;

		}
	}

	/**
	 * 支付,'CashAccountManager','CreditAccountManager','AlipayAccountManager' 三种方式
	 * @param  int $argv['orderNo']     订单号
	 * @param  int $argv['useType']     支付方式
	 * @return [type]       [description]
	 */
	public function payFor($argv) {
		require_once (ROOT_PATH . '../moneyAccountManager/AccountManagerContainer.php');

		$className = $argv['useType'];
		$payForObj = AccountManagerContainer::getInstance() -> getAccountManager($className);

		$orderID = $argv['order_id'];
		//获取该订单需要预付的使用押金,违章押金
		$sql = " SELECT user_id, order_no , order_real_cost,violate_real_rent ,order_stat , order_start_time , order_end_time , car_id ,modify_fee,cancel_fee" . " FROM " . $this -> ecs -> table('car_order') . " WHERE order_id=" . $orderID . " LIMIT 1 ";
		$ret = $this -> mDbAgent -> getRow($sql);
		$useRent = $ret['order_real_cost'];
		$violateRent = $ret['violate_real_rent'];
		$userID = $ret['user_id'];
		$orderStartTime = $ret['order_start_time'];
		$orderEndTime = $ret['order_end_time'];
		$carId = $ret['car_id'];
		$modifyFee = $ret['modify_fee'];
		$orderNo = $ret['order_no'];

		if ($ret['order_stat'] != 0) {
			throw new Exception("", 4018);
		}

		if ($orderID == 0) {
			//throw new Exception();
			throw new Exception("请求参数'订单'错误", 4000);
			// return array('errorCode' => 999);
		}

		//暂定一个用户只有一个现金账户,获取该登陆用户的现金账户accountID;
		$sql = " SELECT id , amount,freeze_money" . " FROM " . $this -> sns -> table('cash_account') . " WHERE user_id=" . $userID . " LIMIT 1 ";
		$cashAccountInfo = $this -> mDbAgent -> getRow($sql);
		$accountID = $cashAccountInfo['id'];
		$availMoney = $cashAccountInfo['amount'] - $cashAccountInfo['freeze_money'];

		if ("eduo" == $className) {
			//判断用户现金账户是否有足够余额支付;
			if ($availMoney < $violateRent + $useRent) {
				//余额不足
				throw new Exception("现金账户余额不足.", 4050);
				// return array('errorCode' => 1);
			}
		}

		//取得身份证等信息
		$sql = "SELECT u.phone , u.true_name , a.identity_num FROM " . $this -> sns -> table("user") . " u LEFT JOIN " . $this -> ecs -> table("identity_approve") . " a ON u.uid = user_id WHERE u.uid = $userID";
		$userInfo = $this -> mDbAgent -> getRow($sql);
		$addon = array();
		if ("creditCard" == $className) {
			$addon['trueName'] = $userInfo['true_name'];
			$addon['phone'] = $userInfo['phone'];
			$addon['IDNumber'] = $userInfo['identity_num'];
			$addon['orderId'] = $orderID;
			$addon['userId'] = $userID;
		}

		require_once (Frm::$ROOT_PATH . 'order_charge/classes/OrderCharge.php');
		$oc = new OrderCharge();
		$deposit = $oc -> getDepositWithoutPayInfo($orderNo);
		$violateRent = $deposit[0]['amount'];
		$useRent = $deposit[1]['amount'] + $deposit[2]['amount'];

		/*冻结违章押金,并生成流水号记录数据库中*/
		$violateRentReturn = $payForObj -> moneyOperate('freeze', $accountID, $transaction, $violateRent, 'violateRent', $addon);
		if ($violateRentReturn['error_code'] != 0) {
			//冻结违章押金失败
			throw new Exception("冻结违章押金时,数据库错误", 5002);
		}

		//冻结使用押金金额,并生成流水号记录数据库中
		//moneyOperate($type,$accountID,$transaction_number='',$money='',$remark='');
		if (0 != $useRent) {
			$useRentReturn = $payForObj -> moneyOperate('freeze', $accountID, $rentTransNum, $useRent, 'useRent', $addon);
			if ($useRentReturn['error_code'] != 0) {
				//throw new Exception();
				throw new Exception("冻结使用押金时,数据库错误", 5002);
				// return $useRentReturn;
			}
		}

		/*冻结修订费,并生成流水号记录数据库中*/
		$violateRentReturn = $payForObj -> moneyOperate('freeze', $accountID, $transaction, $modifyFee, 'modifyFee', $addon);
		if ($violateRentReturn['error_code'] != 0) {
			//冻结违章押金失败
			throw new Exception("冻结违章押金时,数据库错误", 5002);
		}

		if ($useRentReturn['error_code'] == 0 && $violateRentReturn['error_code'] == 0) {
			//冻结成功,更新订单状态
			$this -> mDbAgent -> autoExecute($this -> ecs -> table('car_order'), array('order_stat' => 1, 'payment_trans_num' => $rentTransNum), 'update', 'order_id=' . $orderID);
			//增加违章押金记录到违章押金记录表
			$this -> mDbAgent -> autoExecute($this -> sns -> table('violate_deposit_log'), array('money' => $violateRent, 'transaction_number' => $transaction, 'order_id' => $orderID, 'user_id' => $userID));
		}

		$this -> sendOrderPassword($orderID, $userInfo['phone'], $carId, $orderStartTime, $orderEndTime);

		$this -> sendOrderToCar($userID, $carId, $orderID, $orderStartTime, $orderEndTime);

		//还车时间10分钟后检查有无还车
		$this -> setOrderTask($orderID, $orderEndTime + AdvanceTimeOfGetCar, "setRevertTimeOut", "还车超时");

		$violateRentReturn['orderStartTime'] = date('Y-m-d H:i', $orderStartTime);
		$violateRentReturn['orderEndTime'] = date('Y-m-d H:i', $orderEndTime);
		$violateRentReturn['amount']  = (int)$violateRentReturn['amount'];
		$violateRentReturn['money']  = (int)$violateRentReturn['money'];
		$violateRentReturn['freeze_money']  = (int)$violateRentReturn['freeze_money'];

		return $violateRentReturn;
	}

	public function sendOrderPassword($orderId, $phone, $carId, $orderStartTime, $orderEndTime) {
		//@formatter:off
		$sql = "SELECT c.name , t.name AS station FROM " . $this -> ecs -> table("car") . 
			" c LEFT JOIN " . $this -> ecs -> table("station") . " t ON c.station = t.id  WHERE c.id = $carId";
		//@formatter:on
		$row = $this -> mDbAgent -> getRow($sql);
		$carName = $row['name'];
		$station = $row['station'];

		$rand = mt_rand(100000, 999999);

		$sStartTime = date('Y-m-d H:i', $orderStartTime);
		$sEndTime = date('Y-m-d H:i', $orderEndTime);
		$msgContent = "您已成功预定 {$sStartTime} 到 {$sEndTime}，在“{$station}”的车辆： {$carName}，用车密码是：{$rand}，祝您旅途愉快。";

		$msgSender = new SmsSender();
		$msgSender -> send($msgContent, $phone);

		$sql = "UPDATE " . $this -> ecs -> table("car_order") . " SET use_car_password = '{$rand}' WHERE order_id = $orderId";
		$this -> mDbAgent -> query($sql);

		return $rand;
	}

	public function sendOrderToCar($userId, $carId, $orderid, $start_time, $end_time) {
		// @formatter:off
		$sql = "select c.card_number from " . $this -> ecs -> table("vip_card_issue") . " i
						left join " . $this -> ecs -> table("vip_card") . " c on i.vip_card_id=c.id 
						where i.user_id=" . $userId . " order by c.id desc limit 1 ";
		//@formatter:on
		$card = $this -> mDbAgent -> getRow($sql);

		$cardNum = $card['card_number'];
		if (empty($cardNum)) {
			$cardNum = $userId;
		}

		//@formatter:off
		$sql = "SELECT order_id , order_start_time , order_end_time , use_car_password FROM " . $this->ecs->table( "car_order" ) .
		" WHERE ( order_stat = 1 OR order_stat = 2 ) AND user_id = $userId AND car_id = $carId
			AND order_end_time > " . time() . " ORDER BY order_end_time LIMIT 5";
		//@formatter:on
		$ret = $this -> mDbAgent -> getAll($sql);
		//echo $sql;exit;

		$orderArr = array();
		foreach ($ret as $o) {
			$nst = $o['order_start_time'] - strtotime("2009-01-01 00:00:00");
			//因为字符串表示的时间里
			$lst = unpack("N", pack("V", $nst));
			$xst = sprintf("%08X", $lst[1]);

			$net = $o['order_end_time'] - strtotime("2009-01-01 00:00:00");
			$let = unpack("N", pack("V", $net));
			$xet = sprintf("%08X", $let[1]);

			$orderId = $o['order_id'];

			$useCarPassword = $o['use_car_password'];

			$orderArr[] = array('odn' => (int)$orderId, 'cn' => (int)$cardNum, 'pwd' => "{$useCarPassword}", 'bt' => $xst, 'et' => $xet);
		}

		require_once (ROOT_PATH . "../client/classes/CarAgent.php");
		$agent = new CarAgent();
		$agent -> updateOrder(array('car_id' => $carId, 'order' => $orderArr));
	}

	protected function getOrderIdByNo($orderNo) {
		$sql = " SELECT order_id FROM " . $this -> ecs -> table('car_order') . " WHERE order_no=" . $orderNo;

		return $this -> mDbAgent -> getOne($sql);
	}

	/**
	 * 判断现在与指定日期是否距离是不是大于等于 指定的天数距离
	 * @param  string $beforDay "2012-10-19 12:00:00"
	 * @param  int $dayNum      相隔天数
	 * @return bool
	 */
	function workingDayDistance($beforDay, $dayNum) {
		global $ecs;
		global $db;
		// echo $beforDay;
		$intNow = time();

		/*******/
		// $intNow = strtotime("2012-10-19 12:00:00");
		/*******/
		$strToday = date('Y-m-d', $intNow);

		$strBeforDay = date('Y-m-d', $beforDay);
		// var_dump($strBeforDay);
		$sql = " SELECT COUNT(1)
		  		  FROM " . $ecs -> table('vacation') . " WHERE holiday >= " . "'{$strBeforDay}'" . " AND holiday <= " . "'{$strToday}'";
		$holidayDayCount = $db -> getOne($sql);
		$holidaySecondCount = $holidayDayCount * 24 * 3600;
		//如果期间没有跨越假期
		if ($holidayDayCount == 0) {
			// echo floor(($intNow-$beforDay-$holidaySecondCount)/(3600*24));

			if (floor(($intNow - $beforDay - $holidaySecondCount) / (3600 * 24)) < $dayNum) {

				return false;
			} else {
				// var_dump(($intNow-$beforDay-$holidaySecondCount)/3600);
				return true;
			}

		} else {
			//判断开始间,或者运行程序的时刻,是否在节假日中
			$sql = " SELECT holiday
		  		  FROM " . $ecs -> table('vacation') . " WHERE holiday >= " . "'{$strBeforDay}'" . " AND holiday <= " . "'{$strToday}'";
			$ret = $db -> getAll($sql);
			//格式化成array('t1','t2')的结构
			foreach ($ret as $v) {
				$arrHoliday[] = $v['holiday'];
			}
			// var_dump($arrHoliday);
			//判断开始时间有无在节假日中
			$initioSecond = 0;
			if (in_array($strBeforDay, $arrHoliday)) {
				//开头多算的时间(秒)
				$initioSecond = $beforDay - strtotime($strBeforDay);
			}

			//判断查询的此刻是否在节假日中
			$closingSecond = 0;
			if (in_array($strToday, $arrHoliday)) {
				//结尾多算的时间(秒)
				$closingSecond = strtotime($strToday) + 24 * 3600 - $intNow;

			}

			//beforDay 的00:00:00,到intNow的24:00:00　的间隔时间戳
			$distanceDay = floor(($intNow - $beforDay - $holidaySecondCount + $initioSecond + $closingSecond) / (3600 * 24));
			return $distanceDay >= $dayNum;
		}

	}

	function getOrderConfig() {
		global $ecs;
		//'order_interval','order_min_time','order_max_time','order_book_time'

		$sql = " SELECT code,value " . " FROM " . $ecs -> table('shop_config') . " WHERE code='order_interval' OR code='order_min_time' OR code='order_max_time'  OR code='order_book_time' ";

		$ret = $this -> mDbAgent -> getAll($sql);
		$shopConfig = array();
		foreach ($ret as $v) {

			if ($v['code'] == 'order_interval') {
				$shopConfig['order_interval'] = $v['value'];
			} elseif ($v['code'] == 'order_min_time') {
				$shopConfig['order_min_time'] = $v['value'];
			} elseif ($v['code'] == 'order_max_time') {
				$shopConfig['order_max_time'] = $v['value'];
			} elseif ($v['code'] == 'order_book_time') {
				$shopConfig['order_book_time'] = $v['value'];
			}
		}
		return $shopConfig;
	}

	/**
	 * 根据开始结束时间,选择时间与订单时间有交集的订单信息(已换车并且在缓冲时间内的车辆,不选择此订单)
	 * @param  [int] $startTime [description]
	 * @param  [int] $endTime   [description]
	 * @param  string $fields    [description]
	 * @param  string $where     [description]
	 * @return [type]            [description]
	 */
	function getOrderInfoByTime($startTime, $endTime, $fields = 'o.car_id', $where = '') {
		global $db;
		global $ecs;

		if (!empty($where)) {
			$where = " AND " . $where;
		}

		$shopConfig = $this -> getOrderConfig();
		$orderConfigTime = $shopConfig['order_interval'] * 3600;

		$startTime -= $orderConfigTime;
		$endTime += $orderConfigTime;

		$sql = " SELECT $fields ,o.real_end_time
				  FROM " . $ecs -> table('car_order') . ' o ' . " WHERE (( 	o.order_start_time >=" . $startTime . " AND 		o.order_start_time<=" . $endTime . ' ) ' . " OR    (	o.order_end_time>" . $startTime . " AND 		o.order_end_time<" . $endTime . ' ) ' . " OR    (	o.order_start_time<=" . $startTime . " AND       o.order_end_time>=" . $endTime . ')) ' . " AND " . self::getAvailableOrderWhereSQL() . $where;

		// return $db->getAll($sql);

		$arr = $db -> getAll($sql);
		// print_r($arr);
		$now = time();
		foreach ($arr as $k => $v) {
			if ($v['real_end_time'] > 0 && $v['real_end_time'] > ($now - $orderConfigTime)) {
				unset($arr[$k]);
			}
		}
		// print_r($arr);
		return $arr;
	}

	/**
	 * 返回时间段内,被预定的车辆的集合
	 * @param  array $argv['start_time']
	 * @param  array $argv['end_time']
	 * @return array
	 */
	function carListByOrderTime($argv) {
		// print_r($argv);
		$arrRet = $this -> getOrderInfoByTime($argv['start_time'], $argv['end_time'], 'car_id,order_start_time,order_end_time');

		foreach ($arrRet as $k => $v) {

		}
		return $arrRet;
	}

	/**
	 * 车辆是否已被预定
	 * @param  array $argv['car_id']
	 * @param  array $argv['start_time']
	 * @param  array $argv['end_time']
	 * @param  array $argv['user_id']
	 * @return bool
	 */
	public function thisCarHasOrder($argv) {
		$tStart = strtotime($argv['start_time']);
		$tEnd = strtotime($argv['end_time']);
		if ($argv['old_order_id'] > 0) {
			$where = " AND order_id <> " . $argv['old_order_id'];
		} else {
			$where = '';
		}
		$where .= " AND user_id<>" . UserManager::getLoginUserId();

		$arrRet = $this -> getOrderInfoByTime($tStart, $tEnd, 'car_id', " car_id=" . $argv['car_id'] . $where);
		$colNum = count($arrRet);

		return $colNum > 0;
	}

	/**
	 * 用户此刻是否存在订单
	 * @param  array $argv['user_id']
	 * @param  array $argv['start_time']
	 * @param  array $argv['end_time']
	 * @param  array $argv['old_order_id']
	 * @return bool
	 */
	public function userThisTimeHasOrder($argv) {
		global $db;
		global $ecs;
		$argv['start_time'] = strtotime($argv['start_time']);
		$argv['end_time'] = strtotime($argv['end_time']);
		$oldOrderId = !empty($argv['old_order_id']) ? $argv['old_order_id'] : 0;
		// $shopConfig = $this -> getOrderConfig();
		// $orderConfigTime = $shopConfig['order_interval'] * 3600/2;
		// 自身判断不需要缓冲时间
		$orderConfigTime = 0;
		$um = new UserManager();
		$userID = UserManager::getLoginUserId();
		// echo $userID;
		// $userID = $argv['user_id'];

		$argv['start_time'] -= $orderConfigTime;
		$argv['end_time'] += $orderConfigTime;
		// echo date('Y-m-d H:i:s',$argv['start_time']);
		// echo "<br/>";
		// echo date('Y-m-d H:i:s',$argv['end_time']);

		//@formatter:off
		$sql = " SELECT COUNT(1)
				  FROM " . $ecs -> table('car_order') . 'o'.
				  " WHERE (( 	o.order_start_time >=" . $argv['start_time'] .
				  " AND 		o.order_start_time<=" . $argv['end_time'] . ' ) ' .
				  " OR    (	o.order_end_time>" . $argv['start_time'] .
				  " AND 		o.order_end_time<" . $argv['end_time'] . ' ))' .
				  " AND o.user_id=" . $userID . " AND o.order_id<>" . $oldOrderId.
				  " AND ".self::getAvailableOrderWhereSQL();
				  //@formatter:on

		$colNum = $db -> getOne($sql);
		return $colNum > 0;

	}

	function submitCheck($argv) {
		$this -> checkOrderTime($argv);

		$um = new UserManager();
		$um -> identityApproveValidation();
		$um -> violateValidation();
		$um -> drivingLicenceValidation();

		$cm = new CarManager();
		$cm -> carValidation($argv['car_id']);

		$this -> orderValidation($argv);

		if ($argv['medal_id'] > 0) {
			$mc = MedalContainer::getInstance();
			$m = $mc -> getMedalObj($argv['medal_id']);

			$mc -> isMedalCanBeUse($m);
		}

	}

	function orderValidation($argv) {

		if ($this -> thisCarHasOrder($argv)) {
			throw new Exception("车辆该时间内已有订单", 1);
		}
		if ($this -> userThisTimeHasOrder($argv)) {
			throw new Exception("同一用户不能在同一时间内订两辆车", 1);
		}

		if ($argv['old_order_id'] > 0 && $this -> orderHadUsedMedal($argv['old_order_id'])) {
			throw new Exception("", 4043);
		}
	}

	function orderHadOrder() {

	}

	static public function setOrderStatus($orderId, $status) {
		global $ecs, $db;

		$sql = "UPDATE " . $ecs -> table("car_order") . " SET order_stat = $status WHERE order_id = $orderId ";
		$db -> query($sql);
	}

	function getOrderInfoByOrderId($orderID) {
		$sql = " SELECT * FROM " . $this -> ecs -> table("car_order") . " WHERE order_id=" . $orderID;
		return $this -> mDbAgent -> getRow($sql);
	}

	function isOrderNotPay($orderID) {
		$orderInfo = $this -> getOrderInfoByOrderId($orderID);

		if ($orderInfo['order_stat'] == $this -> STATUS_NOT_PAY) {
			return true;
		}
	}

	/**
	 * 添加订单的的定时任务
	 * @param [type] $orderID [description]
	 * @param [type] $time    [description]
	 */
	function setOrderTask($orderID, $time, $methodName, $remark = "") {
		if (!defined("ROOT_PATH")) {
			define('IN_ECS', true);
			require (dirname(__FILE__) . '/../../cp/includes/init.php');
		}
		include_once (ROOT_PATH . '../client/classes/Timer.php');

		$t = new Timer();
		//function addTask($exceTime,$className,$methodName,$phpURI,$param,$taskType="")
		$t -> addTask($time, 'OrderManager', $methodName, ROOT_PATH . '../client/classes/OrderManager.php', $orderID, $remark);
	}

	/**
	 * 修改订单未支付的订单的状态为支付超时
	 * @param [int] $orderID [订单id]
	 */
	function setPayforTimeOut($orderID) {
		$status = self::$STATUS_PAY_TIMEOUT;
		$sql = "UPDATE " . $this -> ecs -> table("car_order") . " SET order_stat = $status WHERE order_id = $orderID " . " AND order_stat=" . self::$STATUS_NOT_PAY;
		$this -> mDbAgent -> query($sql);
	}

	function setRevertTimeOut($orderID) {
		$order = $this -> getOrder($orderID);
		if (self::$STATUS_ORDERED == $order['order_stat']) {
			$status = self::$STATUS_COMPLETED;
		} else if (self::$STATUS_STARTED == $order['order_stat']) {
			$status = self::$STATUS_REVERT_TIMEOUT;
		} else {
			return;
		}

		$this -> setOrderStatus($orderID, $status);
	}

	/**
	 * 取得订单跟支付相关的信息。在我的订单列表里调用，返回后调用提交订单成功的页面。
	 */
	public function getOrderPaymentInfo($argv) {
		$rc = new RentComputer();
		$frozenIllegalDeposit = $rc -> getFrozenIllegalDeposit();

		$orderId = $argv['order_id'];

		//0表示未支付
		$sql = "SELECT o.order_id , o.order_no , o.order_real_cost , o.violate_real_rent , o.add_time FROM " . $this -> ecs -> table("car_order") . " o WHERE o.order_id = $orderId AND o.order_stat = 0";
		$row = $this -> mDbAgent -> getRow($sql);

		$orderTime = $row['add_time'];
		$orderId = $row['order_id'];
		$orderAmount = $row['order_real_cost'] + $row['violate_real_rent'];

		$upp = new UnionPayMobileManager();
		$strOrderTime = date('YmdHis', $orderTime);
		$uppayOrderInfo = $upp -> getUPPayOrderInfo($strOrderTime, $orderId, $orderAmount);

		//@formatter:off
		return array(
		'orderId' => $orderId,
		'orderSn' => $row['order_no'],
		'rent' => array(
		'frozen' => $frozenIllegalDeposit,
		'budget_deposit' => $row['order_real_cost'],
		'illegal_deposit' => $row['violate_real_rent']
		),
		'uppayOrderInfo' => $uppayOrderInfo
		);
		//@formatter:on
	}

	static public function getAvailableOrderWhereSQL() {
		//@formatter:off
		return "  (o.order_stat=" . OrderManager::$STATUS_NOT_PAY . 
			" OR o.order_stat=" . OrderManager::$STATUS_ORDERED . 
			" OR o.order_stat=" . OrderManager::$STATUS_STARTED . 
			" OR o.order_stat=" . OrderManager::$STATUS_REVERT_TIMEOUT . ") ";
			//@formatter:on

	}

	public function getCreator($orderId) {
		$sql = "SELECT o.user_id FROM " . $this -> ecs -> table("car_order") . " o WHERE o.order_id = $orderId ";
		return $this -> mDbAgent -> getOne($sql);
	}

	public function getCancelOrderFee($argv, $order = null) {
		if (null == $order) {
			$order = $this -> getOrder($argv['order_id']);
		}

		$now = time();

		require_once (ROOT_PATH . "../client/classes/SysConfig.php");
		$sysConfig = new SysConfig();
		$config = $sysConfig -> getAllValue('cancel_order');
		if ($now + (3600 * $config['time']) > $order['start_time']) {
			return $order['order_real_cost'] * $config['percent'] / 100;
		}

		return 0;
	}

	public function cancel($argv) {
		$orderId = $argv['order_id'];

		$order = $this -> getOrder($orderId);
		if (0 != $order['order_stat'] && 1 != $order['order_stat']) {
			throw new Exception('the order with the status can not be canceled.', 4000);
		}

		if (time() >= $order['start_time']) {
			throw new Exception('', 4012);
		}

		$this -> setOrderStatus($orderId, 4);
		//4代表已取消

		$cancelFee = $this -> getCancelOrderFee(null, $order);

		//释放租车押金
		$accountContainer = AccountManagerContainer::getInstance();
		$account = $accountContainer -> getAccountManager("eduo");
		$accountId = $account -> getAccountId($order['user_id']);
		$account -> moneyOperate("freezeComplete", $accountId, $order['payment_trans_num'], $cancelFee);

		//释放违章押金
		require_once (ROOT_PATH . "../client/classes/SecondSettlement.php");
		$ss = new SecondSettlement();
		$ss -> getDistanceDayOrderInfo(time(), 7, 3, $order['user_id'], $illegalDeposit);

		//@formatter:off
		return array(
		'cancelOrderFee'=> $cancelFee,
		'releaseDeposit'=>$order['order_real_cost'] - $cancelFee ,
		'releaseIllegalDeposit'=>$illegalDeposit
		);
		//@formatter:on
	}

	function getConfigHourKm() {
		global $db;
		global $ecs;
		$sql = " SELECT code , value " . " FROM " . $ecs -> table('shop_config') . " WHERE code='hour_km' OR code='day_km'";
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

	static public function getAllOldOrderCost($orderID) {
		global $db;
		global $ecs;
		static $allMoney;
		// $sql = " SELECT order_real_cost ,old_order_id ,modify_times" . " FROM " . $ecs -> table('car_order') . " WHERE order_id=" . $orderID;
		// $ret = $db -> getRow($sql);
		// $allMoney += $ret['order_real_cost'];
		// $modifyTimes=$ret['modify_times'];

		do {
			$sql = " SELECT order_real_cost ,old_order_id,modify_times " . " FROM " . $ecs -> table('car_order') . " WHERE order_id=" . $orderID;
			$ret = $db -> getRow($sql);
			$allMoney += $ret['order_real_cost'];
			$orderID = $ret['old_order_id'];
			$modifyTimes = $ret['modify_times'];
		} while($modifyTimes > 0);

		// while ($modifyTimes > 0 ) {

		// }
		return $allMoney;
	}

	function orderHadUsedMedal($orderID) {
		global $db;
		global $ecs;

		$sql = " SELECT medal_id " . " FROM " . $ecs -> table('car_order') . " WHERE order_id=" . $orderID;
		$medalID = $db -> getOne($sql);

		if ($medalID > 0) {
			true;

		} else {
			return false;
		}
	}

	function canModifyStartTime($argv) {
		global $ecs;
		global $db;
		// 判断 修改订单时
		$oldOrderId = $argv['old_order_id'];
		$start_time = strtotime($argv['start_time']);

		$sql = "SELECT order_start_time,order_end_time FROM " . $ecs -> table("car_order") . " WHERE order_id = '$oldOrderId'";
		$res = $this -> mDbAgent -> getRow($sql);
		if ($start_time > $res['order_start_time'] && $start_time < $res['order_end_time']) {
			throw new Exception("", 4019);
		}
	}

	function checkOrderTime($argv) {
		$start_time = strtotime($argv['start_time']);
		$end_time = strtotime($argv['end_time']);
		$now = time();

		if ($start_time > $end_time) {
			throw new Exception("", 4200);

		}

		if ($start_time + 60 < $now) {
			throw new Exception("", 4017);
		}
	}

}
?>