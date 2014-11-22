<?php

require_once (Frm::$ROOT_PATH . 'client/classes/MemberAuth.php');

class MemberClient extends MemberAuth {

	public function __construct() {
		parent::__construct();

		$conf = Frm::getInstance() -> getConf();
		$timezone = $conf -> getLocalTimezone();
		date_default_timezone_set($timezone);
	}

	public function calculateDeposit($argv) {
		$carId = $argv['car_id'];
		if (!is_numeric($carId)) {
			throw new Exception('', 4000);
		}
		/**
		 * $Author: 邬国轩
		 * $time:	2013-04-24 11:35:46Z
		 * $modify:	检查是否有订此车的权限
		 */
		require_once (Frm::$ROOT_PATH . 'resource/classes/CarGroupManager.php');
		$cgm=new CarGroupManager();
		$bannerCarIds=$cgm->getBanerCarIds(UserManager::getLoginUserId());
		if (in_array($carId, $bannerCarIds)) {
			throw new Exception("", 4240);		
		}
		/****/
		
		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$om = new OrderManager();
		$oldOrderId = $argv['old_order_id'];
		if (!is_numeric($oldOrderId)) {
			$oldOrderId = -1;
		}
		$oldOrder = null;
		if ($oldOrderId > 0) {
			$oldOrder = $om -> getOrder($oldOrderId);
		}

		$startTime = strtotime(trim($argv['start_time']));
		$endTime = strtotime(trim($argv['end_time']));
		$userId = UserManager::getLoginUserId();

		require_once (Frm::$ROOT_PATH . 'system/classes/SysConfig.php');
		$orderConf = SysConfig::getOrderConfig();
		$om -> createCheck($userId, $carId, $startTime, $endTime, -1, $oldOrder, $orderConf);

		require_once (Frm::$ROOT_PATH . 'order_charge/classes/DepositComputer.php');
		$dc = new DepositComputer();
		$violateDeposit = $dc -> getViolateDeposit($userId, $carId);


		$useDeposit = $dc -> getUseDeposit($userId, $carId, $startTime, $endTime);
		// var_dump($useDeposit);
		$oldDepositCount = 0;
		if (null != $oldOrder) {
			require_once (Frm::$ROOT_PATH . 'order_charge/classes/OrderCharge.php');
			$oc = new OrderCharge();
			$ret = $oc -> getDepositPaiedGroupedCount($oldOrder -> order_no);
			// var_dump($ret);
			$useDeposit['CarPrice'] -= $ret['useMileDeposit'];
			if ($ret['useTimeDeposit']!=null) {
				$useDeposit['DayPrice'] -= $ret['useTimeDeposit'];
			}
			$oldDepositCount += $ret['useMileDeposit'] + $ret['useTimeDeposit'];
			// var_dump($oldDepositCount);
		}

		$allDeposit = $useDeposit['CarPrice'] + $useDeposit['DayPrice'];
		$allDeposit -= $oldDepositCount;
		if ($allDeposit < 0) {
			$allDeposit = 0;
		}
		$allDeposit += $violateDeposit['violateDeposit'];

		require_once (Frm::$ROOT_PATH . 'marketing/medal/classes/MedalContainer.php');
		$mc = new MedalContainer();
		$medalArr = $mc -> getUserGainedMedalArr($userId);

		//@formatter:off
		return array('frozen' => $violateDeposit['frozen'], 'use_deposit' => $useDeposit['CarPrice'], 'time_deposit' => $useDeposit['DayPrice'], 'violate_deposit' => $violateDeposit['violateDeposit'], 'total_deposit' => $allDeposit, 'modify_fee' => 0, 'my_medal' => $medalArr);
		//@formatter:on
	}

	public function createOrder($argv) {
		$carId = $argv['car_id'];
		if (!is_numeric($carId)) {
			throw new Exception('', 4000);
		}

		$medalId = $argv['medal_id'];
		if (empty($medalId)) {
			$medalId = -1;
		} else {
			if (!is_numeric($medalId)) {
				throw new Exception('', 4000);
			}
		}

		$oldOrderId = $argv['old_order_id'];
		if (empty($oldOrderId)) {
			$oldOrderId = -1;
		} else {
			if (!is_numeric($oldOrderId)) {
				throw new Exception('', 4000);
			}
		}

		/**
		 * $Author: 邬国轩
		 * $time:	2013-04-24 11:35:46Z
		 * $modify:	检查是否有订此车的权限
		 */
		require_once (Frm::$ROOT_PATH . 'resource/classes/CarGroupManager.php');
		$cgm=new CarGroupManager();
		$bannerCarIds=$cgm->getBanerCarIds(UserManager::getLoginUserId());
		if (in_array($carId, $bannerCarIds)) {
			throw new Exception("", 4240);		
		}
		/****/


		$startTime = strtotime(trim($argv['start_time']));
		$endTime = strtotime(trim($argv['end_time']));
		$userId = UserManager::getLoginUserId();

		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$om = new OrderManager();
		$order = $om -> create($userId, $carId, $startTime, $endTime, $medalId, $oldOrderId);

		$orderCharge = new OrderCharge();
		//是否应该用orderId找需要支付的金额
		$dCount = $orderCharge -> getDepositWithoutPayCount($order -> order_no);

		// require_once (Frm::$ROOT_PATH . 'PayAPI/upmobile/classes/UnionPayMobileManager.php');
		// $upm = new UnionPayMobileManager();
		// $orderInfo = $upm -> getUPPayOrderInfo($order -> order_id, $dCount);

		$um = new UserManager();
		$hasSetSuperPassword = $um -> hasSetSuperPassword($userId);

		require_once (Frm::$ROOT_PATH . 'moneyAccountManager/CashAccountManager.php');
		$cam = new CashAccountManager();
		$accountInfo = $cam -> getAccountInfo($cam -> getAccountId($userId));
		$amount = $accountInfo['amount'] - $accountInfo['freeze_money'];
		$isAfterPayUser = $cam -> isAfterPayUser($userId);

		//add by yangbei 2013-06-14 增加邮件通知
		$userGroup = $isAfterPayUser ? 'guanghua' : 'common';
		require_once (Frm::$ROOT_PATH . 'system/mail/send.php');
		$mail = new Send();
		$userInfo = $um ->getUser($userId);
		// var_dump($userInfo);
		$mail->mail('dingdan@eduoauto.com','新订单通知（未支付）','用户:'.$userInfo['true_name'].'在'.$argv['start_time'].'-'.$argv['end_time'].'预定车辆:'.$carId);
		//add by yangbei 2013-06-14 增加邮件通知

		//@formatter:off
		return array('orderId' => $order -> order_id, 'orderNo' => $order -> order_no, 'deposit' => $dCount, 'uppayOrderInfo' => $orderInfo, 'hasSetSuperPassword' => $hasSetSuperPassword, 'amount' => (int)$amount, 'user_group' => $userGroup);
		//@formatter:on
	}

