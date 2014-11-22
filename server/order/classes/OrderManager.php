<?php

class OrderManager {

	static public $MAX_UNDONE_COUNT = 2;
	static public $MAX_MODIFY_TIMES = 3;

	public function __construct() {

	}

	//创建订单流程
	public function create($userId, $carId, $startTime, $endTime, $medalId, $oldOrderId) {
		if (empty($oldOrderId)) {
			$oldOrderId = -1;
		}
		$oldOrder = null;
		if ($oldOrderId > 0) {
			$oldOrder = $this -> getOrder($oldOrderId);
		}
		if (empty($medalId)) {
			$medalId = -1;
		}

		require_once (Frm::$ROOT_PATH . 'system/classes/SysConfig.php');
		$orderConf = SysConfig::getOrderConfig();
		$this -> createCheck($userId, $carId, $startTime, $endTime, $medalId, $oldOrder, $orderConf);

		$now = time();
		$db = Frm::getInstance() -> getDb();
		//订单事务开始
		$trans = $db -> startTransaction();

		$order = new Order();
		$order -> car_id = $carId;
		$order -> user_id = $userId;
		$order -> add_time = $now;
		$order -> order_start_time = $startTime;
		$order -> order_end_time = $endTime;
		$order -> medal_id = $medalId;

		if ($oldOrderId > 0) {
			$this -> modifyOrder($oldOrder, $order);
		} else {
			$order -> order_no = $this -> createOrderNo($now);
		}

		$this -> saveOrder($order);
		$orderId = $trans -> lastId();
		$order -> order_id = $orderId;

		require_once (Frm::$ROOT_PATH . 'order/conf/listener.php');
		foreach ($listener as $val) {
			require_once ($val['classPath']);
			$l = new $val['className'];
			if ($l -> onCreate($order, $oldOrder)) {
				break;
			}
		}

		//添加支付超时的检查
		$interval = $orderConf['order_pay_timeout'] * 60;
		$this -> setTimer($now + $interval, 'onPaymentTimeout', $orderId, 'Order payment timeout');

		$trans -> commit();

		return $order;
	}

	protected function modifyOrder($oldOrder, $order) {
		$stat = null;
		$parentOrder = $oldOrder;
		if (Order::$STATUS_NOT_PAY == $parentOrder -> order_stat) {
			$stat = Order::$MODIFY_STAT_MODIFIED;

			if ($parentOrder -> old_order_id > 0) {
				$o = $this -> getOrder($parentOrder -> old_order_id);
				if (Order::$STATUS_NOT_PAY != $o -> order_stat) {
					$parentOrder = $o;
				}
			}
		} else {
			$stat = Order::$MODIFY_STAT_IN_MODIFY;
		}
		//始终修改这次被修改的订单的修改状态，即使可能新订单的old_order_id是别的订单，
		//因为只有在上次修改未完成的情况下，新订单的old_order_id才会是别的订单(记为A)，而此时，A的状态已经被上次修改
		//改过了，不用再改了。
		$sql = "UPDATE edo_cp_car_order SET modify_stat=" . $stat . " WHERE order_id={$oldOrder->order_id}";
		$db = Frm::getInstance() -> getDb();
		$db -> execSql($sql);

		$order -> order_no = $parentOrder -> order_no;
		$order -> real_start_time = $parentOrder -> real_start_time;
		$order -> start_mileage = $parentOrder -> start_mileage;
		$order -> old_order_id = $parentOrder -> order_id;
		$order -> use_car_password = $parentOrder -> use_car_password;
		$order -> modify_times = $oldOrder -> modify_times + 1;
	}

	/**
	 *
	 * @param Order $order
	 */
	protected function saveOrder($order) {
		$sql = "INSERT INTO edo_cp_car_order SET order_no='{$order->order_no}' , order_stat={$order->order_stat} ,
			is_shift_stat=0 , car_id={$order->car_id} , user_id={$order->user_id} , add_time={$order->add_time} ,
			order_start_time={$order->order_start_time} , order_end_time={$order->order_end_time} ,
			real_start_time={$order->real_start_time} ,
			real_end_time={$order->real_end_time} , start_mileage={$order->start_mileage} , 
			end_mileage={$order->end_mileage} ,
			medal_id={$order->medal_id} , old_order_id={$order->old_order_id} , 
			modify_times={$order->modify_times} , use_car_password='{$order->use_car_password}' , modify_stat={$order->modify_stat}";
		$db = Frm::getInstance() -> getDb();
		$db -> execSql($sql);
	}

	public function onPaymentTimeout($orderId) {
		$order = $this -> getOrder($orderId);
		if (Order::$STATUS_NOT_PAY == $order -> order_stat) {
			//新订单变成修改失败，老订单变回未修改
			$status = Order::$STATUS_PAY_TIMEOUT;
			$modifyStatus = Order::$MODIFY_STAT_NOT_MODIFY;
			if ($order -> old_order_id > 0) {
				$modifyStatus = Order::$MODIFY_STAT_FAILED;
			}

			$db = Frm::getInstance() -> getDb();

			$sql = "UPDATE edo_cp_car_order SET order_stat = $status , modify_stat = $modifyStatus
				WHERE order_id = $orderId AND order_stat = " . Order::$STATUS_NOT_PAY . " 
				AND modify_stat = " . Order::$MODIFY_STAT_NOT_MODIFY;
			$db -> execSql($sql);

			$modifyStatus = Order::$MODIFY_STAT_NOT_MODIFY;
			$sql = "UPDATE edo_cp_car_order SET modify_stat = $modifyStatus
				WHERE order_id = " . $order -> old_order_id . " AND modify_stat = " . Order::$MODIFY_STAT_IN_MODIFY;
			$db -> execSql($sql);

			$this -> notifyOrderPayTimeout($order);
		}
	}

	public function onTimeEnd($orderId) {
		$order = $this -> getOrder($orderId);
		if (Order::$STATUS_STARTED == $order -> order_stat) {
			$this -> setOrderStatus($orderId, Order::$STATUS_REVERT_TIMEOUT);
		} else if (Order::$STATUS_ORDERED == $order -> order_stat) {
			$this -> setOrderStatus($orderId, Order::$STATUS_DOOR_CLOSED);
			$this -> notifyOrderEnd($order);
		}
	}

	public function onCardCloseDoor($cardId) {
		require_once (Frm::$ROOT_PATH . 'user/classes/CardManager.php');
		$cm = new CardManager();
		$user = $cm -> getCardUser($cardId);

		//车机设备的指令没有队列，所以，产生用车完成指令后，还未发送成功时又产生一条刷卡锁门指令，这时，
		//刷卡锁门指令会覆盖调用车完成指令。
		//也就是说，用车完成指令再不会上报到服务器，而只报刷卡锁门指令。
		$orderStat = $this -> getCanEndStatusArr();
		$orderStat[] = Order::$STATUS_COMPLETED;
		$sqlStat = implode(',', $orderStat);
		require_once (Frm::$ROOT_PATH . 'order/classes/Order.php');
		$sql = "SELECT o.order_id , o.order_stat , o.real_end_time , o.user_id , o.car_id
			FROM edo_cp_car_order o WHERE
			o.user_id = {$user['uid']} AND modify_stat = " . Order::$MODIFY_STAT_NOT_MODIFY . " 
			AND order_stat IN ( " . $sqlStat . " ) 
			ORDER BY real_end_time DESC ";
		$db = Frm::getInstance() -> getDb();
		$order = $db -> getRow($sql);
		if (empty($order)) {
			//找不到订单
			throw new Exception('', 4228);
		}

		if (Order::$STATUS_STARTED == $order['order_stat']) {
			$this -> setOrderStatus($order['order_id'], Order::$STATUS_DOOR_CLOSED);
		} else if (Order::$STATUS_COMPLETED == $order['order_stat']) {
			//if ($order['real_end_time'] + 600 >= time()) {
			$this -> setOrderStatus($order['order_id'], Order::$STATUS_DOOR_CLOSED);
			//}
		}
		//modify by yangbei 2013-05-01 修改刷卡锁门订单取消指令
		// $this -> sendOrderToCar($order['user_id'], $order['car_id']);
		$this -> sendOrderToCar('', $order['car_id']);
		//modify by yangbei 2013-05-01 
	}