	public function getOrderDeposit($argv) {
		$orderId = $argv['order_id'];
		if (!is_numeric($orderId) || $orderId < 0) {
			throw new Exception('', 4000);
		}

		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$om = new OrderManager();
		$order = $om -> getOrder($orderId);

		require_once (Frm::$ROOT_PATH . 'order_charge/classes/OrderCharge.php');
		$orderCharge = new OrderCharge();
		$deposit = $orderCharge -> getDepositWithoutPayCount($order -> order_no);

		require_once (Frm::$ROOT_PATH . 'PayAPI/upmobile/classes/UnionPayMobileManager.php');
		$upm = new UnionPayMobileManager();
		$orderInfo = $upm -> getUPPayOrderInfo($order -> order_id, $deposit);

		$userId = UserManager::getLoginUserId();
		$um = new UserManager();
		$hasSetSuperPassword = $um -> hasSetSuperPassword($userId);

		require_once (Frm::$ROOT_PATH . 'moneyAccountManager/CashAccountManager.php');
		$cam = new CashAccountManager();
		$accountInfo = $cam -> getAccountInfo($cam -> getAccountId($userId));
		$amount = $accountInfo['amount'] - $accountInfo['freeze_money'];

		//@formatter:off
		return array('orderId' => $orderId, 'orderNo' => $order -> order_no, 'deposit' => $deposit, 'uppayOrderInfo' => $orderInfo, 'hasSetSuperPassword' => $hasSetSuperPassword, 'amount' => (int)$amount);
		//@formatter:on
	}

	public function getNearby($argv) {
		$lng = $argv['longi'];
		$lat = $argv['lati'];
		$width = $argv['screen_width'];
		$height = $argv['screen_height'];
		if (!is_numeric($lng) || !is_numeric($lat) || !is_numeric($width) || !is_numeric($height)) {
			throw new Exception('', 4000);
		}

		//add by golf 增加折扣信息
		$hourDiscount=$this->getHourDiscount();
		require_once (Frm::$ROOT_PATH . 'resource/classes/CarPosManager.php');
		$pm = new CarPosManager();
		$carInfo = $pm -> getNearbyCarArr($lng, $lat, $width, $height);

		$ret=$this -> getMapShowCars($carInfo);
		foreach ($ret as $k => $v) {
			$ret[$k]['hourDiscount']=$hourDiscount;
		}
		return $ret;
		//end
	}

	public function getCarListByRang($argv) {
		$startLat = $argv['start_lati'];
		$endLat = $argv['end_lati'];
		$startLng = $argv['start_longi'];
		$endLng = $argv['end_longi'];
		if (!is_numeric($startLat) || !is_numeric($endLat) || !is_numeric($startLng) || !is_numeric($endLng)) {
			throw new Exception('', 4000);
		}

		require_once (Frm::$ROOT_PATH . 'resource/classes/CarPosManager.php');
		$pm = new CarPosManager();
		$carInfo = $pm -> getCarListByRange($startLng, $startLat, $endLng, $endLat);
		//add by golf 增加折扣信息
		$hourDiscount=$this->getHourDiscount();
		$ret=$this -> getMapShowCars($carInfo);
		foreach ($ret as $k => $v) {
			$ret[$k]['hourDiscount']=$hourDiscount;
		}
		return $ret;
		//end
	}

	protected function getMapShowCars($carInfo) {
		require_once (Frm::$ROOT_PATH . 'order/classes/CarFollowedManager.php');
		$followManager = new CarFollowedManager();
		$followArr = $followManager -> getFollowedCarIdList(UserManager::getLoginUserId());

		$carIdArr = array();
		foreach ($carInfo as $car) {
			$carIdArr[] = $car -> id;
		}

		if (!empty($carIdArr)) {
			require_once (Frm::$ROOT_PATH . 'order_charge/classes/PriceManager.php');
			$priceManager = new PriceManager();
			$rangArr = $priceManager -> getCarArrPriceRange($carIdArr);
		}

		$car = array();
		$carArr = array();
		foreach ($carInfo as $k => $v) {
			foreach ($v as $kk => $vv) {
				$car[$kk] = $vv;
			}
			$car['followed'] = in_array($car['id'], $followArr) ? 1 : 0;
			$car['timePriceMin'] = $rangArr[$car['id']]['timePriceMin'];
			$car['timePriceMax'] = $rangArr[$car['id']]['timePriceMax'];
			$car['milePrice'] = $rangArr[$car['id']]['milePrice'];
			$carArr[] = $car;

		}
		return $carArr;
	}

	public function getCarInfoAndOrderTime($argv) {
		$carId = $argv['car_id'];
		if (!is_numeric($carId)) {
			throw new Exception('', 4000);
		}
		$oldOrderId = $argv['old_order_id'];
		if (!is_numeric($oldOrderId)) {
			$oldOrderId = -1;
		}

		require_once (Frm::$ROOT_PATH . 'resource/classes/CarDevice.php');
		$device = new CarDevice();
		$device -> carId = $carId;
		$carList = $this -> getCarList(array($device));
		$car = $carList[0];

		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$orderManager = new OrderManager();
		$orderTimeArr = $orderManager -> getOrderTimeInDay($carId, time(), $oldOrderId);

		$car['orderTime'] = $orderTimeArr;

		return $car;
	}