	/**
	 * 取得现在时刻以后的订单信息。把订单按每天存放好。如果开始时间是今天之前，今天该订单的开始时间就是0；
	 * 如果结束时间是今天之后的，今天该订单的结束时间就是23:59。
	 *
	 * @param  int $argv['car_id'] 车辆id
	 * @param  string $argv['start_time'] 车辆id
	 * @return array
	 */
	public function getOrderTimeInDay($carId, $startTime, $oldOrderId = -1) {
		$orders = $this -> getOrderInfo($carId, $startTime, $oldOrderId);
		return $this -> formatOrderTime($orders);
	}

	public function orderPickCar($userId, $superPassword) {
		require_once (Frm::$ROOT_PATH . 'user/classes/UserManager.php');
		$um = new UserManager();
		if (!$um -> checkSuperPassword($userId, $superPassword)) {
			throw new Exception('', 4215);
		}

		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$om = new OrderManager();
		$order = $om -> getUserAvailableOrder($userId);
		if (Order::$STATUS_ORDERED != $order -> order_stat) {
			throw new Exception('', 4000);
		}

		require_once (Frm::$ROOT_PATH . 'resource/classes/CarDeviceManager.php');
		$dm = new CarDeviceManager();
		$d = $dm -> getDeviceByCar($order -> car_id);

		$cardId = $this -> getUserCardId($um, $userId);

		require_once (Frm::$ROOT_PATH . 'ecm/client/CarAgent.php');
		$carAgent = new CarAgent();
		$carAgent -> creditCardOpenDoor($d -> id, $cardId);
	}
	//add by  yangbei 2013-05-09 增加天物取车函数
	public function twOrderPickCar($userId) {
		require_once (Frm::$ROOT_PATH . 'user/classes/UserManager.php');
		$um = new UserManager();
		
		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$om = new OrderManager();
		$order = $om -> getUserAvailableOrder($userId);
		if (Order::$STATUS_ORDERED != $order -> order_stat) {
			throw new Exception('', 4000);
		}

		
		require_once (Frm::$ROOT_PATH . 'resource/classes/CarDeviceManager.php');
		$dm = new CarDeviceManager();
		$d = $dm -> getDeviceByCar($order -> car_id);

		$cardId = $this -> getUserCardId($um, $userId);

		require_once (Frm::$ROOT_PATH . 'ecm/client/CarAgent.php');
		$carAgent = new CarAgent();
		$carAgent -> creditCardOpenDoor($d -> id, $cardId);
	}
	//add by  yangbei 2013-05-09 
	public function orderCloseDoor($userId) {
		//1.先根据已完成的订单判断，因为如果先判断可用订单，有可能就取到用户的下一个订单了
		$order = $this -> getUserLastCompleteOrder($userId);
		$noOrder = false;
		if (null != $order) {
			//2.先判断时间，不能先判断状态，因为时间到了，不管他有没有关车门，都不能再进行操作。
			require_once (Frm::$ROOT_PATH . 'system/classes/SysConfig.php');
			$min = SysConfig::getValue('close_door_time');
			if (empty($min)) {
				$min = 10;
			}
			$closeDoorTime = $min * 60;

			if ($order -> real_end_time + $closeDoorTime < time()) {
				$noOrder = true;
			}

			if (!$noOrder) {
				$this -> orderCloseDoorOrder($order);

				return $order;
			}
		} else {
			$noOrder = true;
		}

		if ($noOrder) {
			$order = $this -> getUserAvailableOrder($userId);
			if (null == $order) {
				throw new Exception('', 4005);
			}

			return $order;
		}
	}

	protected function orderCloseDoorOrder($order, $force = false) {
		if (Order::$STATUS_DOOR_CLOSED == $order -> order_stat) {
			throw new Exception('', 4223);
		}

		require_once (Frm::$ROOT_PATH . 'resource/classes/CarDeviceManager.php');
		$dm = new CarDeviceManager();
		$device = $dm -> getDeviceByCar($order -> car_id);

		if (!$force) {
			//钥匙是否放收纳箱
			if (!(CarDevice::$CTS_KEY & $device -> cts)) {
				throw new Exception('', 4220);
			}
		}

		//符合条件，关门
		require_once (Frm::$ROOT_PATH . 'user/classes/UserManager.php');
		$cardId = $this -> getUserCardId(new UserManager(), $order -> user_id);

		require_once (Frm::$ROOT_PATH . 'ecm/client/CarAgent.php');
		$carAgent = new CarAgent();
		$carAgent -> creditCardCloseDoor($device -> id, $cardId);
		//modify by 杨贝 2013-04-30 车机订单更新传参修改
		// $this -> sendOrderToCar($order -> user_id, $order -> car_id);
		$this -> sendOrderToCar('', $order -> car_id);
		//modify by 杨贝 2013-04-30 车机订单更新传参修改
	}

	public function onAutoCloseDoor($orderId) {
		$order = $this -> getOrder($orderId);
		$this -> orderCloseDoorOrder($order, true);
	}

	/**
	 * 取得用户当前可用的订单,包括未支付、已预定、已开始、已完成、已超时还车，这些状态，用户都还可以对订单进行操作。
	 */
	public function getUserAvailableOrder($userId) {
		$sql = "SELECT o.order_id , o.order_no , o.order_stat , o.car_id , o.order_start_time, o.order_end_time 
			FROM edo_cp_car_order o WHERE
			o.user_id = $userId AND " . $this -> getAvailableOrderWhereSQL() . " ORDER BY order_start_time ";
		$db = Frm::getInstance() -> getDb();
		$ret = $db -> getAll($sql);
		if (empty($ret)) {
			return null;
		}

		$row = null;
		foreach ($ret as $value) {
			$row = $value;
			//通过getAvailableOrderWhereSQL查出来的，一个订单最多只有两条记录，即已经支付过的和刚刚修改的还未支付的，而未支付的肯定是新提交的订单。
			if (Order::$STATUS_NOT_PAY == $value['order_stat']) {
				continue;
			} else {
				break;
			}
		}

		return $this -> generateOrder($row);
	}

	public function getUserLastCompleteOrder($userId) {
		require_once (Frm::$ROOT_PATH . 'order/classes/Order.php');
		$sql = "SELECT o.order_id , o.order_no , o.order_stat , o.car_id , o.order_start_time , o.real_end_time , o.user_id
			FROM edo_cp_car_order o WHERE
			o.user_id = $userId AND modify_stat = " . Order::$MODIFY_STAT_NOT_MODIFY . " 
			AND ( order_stat = " . Order::$STATUS_COMPLETED . " ) 
			ORDER BY real_end_time DESC ";
		$db = Frm::getInstance() -> getDb();
		$row = $db -> getRow($sql);
		if (empty($row)) {
			return null;
		}

		return $this -> generateOrder($row);
	}

	/**
	 * 因为一个订单号只对应一个未被修改的订单
	 */
	public function getOrderByOrderNo($orderNo) {
		$sql = "SELECT * FROM edo_cp_car_order WHERE order_no = '$orderNo' AND modify_stat = " . Order::$MODIFY_STAT_NOT_MODIFY;
		$db = Frm::getInstance() -> getDb();
		$row = $db -> getRow($sql);
		return $this -> generateOrder($row);
	}
	//add by yangbei 2013-04-24 添加取出指定时间段订单
	//通过车辆ID来获取某段时间的订单
	public function twGetOrderByCarId($user_id,$car_id,$startTime,$endTime,$orderStat){
		$sql = "SELECT * FROM  edo_cp_car_order WHERE order_stat=$orderStat AND user_id<> ".$user_id." AND car_id =" . $car_id . " AND order_end_time BETWEEN $startTime AND $endTime ORDER BY order_end_time desc";
		$db = Frm::getInstance() -> getDb();
		$row = $db -> getRow($sql);
		return $this -> generateOrder($row);
	}
	//add by yangbei 2013-04-24

	//add by yangbei 2013-4-24 添加定时器任务
	public function twSendConnectMsg($orderID){
		//通过order_id 重新获取订单状态，因为状态随时可变
		$order = $this -> getOrder($orderID);
		$oldUserId = $order -> user_id;
		$endTime = $order -> order_end_time + 7200000;
		$startTime = $order -> order_end_time;
		$orderStat = 1;
		$oOrderStat = $order -> order_stat;
		$orders = $this -> twGetOrderByCarId($order -> user_id,$order -> car_id,$startTime,$endTime,$orderStat);
		if ( !empty($orders)&&($oOrderStat==2||$oOrderStat==6)) {
			$newUserId = $orders -> user_id;

			require_once (Frm::$ROOT_PATH . 'user/classes/UserManager.php');
			$um = new UserManager();
			//获取老订单用户信息
			$oUserInfo = $um -> getUser($oldUserId);
			$oName = $oUserInfo['true_name'];
			$oPhone = $oUserInfo['phone'];
			$oEndTime = date('Y-m-d H:i', $order -> order_end_time);


			//获取新订单用户信息
			$nUserInfo = $um -> getUser($newUserId);
			$nName = $nUserInfo['true_name'];
			$nPhone = $nUserInfo['phone'];
			$nStarTime = date('Y-m-d H:i', $orders -> order_start_time);
			//老订单用户发送短息内容
			$oMsgContent = "尊敬的{$oName}用户，您的用车时间至 {$oEndTime}，{$nStarTime}有用户（{$nName}{$nPhone}）订车，请您按时还车，以免影响后面用户用车，感谢您的支持！";
  			
  			//新订单用户发送短息内容
  			
  			$nMsgContent = "尊敬的{$nName}用户，您的取车时间是 {$nStarTime}，上个用户是（{$oName}{$oPhone}），如果找不到车辆，请与其联系，感谢您的支持！";
  		
    		require_once (Frm::$ROOT_PATH . 'system/sms_platform/classes/SmsSender.php');
        	$msgSender = new SmsSender();
    		$msgSender -> send($oMsgContent, $oPhone);
    		$msgSender -> send($nMsgContent, $nPhone);
		}
	}
	//add by yangbei 2013-4-24 

	//获取order对象
	public function getOrder($orderId) {
		require_once (Frm::$ROOT_PATH . 'order/classes/Order.php');

		$sql = "SELECT * FROM edo_cp_car_order WHERE order_id = $orderId";
		$db = Frm::getInstance() -> getDb();
		$row = $db -> getRow($sql);
		if (empty($row)) {
			throw new Exception("", 4228);
		}

		return $this -> generateOrder($row);
	}

	/**
	 * 取得指定订单之后该用户的所有订单,包含该订单
	 */
	public function getUserOrderAfter($userId, $orderId) {
		try {
			$order = $this -> getOrder($orderId);
		} catch(Exception $e) {
			return array();
		}

		$sql = "SELECT * FROM edo_cp_car_order WHERE add_time > {$order->add_time} AND user_id = $userId
			OR order_id = $orderId";
		$db = Frm::getInstance() -> getDb();
		$ret = $db -> getAll($sql);

		return $ret;
	}

	public function onBeginUseCar($time, $hmile, $lmile, $orderId) {
		if (!is_numeric($hmile) || !is_numeric($lmile) || !is_numeric($orderId)) {
			throw new Exception('', 4000);
		}
		$now = time();
		//很可能传上来的时间不正确，但不会传上来一个未发生的未来时间，因为人为的操作动作再怎么说是个过去式。
		if ($time > $now) {
			$time = $now;
		}

		require_once (Frm::$ROOT_PATH . 'order/classes/Order.php');
		$sql = "UPDATE edo_cp_car_order SET real_start_time = $time , start_mileage = $lmile , order_stat = " . Order::$STATUS_STARTED . " WHERE order_id = $orderId AND order_stat = " . Order::$STATUS_ORDERED;
		$db = Frm::getInstance() -> getDb();
		$db -> execSql($sql);
	}

	public function getCanEndStatusArr() {
		require_once (Frm::$ROOT_PATH . 'order/classes/Order.php');
		return array(Order::$STATUS_ORDERED, Order::$STATUS_STARTED, Order::$STATUS_REVERT_TIMEOUT);
	}

	public function onEndUseCar($time, $hmile, $lmile, $orderId) {
		if (!is_numeric($hmile) || !is_numeric($lmile) || !is_numeric($orderId)) {
			throw new Exception('', 4000);
		}
		$now = time();
		//很可能传上来的时间不正确，但不会传上来一个未发生的未来时间，因为人为的操作动作再怎么说是个过去式。
		if ($time > $now) {
			$time = $now;
		}

		$order = $this -> getOrder($orderId);

		//由于车机的订单开始执行后，到执行完成（锁门），中途不管怎么修改，都不会改变该订单的ID，
		//所以要根据订单ID查未被修改的订单。
		$order = $this -> getOrderByOrderNo($order -> order_no);

		$order -> end_mileage = $lmile;

		require_once (Frm::$ROOT_PATH . 'resource/classes/CarManager.php');
		$cm = new CarManager();
		$car = $cm -> getCar($order -> car_id);

		if ($car -> mileFactor <= 0) {
			$order -> mile = 0;
		} else {
			$order -> mile = ($order -> end_mileage - $order -> start_mileage) / $car -> mileFactor;
		}

		require_once (Frm::$ROOT_PATH . 'order/classes/Order.php');
		$sqlStat = implode(',', $this -> getCanEndStatusArr());
		$sql = "UPDATE edo_cp_car_order SET real_end_time = $time , end_mileage = $lmile , mile = {$order->mile} ,
			order_stat = 3 WHERE order_id = {$order->order_id} AND order_stat IN ( " . $sqlStat . " ) ";
		$db = Frm::getInstance() -> getDb();
		$db -> execSql($sql);

		$this -> notifyOrderEnd($order);

		$this -> setTimer($time + 600, 'onAutoCloseDoor', $orderId, 'close the door after 10 min.');
	}