	protected function getCarList($deviceArr) {
		$carIdArr = array();
		$carIdIndexDeviceArr = array();
		foreach ($deviceArr as $device) {
			$carIdArr[] = $device -> carId;
			$carIdIndexDeviceArr[$device -> carId] = $device;
		}

		require_once (Frm::$ROOT_PATH . 'resource/classes/CarManager.php');
		$carManager = new CarManager();
		$carArr = $carManager -> getCarList($carIdArr);

		require_once (Frm::$ROOT_PATH . 'order_charge/classes/PriceManager.php');
		$priceManager = new PriceManager();
		$rangArr = $priceManager -> getCarArrPriceRange($carIdArr);

		require_once (Frm::$ROOT_PATH . 'order/classes/CarFollowedManager.php');
		$followManager = new CarFollowedManager();
		$followArr = $followManager -> getCarArrFollowStatus($carIdArr, UserManager::getLoginUserId());

		$ret = array();
		foreach ($carArr as $car) {
			//@formatter:off
			$row = array('id' => $car -> id, 'status' => $car -> status, 'gearbox' => $car -> gearbox, 'name' => $car -> name, 'number' => $car -> number, 'brand' => $car -> brand, 'model' => $car -> model, 'icon' => $car -> icon, 'station' => $car -> station, 'timePriceMin' => $rangArr[$car -> id]['timePriceMin'], 'timePriceMax' => $rangArr[$car -> id]['timePriceMax'], 'milePrice' => $rangArr[$car -> id]['milePrice'], 'longitude' => $carIdIndexDeviceArr[$car -> id] -> longitude, 'latitude' => $carIdIndexDeviceArr[$car -> id] -> latitude, 'followed' => $followArr[$car -> id]);
			//@formatter:on
			$ret[] = $row;
		}

		return $ret;
	}

	public function getPreferenceCarList($argv) {
		$userId = UserManager::getLoginUserId();

		require_once (Frm::$ROOT_PATH . 'order/classes/CarFollowedManager.php');
		$followManager = new CarFollowedManager();
		$carIdArr = $followManager -> getFollowedCarIdList($userId);
		if (empty($carIdArr)) {
			return array();
		}

		require_once (Frm::$ROOT_PATH . 'resource/classes/CarManager.php');
		$carManager = new CarManager();
		$carArr = $carManager -> getCarList($carIdArr);

		require_once (Frm::$ROOT_PATH . 'order_charge/classes/PriceManager.php');
		$priceManager = new PriceManager();
		$rangArr = $priceManager -> getCarArrPriceRange($carIdArr);
		//获取用户组,获取用户组折扣
		$hourDiscount=$this->getHourDiscount();
		//end

		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$orderManager = new OrderManager();
		$ret = array();
		$now = time();
		foreach ($carArr as $car) {
			$orderTimeArr = $orderManager -> getOrderTimeInDay($car -> id, $now);
			//@formatter:off
			$row = array('id' => $car -> id, 'status' => $car -> status, 'gearbox' => $car -> gearbox, 'name' => $car -> name, 'number' => $car -> number, 'brand' => $car -> brand, 'model' => $car -> model, 'icon' => $car -> icon, 'station' => $car -> station, 'timePriceMin' => $rangArr[$car -> id]['timePriceMin'], 'timePriceMax' => $rangArr[$car -> id]['timePriceMax'], 'milePrice' => $rangArr[$car -> id]['milePrice'], 'orderTime' => $orderTimeArr,'hourDiscount'=>$hourDiscount);
			//@formatter:on
			$ret[] = $row;
		}

		return $ret;
	}

	public function getCarOrderTime($argv) {
		$carId = $argv['car_id'];
		if (!is_numeric($carId)) {
			throw new Exception('', 4000);
		}

		//在地图上点击订车按钮，调用该函数
		require_once (Frm::$ROOT_PATH . 'order/classes/CarFollowedManager.php');
		$cfm = new CarFollowedManager();
		$cfm -> follow(UserManager::getLoginUserId(), $carId);

		$now = time();
		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$orderManager = new OrderManager();
		$orderTimeArr = $orderManager -> getOrderTimeInDay($carId, $now);

		return $orderTimeArr;
	}

	public function openDoor($argv) {
		$superPassword = $argv['super_password'];

		$userId = UserManager::getLoginUserId();

		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$om = new OrderManager();
		$om -> orderPickCar($userId, $superPassword);
	}
	//add by  yangbei 2013-05-09 增加天物取车函数
	public function twOpenDoor($argv) {
		$userId = UserManager::getLoginUserId();
		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$om = new OrderManager();
		$order = $om -> getUserAvailableOrder($userId);
		if (null == $order) {
			throw new Exception('', 4005);
		}

		if (Order::$STATUS_ORDERED == $order -> order_stat) {
			if ($order -> order_start_time > time()) {
				throw new Exception('', 4219);
			}
		}
		//天物的不需要输入超级密码,所以订单合法就直接开门 $superPassword=123456
		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$om = new OrderManager();
		$om -> twOrderPickCar($userId);
	}
	//add by  yangbei 2013-05-09 
	public function logout() {
		require_once (Frm::$ROOT_PATH . 'user/classes/UserManager.php');
		$um = new UserManager();
		$um -> logout();
	}

	public function getUserAvailableOrder() {
		$userId = UserManager::getLoginUserId();

		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$om = new OrderManager();
		$order = $om -> getUserAvailableOrder($userId);
		if (null == $order) {
			throw new Exception('', 4005);
		}

		if (Order::$STATUS_ORDERED == $order -> order_stat) {
			if ($order -> order_start_time > time()) {
				throw new Exception('', 4219);
			}
		}

		require_once (Frm::$ROOT_PATH . 'order_charge/classes/OrderCharge.php');
		$orderCharge = new OrderCharge();
		$dCount = $orderCharge -> getDepositWithoutPayCount($order -> order_no);

		if (Order::$STATUS_NOT_PAY == $order -> order_stat) {
			require_once (Frm::$ROOT_PATH . 'PayAPI/upmobile/classes/UnionPayMobileManager.php');
			$upm = new UnionPayMobileManager();
			$orderInfo = $upm -> getUPPayOrderInfo($order -> order_id, $dCount);
		}

		$order = array('order_id' => $order -> order_id, 'order_no' => $order -> order_no, 'order_stat' => $order -> order_stat, 'car_id' => $order -> car_id, 'order_start_time' => $order -> order_start_time);

		return array('order' => $order, 'deposit' => $dCount, 'uppayOrderInfo' => $orderInfo);
	}

	public function getUserAvailableCar() {
		$userId = UserManager::getLoginUserId();

		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$om = new OrderManager();
		$order = $om -> getUserAvailableOrder($userId);
		if (null == $order) {
			throw new Exception('', 4005);
		}

		require_once (Frm::$ROOT_PATH . 'resource/classes/CarDeviceManager.php');
		$deviceManager = new CarDeviceManager();
		$device = $deviceManager -> getDeviceByCar($order -> car_id);

		$ret = $this -> getCarList(array($device));

		$order = array('order_id' => $order -> order_id, 'order_no' => $order -> order_no, 'order_stat' => $order -> order_stat, 'car_id' => $order -> car_id, 'order_start_time' => $order -> order_start_time,'order_end_time'=>$order->order_end_time);

		return array('order' => $order, 'car' => $ret[0]);
	}
	//add by yangbei 2013-05-13	增加:获取天物可用订单,包括明天,后天.......
	public function twGetUserAvailableCar() {
		$userId = UserManager::getLoginUserId();

		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$om = new OrderManager();
		$order = $om -> twGetUserAvailableOrder($userId);
		if (null == $order) {
			throw new Exception('', 4005);
		}

		require_once (Frm::$ROOT_PATH . 'resource/classes/CarDeviceManager.php');
		$deviceManager = new CarDeviceManager();
		$device = $deviceManager -> getDeviceByCar($order -> car_id);

		$ret = $this -> getCarList(array($device));

		$order = array('order_id' => $order -> order_id, 'order_no' => $order -> order_no, 'order_stat' => $order -> order_stat, 'car_id' => $order -> car_id, 'order_start_time' => $order -> order_start_time,'order_end_time'=>$order->order_end_time);

		return array('order' => $order, 'car' => $ret[0]);
	}
	//add by yangbei 2013-05-13

	public function closeDoor($argv) {
		$userId = UserManager::getLoginUserId();

		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$om = new OrderManager();
		$order = $om -> orderCloseDoor($userId);

		if (Order::$STATUS_COMPLETED == $order -> order_stat) {
			$ret = $this -> getBill(array('order_no' => $order -> order_no));
			$ret['order'] = $order;
		} else {
			$ret = array('order' => $order, 'bill' => null);
		}

		return $ret;
	}

	//add 天物鸣笛功能,在订单缓冲时间前才可以鸣笛
	public function twRingCar($argv)
	{
		//获取用户的有效订单,包括现在到未来所有时间内的订单
		$orderInfo = $this->twGetUserAvailableCar();
		
		//判断当前时间在不在订单时间上,否则不能鸣笛
		$order=$orderInfo['order'];
		require_once(Frm::$ROOT_PATH.'system/classes/SysConfig.php');
		require_once(Frm::$ROOT_PATH.'order/classes/Order.php');
		$sy=SysConfig::getOrderConfig();

		$bufferTime=$sy['order_interval']*60;
		$now = time()+$bufferTime;
		// echo sprintf("现在:%s,订单:%s",date('H:i:s',$now),date('H:i:s',$order['order_start_time']));
		
		if (Order::$STATUS_ORDERED == $order['order_stat']) {
			if ($order['order_start_time'] > $now) {
				throw new Exception('', 4219);
			}
		}
		$carId=$order['car_id'];
		$this->ringCar(array('car_id'=>$carId));


	}
	public function setSuperPassword($argv) {
		$password = $argv['password'];
		$userId = UserManager::getLoginUserId();

		$um = new UserManager();
		$um -> setSuperPassword($userId, $password);
	}
	// add by yangbei 增加修改超级密码接口
	public function modifySuperPassword($argv) {
		$password = $argv['password'];
		$oldpassword = $argv['oldpassword'];
		$userId = UserManager::getLoginUserId();

		$um = new UserManager();
		$um -> modifySuperPassword($userId, $password,$oldpassword);
	}
	public function hasSetSuperPassword($argv) {
		$userId = UserManager::getLoginUserId();

		$um = new UserManager();
		return $um -> hasSetSuperPassword($userId);
	}

	/**
	 * 保存车辆检查图片
	 */
	public function saveOrderBugImg($argv) {
	}

	public function submitEvaluate($argv) {
		$orderNo = $argv['order_no'];
		$content = $argv['content'];
		$level = $argv['level'];
		//modify by yangbei 2013-07-02 修改文件名
		// require_once (Frm::$ROOT_PATH . 'marketing/evaluation/classes/OrderevaluationManager.php');
		require_once (Frm::$ROOT_PATH . 'marketing/evaluation/classes/OrderEvaluationManager.php');
		$oem = new OrderEvaluationManager();
		$oem -> addEvaluation($orderNo, $level, $content);
	}

	public function getUserInfo($argv) {
		$userId = UserManager::getLoginUserId();
		$um = new UserManager();
		$userInfo = $um -> getUser($userId);
		return $userInfo;

	}

	//支付回调
	// public function payforOrderCallback($argv) {
	// 	$orderId = $argv['order_id'];
	// 	$ext_id = $argv['ext_id'];
	// 	$payMentType = $argv['payment_type'];
	// 	if ($orderId != ceil($orderId)) {
	// 		//参数错误,返回错误信息给支付平台,并保存错误状态到本地数据库,供日后重发支付成功消息
	// 		throw new Exception("", 4000);
	// 	}
	// 	//假接口,假定消息码都是成功的
	// 	if (true) {
	// 		# code...
	// 		// require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
	// 		// require_once (Frm::$ROOT_PATH . 'order_charge/classes/OrderCharge.php');
	// 		// $om = new OrderManager();
	// 		// $om -> payforOrder($orderId,$ext_id,OrderCharge::$PAYMENT_TYPE_CASH);

	// 		//插入已支付的charge记录到balance表和charge2balance表
	// 		require_once (Frm::$ROOT_PATH . 'order_charge/classes/OrderCharge.php');
	// 		$orderCharge = new OrderCharge();
	// 		$chargeIdArr['idArr'] = $orderCharge -> getChargeIdsByOrderId($orderId);
	// 		$amount = $orderCharge -> getOrderChargeMoneyByOrderId($orderId);