	protected function setTimer($time, $fnName, $orderId, $desc) {
		require_once (Frm::$ROOT_PATH . 'system/classes/Timer.php');
		$timer = new Timer();
		$timer -> addTask($time, 'OrderManager', $fnName, 'order/classes/OrderManager.php', $orderId, $desc);
	}

	/**
	 * 订单结束，对listener进行通知
	 */
	protected function notifyOrderEnd($order) {
		require_once (Frm::$ROOT_PATH . 'order/conf/listener.php');
		foreach ($listener as $key => $value) {
			include ($value['classPath']);
			$obj = new $value['className'];
			$obj -> onEnd($order);
		}
	}

	protected function notifyOrderPayTimeout($order) {
		require_once (Frm::$ROOT_PATH . 'order/conf/listener.php');
		foreach ($listener as $key => $value) {
			include ($value['classPath']);
			$obj = new $value['className'];
			$obj -> onPayTimeout($order);
		}
	}

	/**
	 * 与payforOrder参数不同，本函数接受的参数为Order对象
	 */
	public function payforOrderOrder($order) {
		require_once (Frm::$ROOT_PATH . 'user/classes/UserManager.php');
		$um = new UserManager();
		if (!$um -> hasSetSuperPassword($order -> user_id)) {
			throw new Exception('', 4227);
		}

		$db = Frm::getInstance() -> getDb();
		//防止重复支付等错误，所以，要锁数据库。
		$trans = $db -> startTransaction();

		$tmpOrder = $this -> getOrder($order -> order_id);
		if (Order::$STATUS_NOT_PAY != $tmpOrder -> order_stat) {
			throw new Exception('', 4231);
		}

		$stat = Order::$STATUS_ORDERED;
		//如果有old_order_id>0,把old_order的修改状态改为修改完成
		if ($order -> old_order_id > 0) {
			$db = Frm::getInstance() -> getDb();
			$sql = " UPDATE edo_cp_car_order SET modify_stat=" . Order::$MODIFY_STAT_MODIFIED . "
				WHERE order_id=" . $order -> old_order_id . " AND modify_stat <> " . Order::$MODIFY_STAT_MODIFIED;
			$db -> execSql($sql);

			//修改时，新的订单记录为未支付状态。
			//支付时，新记录要与原订单状态一致。但有可能原订单未支付
			$oldOrder = $this -> getOrder($order -> old_order_id);
			if (Order::$STATUS_NOT_PAY != $oldOrder -> order_stat) {
				$stat = $oldOrder -> order_stat;
			}
		}
		$this -> setOrderStatus($order -> order_id, $stat);

		$trans -> commit();

		if ($order -> old_order_id <= 0) {
			//如果是新订单，下发成功短信
			$isSendPassword = false;
		}
		else
		{
			if ($oldOrder->order_stat == Order::$STATUS_NOT_PAY) {
				$isSendPassword = false;
			}
			else
			{
				$isSendPassword = true;
			}

		}
		if (!$isSendPassword) {
			require_once (Frm::$ROOT_PATH . 'user/classes/UserManager.php');
			$um = new UserManager();
			$userInfo = $um -> getUser($order -> user_id);
			$this -> sendOrderPassword($order -> order_id, $userInfo['phone'], $order -> car_id, $order -> order_start_time, $order -> order_end_time);
		}

		//$this -> twSendConnectMsg($order -> order_id);
		$this -> sendOrderToCar($order -> user_id, $order -> car_id);

		$this -> setTimer($order -> order_end_time, 'onTimeEnd', $order -> order_id, 'Order time end');
		
		
	}

	/**
	 * 对订单进行结算
	 */
	public function settle($orderNo) {
		$db = Frm::getInstance() -> getDb();
		$trans = $db -> startTransaction();

		require_once (Frm::$ROOT_PATH . 'order/classes/Order.php');
		$sql = "SELECT order_stat FROM edo_cp_car_order WHERE
			order_no = '$orderNo' AND modify_stat = " . Order::$MODIFY_STAT_NOT_MODIFY;
		$orderStat = $db -> getOne($sql);
		if (Order::$STATUS_COMPLETED != $orderStat && Order::$STATUS_DOOR_CLOSED != $orderStat&& Order::$STATUS_CANCELED != $orderStat) {
			throw new Exception('', 4232);
		}

		$sql = "UPDATE edo_cp_car_order SET order_stat = " . Order::$STATUS_SETTLED . "
			WHERE order_no = '$orderNo' AND modify_stat = " . Order::$MODIFY_STAT_NOT_MODIFY;
		$db -> execSql($sql);

		$trans -> commit();
	}

	public function getWithoutSettledOrderList($userId = -1,$start=0,$limit=50) {
		$sqlUser = "";
		if ($userId > 0) {
			$sqlUser = " AND user_id = $userId ";
		}
		require_once (Frm::$ROOT_PATH . 'order/classes/Order.php');
		//取消的订单也应该包含在里，因为现在取消是不立即结算的
		//@formatter:off
		$statArr = array(Order::$STATUS_NOT_PAY, Order::$STATUS_ORDERED, Order::$STATUS_STARTED, Order::$STATUS_COMPLETED, Order::$STATUS_REVERT_TIMEOUT, Order::$STATUS_DOOR_CLOSED, Order::$STATUS_CANCELED);
		//@formatter:on
		$sql = "SELECT * FROM edo_cp_car_order o 
				LEFT JOIN ( SELECT order_no,COUNT(*) as count FROM order_charge WHERE 1 $sqlUser AND status=1 GROUP BY order_no ) oc ON oc.order_no=o.order_no 
				WHERE oc.count>0 AND o.order_stat IN( " . implode(',', $statArr) . " )
			 $sqlUser AND o.modify_stat 
			IN ( " . Order::$MODIFY_STAT_NOT_MODIFY . " , " . Order::$MODIFY_STAT_IN_MODIFY . " ) ";

		$db = Frm::getInstance() -> getDb();
		$ret = $db -> limitSelect($sql,$start,$limit);

		$sql = "SELECT COUNT(*) FROM edo_cp_car_order o 
				LEFT JOIN ( SELECT order_no,COUNT(*) as count FROM order_charge WHERE 1 $sqlUser AND status=1 GROUP BY order_no ) oc ON oc.order_no=o.order_no 
				WHERE oc.count>0 AND o.order_stat IN( " . implode(',', $statArr) . " )
			 $sqlUser AND o.modify_stat 
			IN ( " . Order::$MODIFY_STAT_NOT_MODIFY . " , " . Order::$MODIFY_STAT_IN_MODIFY . " ) ";
		$count=$db->getOne($sql);
		$ret['count']=$count;
		return $ret;
	}