	// 		$orderCharge -> orderPayment($orderId, $amount, $chargeIdArr, $ext_id, $payMentType);

	// 	}

	// }

	public function getNearestStationsCar($argv) {
		$lng = (int)($argv['lng'] * 1000000);
		$lat = (int)($argv['lat'] * 1000000);

		require_once (Frm::$ROOT_PATH . 'resource/classes/CarManager.php');
		$carManager = new CarManager();

		$stations = $carManager -> getNearestStation($lng, $lat);

		$stationIds = array();
		foreach ($stations as $v) {
			$stationIds[] = $v['id'];
		}

		$carArr = $carManager -> getStationsCars($stationIds);

		require_once (Frm::$ROOT_PATH . 'order/classes/CarFollowedManager.php');
		$fm = new CarFollowedManager();
		$userId = UserManager::getLoginUserId();
		$followsArr = $fm -> getFollowedCarIdList($userId);

		foreach ($carArr as $k => $c) {
			foreach ($c as $key => $value) {
				if (is_numeric($key) == $key) {
					$carArr[$k][$key]['is_follow'] = in_array($key, $followsArr) ? 1 : 0;
				}
			}
		}

		return $carArr;

	}

	public function getInvoiceInfo() {
		$userId = UserManager::getLoginUserId();

		require_once (Frm::$ROOT_PATH . 'order_charge/classes/InvoiceManager.php');
		$im = new InvoiceManager();
		$info = $im -> getInvoiceInfo($userId);

		return $info;
	}

	public function askForInvoice($argv) {
		$invoiceTitle = $argv['invoice_title'];
		$invoiceAddress = $argv['invoice_address'];
		$asDefault = 'true' == $argv['as_default'];
		$contact = $argv['contact'];
		$phone = $argv['phone'];
		$orderId = $argv['order_id'];

		$userId = UserManager::getLoginUserId();

		require_once (Frm::$ROOT_PATH . 'order_charge/classes/InvoiceManager.php');
		$im = new InvoiceManager();
		$im -> askForInvoice($orderId, $invoiceTitle, $invoiceAddress, $contact, $phone, $asDefault, $userId);
	}

	/**
	 * 现金账户充值请求
	 */
	public function cashAccountChargeReq($argv) {
		$userId = UserManager::getLoginUserId();

		$db = Frm::getInstance() -> getDb();

		$sql = "SELECT u.true_name FROM edo_user u 
				WHERE u.uid = $userId ";
		$true_name = $db -> getOne($sql);
		// if (!in_array($true_name, array('李明', '林雪涛', '李益明', '游杰今'))) {
		// 	throw new Exception('', 6000);
		// }

		$um = new UserManager();
		if (!$um -> hasSetSuperPassword($userId)) {
			throw new Exception('', 4227);
		}

		$amount = $argv['amount'] * 100;
		//判断是否金额最小值为1元到5000元之间
		if (!is_numeric($amount) || $amount < 100 || $amount > 500000) {
			throw new Exception('', 4000);
		}

		require_once (Frm::$ROOT_PATH . 'moneyAccountManager/CashAccountManager.php');
		$cam = new CashAccountManager();
		if ('web' == $argv['t']) {
			return $cam -> chargeRequst($userId, $amount, 'web');
		} else {
			return $cam -> chargeRequst($userId, $amount);
		}

	}

	public function getConsumeDetail() {
		$userId = UserManager::getLoginUserId();

		$opStr = array('charge' => '充值', 'consume' => '消费', 'freeze' => '预授权', 'freezeComplete' => '预授权完成', 'freezeCancel' => '预授权取消', 'withdraw' => '提现');

		require_once (Frm::$ROOT_PATH . 'moneyAccountManager/CashAccountManager.php');
		$cam = new CashAccountManager();
		$accountId = $cam -> getAccountId($userId);
		$caDetail = $cam -> detail($accountId);
		foreach ($caDetail as &$row) {
			$row['time'] = date('Y-m-d H:i', $row['time']);
			$row['use_type'] = $opStr[$row['use_type']];
		}

		return $caDetail;
	}

	public function getMedalList() {
		$userId = UserManager::getLoginUserId();

		require_once (Frm::$ROOT_PATH . 'marketing/medal/classes/MedalContainer.php');
		$mc = new MedalContainer();
		return $mc -> getUserMedalArr($userId);
	}

	public function gainMedal($argv) {
		$medalId = $argv['id'];
		if (!is_numeric($medalId)) {
			throw new Exception('', 4000);
		}

		$userId = UserManager::getLoginUserId();

		require_once (Frm::$ROOT_PATH . 'marketing/medal/classes/MedalContainer.php');
		$mc = new MedalContainer();
		$mc -> gainMedal($userId, $medalId);
	}

	// //测试现金账户用的临时代码
	// public function payforOrder($argv) {
	// 	$payType = 'eduo';

	// 	$orderId = intval($argv['order_id']);
	// 	//判断订单是否已经支付

	// 	//计算需要支付的金额
	// 	require_once (Frm::$ROOT_PATH . 'order_charge/classes/OrderCharge.php');
	// 	$orderCharge = new OrderCharge();
	// 	//获取该订单需要支付的押金
	// 	$payDepositMoney = $orderCharge -> getDepositByOrderId($orderId);

	// 	//使用易多账户支付
	// 	$userId = UserManager::getLoginUserId();
	// 	require_once (Frm::$ROOT_PATH . 'moneyAccountManager/AccountManagerContainer.php');
	// 	$acmc = AccountManagerContainer::getInstance();
	// 	$m = $acmc -> getAccountManager($payType);

	// 	//如何获取accountId;
	// 	$accountId = $m -> getAccountId($userId);
	// 	$ret = $m -> moneyOperate('freeze', $accountId, &$transaction_number, $payDepositMoney);

	// 	$argv['ext_id'] = $transaction_number;
	// 	$argv['payment_type'] = OrderCharge::$PAYMENT_TYPE_CASH;
	// 	$this -> payforOrderCallback($argv);
	// 	return $ret;
	// }

	public function ringCar($argv) {
		$userId = UserManager::getLoginUserId();

		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$om = new OrderManager();
		$order = $om -> getUserAvailableOrder($userId);
		if (null == $order) {
			throw new Exception('', 4005);
		}

		if (Order::$STATUS_ORDERED == $order -> order_stat) {
			if ($order -> order_start_time > time()) {
				throw new Exception('', 4219);
			}
		}

		require_once (Frm::$ROOT_PATH . 'resource/classes/CarDeviceManager.php');
		$cdm = new CarDeviceManager();
		$d = $cdm -> getDeviceByCar($order->car_id);

		require_once (Frm::$ROOT_PATH . 'ecm/client/CarAgent.php');
		$a = new CarAgent();
		$a -> ring($d -> id);
	}

	public function unPay($argv) {
		$orderId = intval($argv['order_id']);
		//判断订单是否已经支付
		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$om = new OrderManager();
		$order = $om -> getOrder($orderId);

		//计算需要支付的金额
		require_once (Frm::$ROOT_PATH . 'order_charge/classes/OrderCharge.php');
		$orderCharge = new OrderCharge();
		//获取该订单需要支付的押金
		$payDepositMoney = $orderCharge -> getDepositByOrderId($orderId);

		//2013-06-03,golf 注释,修改手机银联接口
		// require_once (Frm::$ROOT_PATH . 'PayAPI/UPPop/classes/UnionPayMobileManager.php');
		// $unm = new UnionPayMobileManager();
		// $unm -> pay($orderId, date('YmdHis', $order -> add_time), $payDepositMoney);
		//end

		//add by golf 2013-06-03
		require_once (Frm::$ROOT_PATH . 'PayAPI/upmp/classes/UnionMobilePayManager.php');
		$ump=new UnionMobilePayManager();
		return $ump->orderPay($orderId,$payDepositMoney);
		//end;

	}

	/**
	 * 取得实名认证信息
	 */
	public function getIDInfo() {
		$userId = UserManager::getLoginUserId();

		require_once (Frm::$ROOT_PATH . 'user/classes/IdentificationManager.php');
		$im = new IdentificationManager();
		$info = $im -> getIDInfo($userId);

		if (!empty($info)) {
			$um = new UserManager();
			$user = $um -> getUser($userId);
			$info['true_name'] = $user['true_name'];
		}

		return $info;
	}

	public function payforOrder($argv) {
		$orderId = intval($argv['order_id']);
		$superPassword = $argv['super_password'];
		//判断订单是否支付
		$userId = UserManager::getLoginUserId();

		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$om = new OrderManager();
		$order = $om -> getOrder($orderId);

		

		//计算需要支付的金额
		require_once (Frm::$ROOT_PATH . 'order_charge/classes/OrderCharge.php');
		$orderCharge = new OrderCharge();
		//获取该订单需要支付的押金
		$payDepositMoney = $orderCharge -> getDepositWithoutPayCount($order -> order_no);

		switch ($argv['t']) {
			case 'e' :
				//判断支付密码是否正确
				require_once (Frm::$ROOT_PATH . 'user/classes/UserManager.php');
				$um = new UserManager();
				if (!$um -> checkSuperPassword($userId, $superPassword)) {
					throw new Exception('', 4215);
				}

				$payType = 'eduo';
				//使用易多账户支付

				require_once (Frm::$ROOT_PATH . 'moneyAccountManager/AccountManagerContainer.php');
				$acmc = AccountManagerContainer::getInstance();
				$m = $acmc -> getAccountManager($payType);
				//如何获取accountId;
				$accountId = $m -> getAccountId($userId);

				$ret = $m -> moneyOperate('freeze', $accountId, &$transaction_number, $payDepositMoney);
				if ($ret['error_code'] == 0) {
					//插入已支付的charge记录到balance表和charge2balance表
					// $chargeIdArr['idArr'] = $orderCharge -> getChargeIdsByOrderId($orderId);
					try {
						$orderCharge -> orderPayment($orderId, $payDepositMoney, $transaction_number, OrderCharge::$PAYMENT_TYPE_CASH);
					} catch(Exception $e) {
						$m -> moneyOperate('freezeComplete', $accountId, &$transaction_number, 0);

						throw $e;
					}

					$ret['orderStartTime'] = date('Y-m-d H:i', $order -> order_start_time);
					$ret['orderEndTime'] = date('Y-m-d H:i', $order -> order_end_time);

					//判断是否后付费用户组,是的话记录该订单id,是否公用
					if ($m -> isAfterPayUser($userId)) {
						$isPublic = $argv['public'];
						switch ($isPublic) {
							case '0' :
								$isPublic = 0;
								break;
							default :
								$isPublic = 1;

								$moneyProvenance = $_POST['money_provenance'];
								$reason = $_POST['reason'];
								break;
						}

						UserManager::businessConsumeLog($orderId, $isPublic, $moneyProvenance, $reason);
					}

					return $ret;
				} else {
					throw new Exception("支付失败", 1);
				}

				break;
			case 'u' :
				$um = new UserManager();
				$userInfo=$um->getUser($userId);
				$phone=$userInfo['phone'];
				//midify by golf 去除权限
				// if (!in_array($phone, array('13910398687','18618213420','18612254429'))) {
				// 	throw new Exception("", 6000);
				// }
				//end
				require_once (Frm::$ROOT_PATH . 'PayAPI/UPPop/classes/UnionWebPayManager.php');
				$unm = new UnionWebPayManager();
				$unm -> pay($order -> order_no . '-' . $orderId, date('YmdHis', $order -> add_time), $payDepositMoney, 'preAuth');
				break;
		}
	}

	function getMyOrderList($argv) {
		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$om = new OrderManager();
		$userId = UserManager::getLoginUserId();
		$orders = $om -> getMyOrderList($userId);
		return $orders;
	}

	function getModifyOrderInfo($argv) {
		$orderId = $argv['order_id'];
		if (!is_numeric($orderId)) {
			throw new Exception('', 4000);
		}
		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$om = new OrderManager();
		$order = $om -> getOrder($orderId);

		$carOrderInfo = $this -> getCarInfoAndOrderTime(array('car_id' => $order -> car_id));
		$carOrderInfo['old_order_id'] = $order -> order_id;
		return $carOrderInfo;
	}