	protected function sendOrderPassword($orderId, $phone, $carId, $orderStartTime, $orderEndTime) {
		$db = Frm::getInstance() -> getDb();

		require_once (Frm::$ROOT_PATH . 'resource/classes/CarManager.php');
		$cm = new CarManager();
		$car = $cm -> getCar($carId);
		$carName = $car -> name;
		$station = $car -> station;
		$carNum  = $car -> number;
		$rand = mt_rand(100000, 999999);

		$sStartTime = date('Y-m-d H:i', $orderStartTime);
		$sEndTime = date('Y-m-d H:i', $orderEndTime);
		$msgContent = "您已成功预定 {$sStartTime} 到 {$sEndTime}，在“{$station}”的车辆(车牌:{$carNum})： {$carName}，用车密码是：{$rand}，祝您旅途愉快。";

		require_once (Frm::$ROOT_PATH . 'system/sms_platform/classes/SmsSender.php');
		$msgSender = new SmsSender();
		$msgSender -> send($msgContent, $phone);

		$sql = "UPDATE edo_cp_car_order SET use_car_password = '{$rand}' WHERE order_id = $orderId";
		$db -> execSql($sql);

		return $rand;
	}

	protected function sendOrderToCar($userId, $carId) {
		$db = Frm::getInstance() -> getDb();
		//delete by yangbei 2013-04-26 删除根据userId获取用户会员卡号
		// @formatter:off
		// $sql = "select c.card_number from edo_cp_vip_card_issue i
		// 	left join edo_cp_vip_card  c on i.vip_card_id=c.id
		// 	where i.user_id=" . $userId . " order by c.id desc ";
		// //@formatter:on
		// $card = $db -> getRow($sql);

		// $cardNum = $card['card_number'];
		// if (empty($cardNum)) {
		// 	$cardNum = $userId;
		// }
		//delete by yangbei 2013-04-26 

		//modify by yangbei 2013-04-22 修改订单下发车机sql
		//@formatter:off
		/*$sql = "SELECT order_id , order_start_time , order_end_time , use_car_password , car_id FROM edo_cp_car_order
			WHERE ( order_stat = 1 OR order_stat = 2 OR order_stat = 6 ) AND user_id = $userId AND car_id = $carId
			AND order_end_time > " . time() . " AND modify_stat = " . Order::$MODIFY_STAT_NOT_MODIFY . " 
			ORDER BY order_end_time ";*/
		// $sql = "SELECT order_id , order_start_time , order_end_time , use_car_password , car_id FROM edo_cp_car_order
		// 	WHERE ( order_stat = 1 OR order_stat = 2 OR order_stat = 6 )AND car_id = $carId
		// 	AND order_end_time > " . time() . " AND modify_stat = " . Order::$MODIFY_STAT_NOT_MODIFY . " 
		// 	ORDER BY order_end_time ";	
		//@formatter:on
		//modify by yangbei 2013-04-22 修改订单下发车机sql
		//modify by yangbei 2013-04-26 修改订单下发车机指令，增加用户会员卡号
		//add by yangbei 2013-04-30 增加user_id字段
		$sql = "SELECT order_id, order_start_time, order_end_time, use_car_password, car_id, v.card_number,o.user_id
				FROM edo_cp_car_order o
				LEFT JOIN edo_cp_vip_card_issue b ON b.user_id = o.user_id
				LEFT JOIN edo_cp_vip_card v ON b.vip_card_id = v.id
				WHERE (	o.order_stat =1	OR o.order_stat =2 OR o.order_stat =6)
				AND car_id =$carId
				AND order_end_time >" . time() . " 
				AND modify_stat = " . Order::$MODIFY_STAT_NOT_MODIFY . "
				ORDER BY o.order_end_time";
		//modify by yangbei 2013-04-26 修改订单下发车机指令，增加用户会员卡号		
		$ret = $db -> limitSelect($sql, 0, 5);

		require_once (Frm::$ROOT_PATH . 'resource/classes/CarDeviceManager.php');
		$dm = new CarDeviceManager();
		$device = $dm -> getDeviceByCar($carId);
		if (null == $device) {
			throw new Exception('', 4237);
		}

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
			// add by yangbei 2013-04-26 增加会员卡号
			$cardNum = $o['card_number'];
			// add by yangbei 2013-04-26 增加会员卡号
			if (empty($cardNum)) {
				$cardNum = $o['user_id'];
			}
			$orderArr[] = array('odn' => (int)$orderId, 'cn' => (int)$cardNum, 'pwd' => "{$useCarPassword}", 'bt' => $xst, 'et' => $xet);
		}

		require_once (Frm::$ROOT_PATH . 'ecm/client/CarAgent.php');
		$agent = new CarAgent();
		$agent -> updateOrder(array('car_id' => $device -> id, 'order' => $orderArr));
	}

	/**
	 * 获取某车辆的订单信息
	 * @param  int $argv['car_id'] 车辆id
	 * @param  string $argv['start_time'] 车辆id
	 * @return array       [description]
	 */
	protected function getOrderInfo($carId, $startTime, $oldOrderId = -1) {
		//结束时间大于现在的，都是未完成的订单，对下个订单有影响。
		//但订单如果是超时还车，也要选出来
		require_once (Frm::$ROOT_PATH . 'order/classes/Order.php');
		$sql = "SELECT o.order_id,o.car_id,o.order_start_time AS start_time,o.order_end_time AS end_time
			FROM edo_cp_car_order o WHERE o.car_id=" . $carId . " 
			AND o.order_id <> $oldOrderId 
			AND ( o.modify_stat = " . Order::$MODIFY_STAT_NOT_MODIFY . " 
				OR o.modify_stat = " . Order::$MODIFY_STAT_IN_MODIFY . " 
			)
			AND ( ( order_end_time>" . $startTime . " 
					AND " . $this -> getAvailableOrderWhereSQL() . " )
					OR ( order_stat = " . Order::$STATUS_REVERT_TIMEOUT . " AND order_end_time < " . $startTime . " 
				) 
			)
			ORDER BY o.order_start_time";
		$db = Frm::getInstance() -> getDb();
		$orders = $db -> getAll($sql);
		return $orders;
	}

	/**
	 * 格式化订单时间结构,提供前端js使用
	 *
	 * @param  array $orders (order_id,car_id,start_time,end_time)
	 * @return array         [description]
	 */
	protected function formatOrderTime($orders) {
		$dayArr = array();
		$now = time();
		foreach ($orders as $k => $v) {
			$order = array('start_time' => '', 'end_time' => '');

			//开始时间
			$st = $v['start_time'];
			$sd = (int)(($st - strtotime(date('Y-m-d 00:00:00'))) / 86400);
			//计算开始时间距今天的天数
			if (($st - strtotime(date('Y-m-d 00:00:00'))) / 86400 < 0) {//订单已经于今天之前开始了
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
					// $dayArr[$i][] = array('start_time' => $order['start_time'], 'end_time' => '23:59');

					if (empty($dayArr[$i - 1])) {
						$dayArr[$i][] = array('start_time' => $order['start_time'], 'end_time' => '23:59');
					} else {
						$dayArr[$i][] = array('start_time' => '00:00', 'end_time' => '23:59');
					}

					//订单完全跨越的那些天
					// if ($sd==count($dayArr)) {
					// 	$dayArr[$i][] = array('start_time' => $order['start_time'] , 'end_time' => '23:59');
					// }
					// else
					// {
					// 	$dayArr[$i][] = array('start_time' => '00:00', 'end_time' => '23:59');
					// }

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

	protected function setOrderStatus($orderId, $status) {
		$sql = "UPDATE edo_cp_car_order SET order_stat = $status WHERE order_id = $orderId ";
		$db = Frm::getInstance() -> getDb();
		$db -> execSql($sql);
	}

	protected function createOrderNo($now) {
		$path = Frm::$ROOT_PATH . 'temp/order_no.locker';
		$fp = fopen($path, 'r+');
		$sNow = date('YmdHis', $now);
		if (flock($fp, LOCK_EX)) {
			fseek($fp, 0, SEEK_SET);
			$t = fread($fp, 15);
			if ($t == $sNow) {
				//整数的长度11位
				$count = unpack('L', fread($handle, 4));
				fseek($fp, 15, SEEK_SET);
				fwrite($fp, pack('L', $count + 1));
			} else {
				$pos = fseek($fp, 0, SEEK_SET);
				fwrite($fp, $sNow . pack('L', 1));
			}

			flock($fp, LOCK_UN);
		}
		fclose($fp);

		return $sNow . ($count + 1);
	}

	/**
	 * 创建订单是的条件检查
	 */
	public function createCheck($userId, $carId, $startTime, $endTime, $medalId, $oldOrder, $orderConf) {
		$now = time();
		//是否设置超级密码
		$um = new UserManager();
		if (!$um->hasSetSuperPassword($userId)) {
			throw new Exception("", 4227);
		}
		//对输入时间的检查
		if ($startTime + 5 * 60 < $now) {//防止老报“开始时间不能早于当前时间”
			if (null == $oldOrder) {//有老订单，放后面老订单的判断里判断
				throw new Exception("", 4017);
			}
		}
		// add by yangbei 2013-06-21 增加修改开始订单时间判断
		if ($oldOrder!=null) {
        //订单已经开始
            if ($oldOrder->order_start_time<$now) {
                if ($startTime!=$oldOrder->order_start_time) {
                    throw new Exception("", 4013);

                }

            }else if ($oldOrder->order_start_time>$now) {
                //订单未开始
                if ($startTime + 5 * 60 < $now) {
                    throw new Exception("", 4017);
                }
            }
        }

		// add by yangbei 2013-06-21 end

		if ($startTime >= $endTime) {
			throw new Exception("", 4200);
		}

		//检查车辆是否安装了车机设备 add by wuguoxuan at 2013-04-13
		require_once (Frm::$ROOT_PATH . 'resource/classes/CarDeviceManager.php');
		$dm = new CarDeviceManager();
		$device = $dm -> getDeviceByCar($carId);
		if (null == $device) {
			throw new Exception('', 4237);
		}
		
		//老订单的判断
		$this -> oldOrderCheck($userId, $carId, $startTime, $endTime, $medalId, $oldOrder, $orderConf, $now);

		//不能订7天之后的车
		if ($now + $orderConf['order_max_start_time'] * 60 < $startTime) {
			throw new Exception('', 4212);
		}
		//订单总时长不能超过3天
		if ($endTime - $startTime > 60 * $orderConf['order_max_time']) {
			throw new Exception('', 4009);
		}

		//是否欠费
		require_once (Frm::$ROOT_PATH . 'moneyAccountManager/CashAccountManager.php');
		$cam = new CashAccountManager();
		if ($cam -> hasArrearage($userId)) {
			throw new Exception('', 4050);
		}

		//未通过实名认证
		require_once (Frm::$ROOT_PATH . 'user/classes/IdentificationManager.php');
		$im = new IdentificationManager();
		if (!$im -> hasUploadApproveData($userId)) {
			throw new Exception('', 4226);
		} else {
			if (!$im -> isPassApprove($userId)) {
				throw new Exception('', 4225);
			}
		}

		//对车辆状态的检查
		require_once (Frm::$ROOT_PATH . 'resource/classes/CarManager.php');
		$cm = new CarManager();
		$car = $cm -> getCar($carId);
		if (Car::$STATUS_ENABLE != $car -> status) {
			require_once (Frm::$ROOT_PATH . 'resource/classes/CarGroupManager.php');
			$cgm=new CarGroupManager();
			$num = $cgm->isPausePermit($userId);
			if (null==$num) {
				throw new Exception('', 4016);
			}
			
		}

		//看是否有违章
		require_once (Frm::$ROOT_PATH . 'order/classes/ViolateManager.php');
		$vm = new ViolateManager();
		if ($vm -> hasViolate($userId)) {
			throw new Exception('', 4213);
		}

		//驾照有效期
		require_once (Frm::$ROOT_PATH . 'user/classes/DrivingLicenceManager.php');
		$dlm = new DrivingLicenceManager();
		$expire = $dlm -> getDrivingLicenceExpire($userId);
		if ($expire - $now < 7 * 3600 * 24) {
			throw new Exception('', 4015);
		}

		//对已有订单是否冲突的检查
		$this -> checkHasOrder($carId, $userId, $startTime, $endTime, $oldOrder, $orderConf);

		//检查用户未完成订单数
		$this -> checkOrderUndone($userId, $oldOrder);

	}

	protected function oldOrderCheck($userId, $carId, $startTime, $endTime, $medalId, $oldOrder, $orderConf, $now) {
		if (null == $oldOrder) {
			return;
		}

		$carModifyStat = array(Order::$STATUS_NOT_PAY, Order::$STATUS_ORDERED, Order::$STATUS_STARTED, Order::$STATUS_REVERT_TIMEOUT);
		if (!in_array($oldOrder -> stat, $carModifyStat)) {
			throw new Exception('', 4236);
		}

		if ($oldOrder -> order_start_time < $now) {
			if ($startTime != $oldOrder -> order_start_time) {
				throw new Exception('', 4013);
			}
		}

		if ($oldOrder -> modify_times >= self::$MAX_MODIFY_TIMES) {
			throw new Exception('', 4235);
		}
	}

	/**
	 * 判断车或用户是否有订单
	 */
	protected function checkHasOrder($carId, $userId, $startTime, $endTime, $oldOrder, $orderConf) {
		if (null != $oldOrder) {
			$where = " AND order_id <> " . $oldOrder -> order_id . " AND order_id <> " . $oldOrder -> old_order_id;
		} else {
			$where = '';
		}

		$orderConfigTime = $orderConf['order_interval'] * 60;

		$orgStartTime=$startTime;
		$orgEndTime=$endTime;

		$startTime -= $orderConfigTime;
		$endTime += $orderConfigTime;
		
		$sql = "SELECT o.user_id,o.car_id,o.order_id,o.order_start_time,o.order_end_time FROM edo_cp_car_order o WHERE
			(( 	o.order_start_time >=" . $startTime . " AND o.order_start_time<=" . $endTime . ' ) ' . "
			OR    (	o.order_end_time>" . $startTime . " AND o.order_end_time<" . $endTime . ' ) ' . "
			OR    (	o.order_start_time<=" . $startTime . " AND o.order_end_time>=" . $endTime . "))
			AND ( o.car_id = $carId OR o.user_id = $userId ) $where
			AND " . $this -> getAvailableOrderWhereSQL();
		$db = Frm::getInstance() -> getDb();
		//echo $sql;exit;
		$orders = $db -> getAll($sql);

		//过滤掉同一个用户,不计算缓冲时间的订单
		foreach ($orders as $k => $v) {
			if ($v['user_id']==$userId && ($v['order_start_time']>$orgEndTime||$v['order_end_time']<$orgStartTime)) {
				unset($orders[$k]);
			}
		}
		//add by yangbei 2013-06-12 增加超时还车判断
		$this->checkUnRevert($startTime,$carId);
		//add by yangbei 2013-06-12 增加超时还车判断
		foreach ($orders as $row) {
			if (!empty($row)) {
				if ($row['user_id'] == $userId) {
					//同一时间不能订两辆车;
					throw new Exception('', 4014);
				} else {
					//"该车在指定的时间段内已经被预定。";
					throw new Exception('', 4008);
				}
			}
		}
		
	}

	/**
	 * 检查用户未完成订单的数量，并且，如果有未支付的，不能再订车.
	 *
	 * @param unknown_type $userId
	 */
	protected function checkOrderUndone($userId, $oldOrder) {
		if (null != $oldOrder) {
			$where = " AND order_id <> " . $oldOrder -> order_id . " AND order_id <> " . $oldOrder -> old_order_id;
		} else {
			$where = '';
		}

		$orderStatArr = array(Order::$STATUS_NOT_PAY, Order::$STATUS_ORDERED, Order::$STATUS_STARTED, Order::$STATUS_REVERT_TIMEOUT);
		$sql = "SELECT order_stat FROM edo_cp_car_order WHERE order_stat IN ( " . implode(',', $orderStatArr) . " )
			AND modify_stat = " . Order::$MODIFY_STAT_NOT_MODIFY . " AND user_id = $userId $where";
		$db = Frm::getInstance() -> getDb();
		$ret = $db -> limitSelect($sql, 0, self::$MAX_UNDONE_COUNT);
		if (count($ret) >= self::$MAX_UNDONE_COUNT) {
			throw new Exception('', 4229);
		}

		foreach ($ret as $value) {
			if (Order::$STATUS_NOT_PAY == $value['order_stat']) {
				throw new Exception('', 4233);
			} else if (Order::$STATUS_REVERT_TIMEOUT == $value['order_stat']) {
				throw new Exception('', 4238);
			}
		}
	}
	//add by yangbei 2013-05-13	增加获取天物可用订单
	public function twGetUserAvailableOrder($userId) {
		$sql = "SELECT o.order_id , o.order_no , o.order_stat , o.car_id , o.order_start_time, o.order_end_time 
			FROM edo_cp_car_order o WHERE
			o.user_id = $userId AND " . $this -> twGetAvailableOrderWhereSQL() . " ORDER BY order_start_time ";
		$db = Frm::getInstance() -> getDb();
		$ret = $db -> getAll($sql);
		if (empty($ret)) {
			return null;
		}

		$row = null;
		foreach ($ret as $value) {
			$row = $value;
			//通过getAvailableOrderWhereSQL查出来的，一个订单最多只有两条记录，即已经支付过的和刚刚修改的还未支付的，而未支付的肯定是新提交的订单。
			if (Order::$STATUS_NOT_PAY == $value['order_stat']) {
				continue;
			} else {
				break;
			}
		}

		return $this -> generateOrder($row);
	}
	protected function twGetAvailableOrderWhereSQL() {
		require_once (Frm::$ROOT_PATH . 'order/classes/Order.php');
		//@formatter:off
		return "  (o.order_stat = " . Order::$STATUS_NOT_PAY . " OR 
			o.order_stat = " . Order::$STATUS_ORDERED . " OR o.order_stat = " . Order::$STATUS_STARTED . " 
			OR o.order_stat=" . Order::$STATUS_REVERT_TIMEOUT . " 
			OR o.order_stat=" . Order::$STATUS_COMPLETED." ) 
			AND (o.modify_stat=" . Order::$MODIFY_STAT_NOT_MODIFY . " 
			OR o.modify_stat=" . Order::$MODIFY_STAT_IN_MODIFY . " )";
		//@formatter:on
	}

	//add by yangbei 2013-05-13	
	protected function getAvailableOrderWhereSQL() {
		require_once (Frm::$ROOT_PATH . 'order/classes/Order.php');
		//@formatter:off
		return "  (o.order_stat = " . Order::$STATUS_NOT_PAY . " OR 
			o.order_stat = " . Order::$STATUS_ORDERED . " OR o.order_stat = " . Order::$STATUS_STARTED . " 
			OR o.order_stat=" . Order::$STATUS_REVERT_TIMEOUT . " ) 
			AND (o.modify_stat=" . Order::$MODIFY_STAT_NOT_MODIFY . " 
			OR o.modify_stat=" . Order::$MODIFY_STAT_IN_MODIFY . " )";
		//@formatter:on
	}

	protected function generateOrder($dbRetRow) {
		if (empty($dbRetRow)) {
			return null;
		}

		$order = new Order();
		$order -> order_id = $dbRetRow['order_id'];
		$order -> order_no = $dbRetRow['order_no'];
		$order -> order_stat = $dbRetRow['order_stat'];
		$order -> car_id = $dbRetRow['car_id'];
		$order -> user_id = $dbRetRow['user_id'];
		$order -> add_time = $dbRetRow['add_time'];
		$order -> order_start_time = $dbRetRow['order_start_time'];
		$order -> order_end_time = $dbRetRow['order_end_time'];
		if (!empty($dbRetRow['real_start_time'])) {
			$order -> real_start_time = $dbRetRow['real_start_time'];
		}
		if (!empty($dbRetRow['real_end_time'])) {
			$order -> real_end_time = $dbRetRow['real_end_time'];
		}
		if (!empty($dbRetRow['start_mileage'])) {
			$order -> start_mileage = $dbRetRow['start_mileage'];
		}
		if (!empty($dbRetRow['end_mileage'])) {
			$order -> end_mileage = $dbRetRow['end_mileage'];
		}
		$order -> medal_id = $dbRetRow['medal_id'];
		$order -> old_order_id = $dbRetRow['old_order_id'];
		$order -> modify_times = $dbRetRow['modify_times'];
		$order -> use_car_password = $dbRetRow['use_car_password'];
		$order -> modify_stat = $dbRetRow['modify_stat'];
		$order -> car_has_bug = $dbRetRow['car_has_bug'];

		return $order;
	}

	protected function getUserCardId($userManager, $userId) {
		$cardId = $userManager -> getCardId($userId);
		if (empty($cardId)) {
			$cardId = $userId;
		}

		return $cardId;
	}

	public function getMyOrderList($userId) {
		$sql = "SELECT o.order_id , o.order_no , o.order_start_time , o.order_end_time , o.order_org_cost ,
				o.car_id , c.name , c.number , st.name AS station , c.icon , o.order_stat ,w.open_login_num, o.add_time ,
				i.num as invoice_num , c.brand , c.gearbox,o.modify_stat
				FROM edo_cp_car_order  o 
				LEFT JOIN edo_cp_car  c ON c.id = o.car_id 
				LEFT JOIN edo_cp_station st ON c.station = st.id
				LEFT JOIN 
				(SELECT COUNT(1) AS num , order_id  FROM edo_cp_invoice_log  
				GROUP BY order_id) i  ON i.order_id=o.order_id  
				LEFT JOIN 
				( SELECT user_id, COUNT(1) as open_login_num FROM edo_open_login 
				WHERE status=1 GROUP BY user_id )  w  ON w.user_id = o.user_id  
				WHERE o.user_id = $userId AND o.modify_stat = 0 
				ORDER BY o.add_time DESC ";

		$db = Frm::getInstance() -> getDb();
		$orderRet = $db -> getAll($sql);
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

		$sql = "SELECT c.amount , c.order_no , c.item FROM order_charge c
			WHERE order_no IN (" . implode(',', $orderNoArr) . ") AND ( item = 1 OR item = 2 OR item = 3 )
			AND status IN ( 0 , 1 )
			ORDER BY order_no ";
		$arr = array();
		$depositCountArr = array();
		$ret = $db -> getAll($sql);
		if (!empty($ret)) {
			require_once (Frm::$ROOT_PATH . 'order_charge/classes/OrderCharge.php');
			foreach ($ret as $value) {
				$depositCountArr[$value['order_no']] += $value['amount'];

				$item = '';
				if (OrderCharge::$ITEM_USE_TIME_DEPOSIT == $value['item']) {
					$item = 'timeDeposite';
				} else if (OrderCharge::$ITEM_USE_MILE_DEPOSIT == $value['item']) {
					$item = 'mileDeposite';
				} else if (OrderCharge::$ITEM_VIOLATE_DEPOSIT == $value['item']) {
					$item = 'violateDeposite';
				}
				$arr[$value['order_no']][$item] = $value['amount'];
			}
		}

		foreach ($orderRet as &$value) {
			$value['deposit'] = $depositCountArr[$value['order_no']];
			$value['depositDetail'] = $arr[$value['order_no']];
		}

		return $orderRet;
	}

	public function cancel($order, $superPassword) {
		$um = new UserManager();
		if (!$um -> checkSuperPassword($order -> user_id, $superPassword)) {
			throw new Exception('', 4215);
		}

		$db = Frm::getInstance() -> getDb();
		$sql = " UPDATE edo_cp_car_order o SET order_stat=" . Order::$STATUS_CANCELED . " WHERE order_id=" . $order -> order_id;
		$db -> execSql($sql);

		$this -> notifyOrderCancel($order, $listener);
	}
	//add by yangbei 2013-04-25 添加天物订单取消方法
	public function twCancel($order) {	

		$db = Frm::getInstance() -> getDb();
		$sql = " UPDATE edo_cp_car_order o SET order_stat=" . Order::$STATUS_CANCELED . " WHERE order_id=" . $order -> order_id;
		$db -> execSql($sql);

		$this -> notifyOrderCancel($order, $listener);
	}
	//add by yangbei 2013-04-25 
	public function notifyOrderCancel($order) {
		require_once (Frm::$ROOT_PATH . 'order/conf/listener.php');
		foreach ($listener as $key => $value) {
			include ($value['classPath']);
			$obj = new $value['className'];
			$obj -> onCancel($order);
		}
		$this->sendOrderToCar('',$order->car_id);
	}


	/**
	 * 与payforOrder参数不同，本函数接受的参数为Order对象
	 */
	public function twPayforOrderOrder($order) {
		require_once (Frm::$ROOT_PATH . 'user/classes/UserManager.php');
		$um = new UserManager();

		$db = Frm::getInstance() -> getDb();
		//防止重复支付等错误，所以，要锁数据库。
		$trans = $db -> startTransaction();

		$tmpOrder = $this -> getOrder($order -> order_id);
		if (Order::$STATUS_NOT_PAY != $tmpOrder -> order_stat) {
			throw new Exception('', 4231);
		}

		$stat = Order::$STATUS_ORDERED;
		//如果有old_order_id>0,把old_order的修改状态改为修改完成
		if ($order -> old_order_id > 0) {
			$db = Frm::getInstance() -> getDb();
			$sql = " UPDATE edo_cp_car_order SET modify_stat=" . Order::$MODIFY_STAT_MODIFIED . "
				WHERE order_id=" . $order -> old_order_id . " AND modify_stat <> " . Order::$MODIFY_STAT_MODIFIED;
			$db -> execSql($sql);

			//修改时，新的订单记录为未支付状态。
			//支付时，新记录要与原订单状态一致。但有可能原订单未支付
			$oldOrder = $this -> getOrder($order -> old_order_id);
			if (Order::$STATUS_NOT_PAY != $oldOrder -> order_stat) {
				$stat = $oldOrder -> order_stat;
			}
		}
		$this -> setOrderStatus($order -> order_id, $stat);

		$trans -> commit();

		if ($order -> old_order_id <= 0) {
			//如果是新订单，下发成功短信
			require_once (Frm::$ROOT_PATH . 'user/classes/UserManager.php');
			$um = new UserManager();
			$userInfo = $um -> getUser($order -> user_id);
			$this -> twSendOrderPassword($order -> order_id, $userInfo['phone'], $order -> car_id, $order -> order_start_time, $order -> order_end_time);
		}

		$this -> sendOrderToCar($order -> user_id, $order -> car_id);
		
		$this -> setTimer($order -> order_end_time+60*5,'twSendConnectMsg',$order -> order_id,'MsgPrompt');
		$this -> setTimer($order -> order_end_time, 'onTimeEnd', $order -> order_id, 'Order time end');
	}

	protected function twSendOrderPassword($orderId, $phone, $carId, $orderStartTime, $orderEndTime) {
		$db = Frm::getInstance() -> getDb();

		require_once (Frm::$ROOT_PATH . 'resource/classes/CarManager.php');
		$cm = new CarManager();
		$car = $cm -> getCar($carId);
		$carName = $car -> name;
		$station = $car -> station;
		$carNum = $car -> number;
		$rand = 123456;

		$sStartTime = date('Y-m-d H:i', $orderStartTime);
		$sEndTime = date('Y-m-d H:i', $orderEndTime);
		$msgContent = "您已成功预定 {$sStartTime} 到 {$sEndTime}，在“{$station}”的车辆： {$carName}(车牌:{$carNum})，用车密码是：{$rand}，祝您旅途愉快。";

		require_once (Frm::$ROOT_PATH . 'system/sms_platform/classes/SmsSender.php');
		$msgSender = new SmsSender();
		$msgSender -> send($msgContent, $phone);

		$sql = "UPDATE edo_cp_car_order SET use_car_password = '{$rand}' WHERE order_id = $orderId";
		$db -> execSql($sql);

		return $rand;
	}
	//add by yangbei 2013-06-12 增加超时还车判断
	public function checkUnRevert($order_start_time,$carId){

		$sql = "SELECT * FROM edo_cp_car_order WHERE car_id=".$carId." AND order_end_time<=".$order_start_time." order by order_end_time desc";
		$db = Frm::getInstance() -> getDb();
		$order = $db -> getRow($sql);
		if (!empty($order)) {
			// var_dump($order);
			if ($order['order_stat']=='6'&& $order['modify_stat']=='0') {
				require_once (Frm::$ROOT_PATH . 'user/classes/UserManager.php');
				$um = new UserManager();
				$userInfo = $um -> getUser($order['user_id']);
				$userId = $um -> getLoginUserId();
				$nUserInfo=$um -> getUser($userId);
				// require_once (Frm::$ROOT_PATH . 'system/sms_platform/classes/SmsSender.php');
				// $msgSender = new SmsSender();
				// $msgSender -> send('用户:'.$nUserInfo['phone'].'要订车，之前的用户'.$userInfo['phone'].'超时还车，请及时处理', '15606556731');
				require_once (Frm::$ROOT_PATH . 'system/mail/send.php');
				$mail = new Send();
				$mail->mail('dingdan@eduoauto.com','超时还车，请及时处理','用户:'.$nUserInfo['phone'].'要订车，之前的用户'.$userInfo['phone'].'超时还车，请及时处理');
				throw new Exception("", 4241);
			}
		}
	}
}
?>