	public function getCancelOrderCommission($argv) {
		$orderId = $argv['order_id'];
		if (!is_numeric($orderId)) {
			throw new Exception('', 4000);
		}
		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$om = new OrderManager();
		$order = $om -> getOrder($orderId);

		if ($order->order_stat==Order::$STATUS_NOT_PAY) {
			$cancelFee=0;
		}
		else
		{
			require_once (Frm::$ROOT_PATH . 'order_charge/classes/DepositComputer.php');
			$depositComputer = new DepositComputer();
			$cancelFee = $depositComputer -> getCancel($order);
		}
		

		return $cancelFee;
	}

	public function cancel($argv) {
		$orderId = $argv['order_id'];
		if (!is_numeric($orderId)) {
			throw new Exception('', 4000);
		}

		$superPassword = $argv['super_password'];

		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$om = new OrderManager();
		$order = $om -> getOrder($orderId);

		$om -> cancel($order, $superPassword);

		return "您的订单已经取消，押金将在次日释放。";
	}
	//add by yangbei 2013-04-25 添加天物订单取消方法
	public function twCancel($argv) {
		$orderId = $argv['order_id'];
		if (!is_numeric($orderId)) {
			throw new Exception('', 4000);
		}

		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$om = new OrderManager();
		$order = $om -> getOrder($orderId);

		$om -> twCancel($order);

		return "您的订单已经取消，押金将在次日释放。";
	}
	//add by yangbei 2013-04-25 添加天物订单取消方法
	public function getBill($argv) {
		$orderNo = $argv['order_no'];
		if (empty($orderNo)) {
			throw new Exception('', 4000);
		}

		require_once (Frm::$ROOT_PATH . 'order_charge/classes/OrderCharge.php');
		$oc = new OrderCharge();
		$ret = $oc -> getBill($orderNo);

		$feeCount = 0;
		foreach ($ret as $value) {
			$feeCount += $value['amount'];
		}

		require_once (Frm::$ROOT_PATH . 'marketing/evaluation/classes/OrderEvaluationManager.php');
		$oem = new OrderEvaluationManager();
		$evaluation = $oem -> getEvaluation($orderNo);

		return array('feeCount' => $feeCount, 'bill' => $ret, 'evaluation' => $evaluation);
	}

	//空函数;
	function emptyCall($argv) {

	}

	function getViolateInfo($argv) {
		$userId = UserManager::getLoginUserId();
		$um = new UserManager();
		return $um -> getViolateInfo($userId);
	}

	public function bindLocal($argv) {
		$phone = $argv['phone'];
		$captcha = $argv['captcha'];

		$um = new UserManager();
		$um -> bindLocal($phone, $captcha);
	}

	public function getShowOrderContent($argv) {
		$orderId = $argv['orID'];

		require_once (Frm::$ROOT_PATH . 'marketing/show_order/classes/ShowOrderManager.php');
		$som = new ShowOrderManager();
		return $som -> getShowOrderContent($orderId);
	}


	/**
	 * $Author: 邬国轩
	 * $time:	2013-04-25 23:35:46Z
	 * $modify:	天物系统,订单提交
	 */
	/****/
	public function twPayforOrder($argv)
	{
		//创建订单
		$carId = $argv['car_id'];
		$startTime = strtotime(trim($argv['start_time']));
		$endTime = strtotime(trim($argv['end_time']));
		$userId = UserManager::getLoginUserId();

		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$om = new OrderManager();
		$um = new UserManager();
		//检查是否天务集团群组的会员,否则不给订车
		if (!$um->isTwUserGroup($userId)) {
			throw new Exception("sorry,非天物集团用户,无法在此网站订车", 1);	
		}

		//检查是否是天物集团的车辆,否则不能用此方法订车
		require_once(Frm::$ROOT_PATH."resource/classes/CarManager.php");
		$cm = new CarManager();

		if (!$cm->isCarInTwGroup($carId)) {
			throw new Exception("您无权限预定此车辆,因为此车辆不是天物集团所有", 1);
		}

		$order = $om -> create($userId, $carId, $startTime, $endTime, $medalId, $oldOrderId);
		$om->twPayforOrderOrder($order);
		
		//记录用途
		$isPublic=1;
		$moneyProvenance = '无信息';
		$reason = $argv['reason'];
		UserManager::businessConsumeLog($order->order_id, $isPublic, $moneyProvenance, $reason);

		//输出车辆返回信息
		$orderTimeArr = $om -> getOrderTimeInDay($carId, strtotime(date('Y-m-d 00:00'))+1);
			//@formatter:off
		$carUpdateInfo = array('carInfo'=>array('id' => $carId, 'orderTime' => $orderTimeArr));
			//@formatter:on
		return $carUpdateInfo;
	}

	/**
	 * $Author: 邬国轩
	 * $time:	2013-04-25 23:35:46Z
	 * $modify:	天物系统,获取天物集团的车辆
	 */
	/****/
	public function twGetCarsInfo($argv)
	{


		require_once (Frm::$ROOT_PATH . 'resource/classes/CarManager.php');
		$carManager = new CarManager();

		$carIdArr=$carManager->twGetCarsIdByTitle('天物');

		$carArr = $carManager -> getCarList($carIdArr);

		require_once (Frm::$ROOT_PATH . 'order_charge/classes/PriceManager.php');
		$priceManager = new PriceManager();
		$rangArr = $priceManager -> getCarArrPriceRange($carIdArr);

		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$orderManager = new OrderManager();
		$ret = array();
		$now = time();
		foreach ($carArr as $car) {
			$orderTimeArr = $orderManager -> getOrderTimeInDay($car -> id, strtotime(date('Y-m-d 00:00'))+1);
			//@formatter:off
			$row = array('id' => $car -> id, 'status' => $car -> status, 'gearbox' => $car -> gearbox, 'name' => $car -> name, 'number' => $car -> number, 'brand' => $car -> brand, 'model' => $car -> model, 'icon' => $car -> icon, 'station' => $car -> station, 'timePriceMin' => $rangArr[$car -> id]['timePriceMin'], 'timePriceMax' => $rangArr[$car -> id]['timePriceMax'], 'milePrice' => $rangArr[$car -> id]['milePrice'], 'orderTime' => $orderTimeArr);
			//@formatter:on
			$ret[] = $row;
		}
		return $ret;
	}
	public function twGetCarInfo($argv)
	{
		// sleep(20);
		$carId=$argv['car_id'];
		if (!is_numeric($carId)) {
			throw new Exception('', 4000);
		}
		require_once (Frm::$ROOT_PATH . 'resource/classes/CarManager.php');
		$carManager = new CarManager();
		$car = $carManager->getCar($carId);

		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$orderManager = new OrderManager();

		$orderTimeArr = $orderManager -> getOrderTimeInDay($car -> id, strtotime(date('Y-m-d 00:00'))+1);
		//@formatter:off
		$row = array('id' => $car -> id, 'status' => $car -> status, 'gearbox' => $car -> gearbox, 'name' => $car -> name, 'number' => $car -> number, 'brand' => $car -> brand, 'model' => $car -> model, 'icon' => $car -> icon, 'station' => $car -> station, 'timePriceMin' => $rangArr[$car -> id]['timePriceMin'], 'timePriceMax' => $rangArr[$car -> id]['timePriceMax'], 'milePrice' => $rangArr[$car -> id]['milePrice'], 'orderTime' => $orderTimeArr,'now'=>time());

		return array('car'=>$row);
	
	}


	
	public function getMyordersWithPager($argv)
	{

		$userId=intval($argv['user_id']);
		// $page = empty(intval($argv['page'])) ? 0 : intval($argv['page']);
		$pageSize = 5;

		require_once (Frm::$ROOT_PATH . 'order/classes/OrderRecord.php');

		$or = new OrderRecord();
		$ret=$or -> getMyOrderPageList($userId,$page,$pageSize);

		return $ret;
	}

    
    /**
	 * $Author: 马涛
	 * $time:	2013-05-16 17:07:46Z
	 * $modify:	充值系统 
	 */
    /*判断用户是否有权限登录*/
    public function haveRightToLogin()
    {
    	$operatorArr = array('18701434897','15010509719');
		session_start();
        if (in_array($_SESSION['phone'], $operatorArr))
		{
		   	return true;
		}
		else
		{
			return false;
		}
    }

    
    /*获取会员信息*/
    public function showMemberInformation($argv)
	{

		if($this->haveRightToLogin())
		{
            $phone=$argv['user_phone'];
    		$amount=$argv['recharge_amount'];
    		$reason=$argv['recharge_reason'];
	       
			if(!$phone)
    		{
        	    throw new Exception("手机号码不能为空！", 2);
    		}

    		if(!$amount)
    		{
    			throw new Exception("充值金额不能为空！", 3);
    		}
    		if(!$reason)
    		{
    			throw new Exception("充值原因不能为空！", 4);
    		}
	        
    		//require_once (Frm::$ROOT_PATH . 'client/classes/UserManager.php');
    		
    		$um = new UserManager();
    		$isPhone=$um->checkphone_s($phone);
    		$isAmount=$um->isValidMoney($amount);
    		$isValidText=$um->isValidText($reason);
        	if(!$isPhone)
    		{
    			throw new Exception("sorry,手机号码不符合规则！", 555);
    		}
    		if(!$isAmount)
    		{
    			throw new Exception("sorry,充值金额不符合规则！", 6);
    		}
    		if(!$isValidText)
    		{
    			throw new Exception("sorry,充值原因不符合规则不能含有：*,--,',;,%,<,>,",7);
    		}
	
        	/*2.获取充值会员的id并验证是否存在*/
        	$memberId = UserManager::getUserIdByPhone($phone);
        	if(!$memberId)
        	{
        		throw new Exception("sorry,该手机号码的用户尚未注册", 8);
        	}
        	/*3.验证是否通过实名认证*/
			if(!$um->isPassApprove($memberId))
			{
				//throw new Exception("sorry,该用户没有通过实名认证，不能充值！", 9);
			}
			$member=array();
			$member=$um->getUser($memberId);
			$memberInformation=array('user_name'=>$member['true_name'],'user_phone'=>$member['phone'],'recharge_amount'=>$amount,'recharge_reason'=>$reason);
			return $memberInformation;
		}
    }

    public function memberRecharge($argv)
    {
    	$phone=$argv['user_phone'];
    	$amount=$argv['recharge_amount'];
    	$reason=$argv['recharge_reason'];
    	$operatorId = UserManager::getLoginUserId();
    	$memberId = UserManager::getUserIdByPhone($phone);
        
    	require_once (Frm::$ROOT_PATH . 'admin/classes/RechargeManager.php');
    	$rm=new RechargeManager();

    	$rechargeId=$rm->creatRecharge($memberId , $amount , $operatorId , $reason);
    	if($rechargeId>0)
    	{
    		$array=array();
    		$array=$rm->getRechargeRecordById($rechargeId);
    		$rechargeRecord=array('user_name'=>$array['userName'],'user_phone'=>$array['phone'],'recharge_amount'=>$array['amount'],'balance'=>$array['balance']);
    		return $rechargeRecord;
    	}
        else
        {
        	throw new Exception("充值失败！",10);
        }
    
    }

	// add by yangbei 2013-05-29 限行规则读取
	public function readRule(){
		$rule = file(Frm::$ROOT_PATH . 'cp/temp/caches/rule.php');
		$arr = explode(",",$rule[0]);
		$myJson = json_encode($arr);
		// echo($myJson);
		return $myJson;
	}

	public function getOpenLoginInfo($argv)
	{
		$userId=UserManager::getLoginUserId();
		$um=new UserManager();
		return $um->getOpenPlatInfo($userId);
	}
	public function untie($argv)
	{
		$from = $argv['from'];
		$userId=UserManager::getLoginUserId();
		$um=new UserManager();
		$um->untie($userId,$from);
	}

	protected function getHourDiscount()
	{
		require_once (Frm::$ROOT_PATH . 'order_charge/classes/PriceManager.php');
		$um=new UserManager();
		$user=$um->getUser(UserManager::getLoginUserId());
		$groupId=$user['user_group_id'];
		//获取用户组,获取用户组折扣
		if ($groupId>0) {
			$pm=new PriceManager();
			$hourDiscount= $pm->getUserGroupDiscount($groupId);
			$hourDiscount=$hourDiscount['discount'];
		}
		else
		{
			$hourDiscount=100;
		}

		return $hourDiscount/100;
	}
}

?>	
