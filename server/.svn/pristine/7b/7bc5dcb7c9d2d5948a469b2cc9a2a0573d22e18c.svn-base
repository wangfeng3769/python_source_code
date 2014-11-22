<?php

class OrderCharge {

	static public $ITEM_VIOLATE_DEPOSIT = 1;
	static public $ITEM_USE_TIME_DEPOSIT = 2;
	static public $ITEM_USE_MILE_DEPOSIT = 3;
	static public $ITEM_TIME_RENT = 4;
	static public $ITEM_MILE_RENT = 5;
	static public $ITEM_CANCEL_FEE = 6;
	static public $ITEM_MODIFY_FEE = 7;
	//违章罚款
	static public $ITEM_VIOLATE_FEE = 8;
	//违章代办费
	static public $ITEM_VIOLATE_AGENCY_FEE = 9;
	//还车超时费
	static public $ITEM_TIMEOUT_FEE = 10;

	static public $PAYMENT_TYPE_CASH = 0;
	static public $PAYMENT_TYPE_UPMOBILE = 1;
	static public $PAYMENT_TYPE_UPWEB = 2;

	static public $STATUS_WITHOUT_PAY = 0;
	static public $STATUS_PAIED = 1;
	static public $STATUS_CANCELED = 2;
	static public $STATUS_SETTLED = 3;
	//已经结算

	static public $BALANCE_STATUS_REQ = 0;
	static public $BALANCE_STATUS_COMPLETE = 1;

	public function __construct() {
	}

	/**
	 * 订单管理里添加订单的接口
	 * @param Order $order
	 */
	public function onCreate($order, $oldOrder) {
		try {
			$this -> depositCharge($order, $oldOrder);
			return true;
		} catch(Exception $e) {
			return false;
		}
	}

	/**
	 * 银联手机支付的接口
	 */
	public function onPurchase($purchaseObj) {
		try {
			$this -> orderPayment($purchaseObj -> orderId, $purchaseObj -> amount, $purchaseObj -> id, self::$PAYMENT_TYPE_UPMOBILE);
			return true;
		} catch(Exception $e) {
			return false;
		}
	}

	public function onEnd($order) {
		$startTime = $order -> order_start_time;
		$endTime = $order -> order_end_time;
		if ($order -> real_end_time > $endTime) {
			$endTime = $order -> real_end_time;
		}

		require_once (Frm::$ROOT_PATH . 'order_charge/classes/PriceManager.php');
		$pm = new PriceManager();

		require_once (Frm::$ROOT_PATH . 'user/classes/UserManager.php');
		$um=new UserManager();
		$user=$um->getUser($order -> user_id);
		$groupId=$user['user_group_id'];

		$hourDiscount= $pm->getUserGroupDiscount($groupId);
		if (empty($hourDiscount)) {
			$hourDiscount=1;
		}
		else{
			$hourDiscount=$hourDiscount['discount']/100;
		}
		
		$basePrice = $pm -> getCarBasePrice($order -> car_id);
		foreach ($basePrice as $k => $v) {
			$basePrice[$k]['hour_price']=$v['hour_price']*$hourDiscount;
		}
		//end
		require_once (Frm::$ROOT_PATH . 'system/classes/HolidayManager.php');
		$kmPrice = $basePrice[HolidayManager::$WORKDAY]['price_km'];

		require_once (Frm::$ROOT_PATH . 'system/classes/HolidayManager.php');
		$hm = new HolidayManager();
		$holidayArr = $hm -> getHolidayArr($startTime, $endTime);

		$dateTopPrice = $pm -> getCarTopPriceDayArr($order -> car_id, $startTime, $endTime);


		$arrHours = $pm -> getHourPriceInDate($order -> car_id, $startTime, $endTime, $basePrice, $holidayArr);

		$arrPeriodHour = $pm -> getPeriodTopInDate($order -> car_id, $startTime, $endTime, $holidayArr);

		require_once (Frm::$ROOT_PATH . 'TimeJs/orderAlg.php');
		$orderAlg = new COrderAlg();
		$ret = $orderAlg -> CloseOrder($order -> order_start_time, $order -> order_end_time, $endTime, (float)($order -> mile) / 1000, $kmPrice, '12:00', $arrHours, $arrPeriodHour, $dateTopPrice);

		$this -> saveUseFee($order, $ret);
	}

	public function onPayTimeout($order) {
		$this -> invalidOrderCharge($order);
	}

	public function onCancel($order) {
		$this -> invalidOrderCharge($order);
		
		require_once (Frm::$ROOT_PATH . 'order_charge/classes/DepositComputer.php');
		$depositComputer = new DepositComputer();
		$cancelFee = $depositComputer -> getCancel($order);
		$status=self::$STATUS_WITHOUT_PAY;
		$now=date('Y-m-d H:i:s');
		if ($cancelFee > 0) {
			$db = Frm::getInstance() -> getDb();
			$sql = "INSERT INTO order_charge (order_no , item , amount,order_id , add_time , user_id , status ) 
				VALUES ( '{$order->order_no}' , " . self::$ITEM_CANCEL_FEE . " , $cancelFee ,
				'{$order->order_id}' , '{$now}' , {$order->user_id} , $status )";
			$db -> execSql($sql);
			}
		

	}

	/**
	 * 作废订单计费
	 */
	protected function invalidOrderCharge($order) {
		$sql = "UPDATE order_charge SET status = " . self::$STATUS_CANCELED . " 
			WHERE order_id = " . $order -> order_id . " AND status = " . self::$STATUS_WITHOUT_PAY;
		$db = Frm::getInstance() -> getDb();
		$db -> execSql($sql);
	}

	protected function saveUseFee($order, $feeInfo) {
		$status = self::$STATUS_WITHOUT_PAY;
		$strNow = date('Y-m-d H:i:s');
		$db = Frm::getInstance() -> getDb();
		$itemsStr=sprintf('(%s,%s,%s)',self::$ITEM_TIME_RENT,self::$ITEM_TIMEOUT_FEE,self::$ITEM_MILE_RENT);
	
		//增加判断,防止计价数据重复插入
		$sql = 	" SELECT COUNT(1) FROM order_charge WHERE order_id=".$order->order_id.
				" AND item IN $itemsStr";
		$hasCount=$db->getOne($sql);
		if ($hasCount) {
			return;
		}
		//end
		$timeRent = $feeInfo['UseCar'];
		if ($timeRent > 0) {
			$sql = "INSERT INTO order_charge (order_no , item , amount,order_id , add_time , user_id , status )
				VALUES ( '{$order->order_no}' , " . self::$ITEM_TIME_RENT . " , $timeRent ,
				'{$order->order_id}' , '$strNow' , {$order->user_id} , $status )";
			$db -> execSql($sql);
		}

		$timeoutFee = $feeInfo['Penalty'];
		if ($timeoutFee > 0) {
			$sql = "INSERT INTO order_charge (order_no , item , amount,order_id , add_time , user_id , status )
				VALUES ( '{$order->order_no}' , " . self::$ITEM_TIMEOUT_FEE . " , $timeoutFee ,
				'{$order->order_id}' , '$strNow' , {$order->user_id} , $status )";
			$db -> execSql($sql);
		}

		$mileFee = $feeInfo['kmPrice'];
		if ($mileFee > 0) {
			$sql = "INSERT INTO order_charge (order_no , item , amount,order_id , add_time , user_id , status )
				VALUES ( '{$order->order_no}' , " . self::$ITEM_MILE_RENT . " , $mileFee ,
				'{$order->order_id}' , '$strNow' , {$order->user_id} , $status )";
			$db -> execSql($sql);
		}
	}

	/**
	 * 对订单进行支付
	 */
	public function orderPayment($orderId, $amount, $ext_id, $payMentType) {
		if (!is_numeric($orderId) || !is_numeric($amount) || !is_numeric($payMentType) || $amount < 0) {
			//不是对订单的支付操作，可能是充值
			throw new Exception('', 4000);
		}

		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$om = new OrderManager();
		$order = $om -> getOrder($orderId);

		if (null == $order) {
			throw new Exception('', 4000);
		}

		$d = $this -> getDepositWithoutPayInfo($order -> order_no);
		//		if (empty($d)) {
		//			throw new Exception('', 4000);
		//		}

		$c = 0;

		

		$idArr = array();
		foreach ($d as $value) {
			$c += $value['amount'];
			$idArr[] = $value['id'];
		}

		if ($c != $amount) {
			throw new Exception('', 4000);
		}

		$om -> payforOrderOrder($order);
		
		if ($amount > 0) {
			$this -> savePayment($orderId, $amount, $idArr, $ext_id, $payMentType);

			//如果该用户以前有支付违章押金，退掉以前的违章押金
			$this -> refundOldViolateDeposit($order -> user_id);
		}
	}

	/**
	 * 退还用户以前已经支付的违章押金。因为以前的违章押金到期了，不能再用作别的订单.
	 */
	protected function refundOldViolateDeposit($userId) {
		require_once (Frm::$ROOT_PATH . 'order_charge/classes/DepositComputer.php');
		$dc = new DepositComputer();
		$arrOrders = array();
		$dc -> needPayViolateDeposit($userId, $arrOrders);
		
		foreach ($arrOrders as $row) {
			if (!$row['needRefund']) {
				continue;
			}
			require_once (Frm::$ROOT_PATH . 'order_charge/classes/Settlement.php');
			$s = new Settlement();
			$s -> refund(array($row['chargeId']), $row['amount']);
		}
	}

	/**
	 * 保存用户的支付信息到数据库
	 */
	protected function savePayment($orderId, $amount, $chargeIdArr, $ext_id, $payMentType) {
		if (0 == $amount) {
			return;
		}

		$db = Frm::getInstance() -> getDb();
		$trans = $db -> startTransaction();

		$sql = "INSERT INTO order_balance (order_id , pay_amount , account_type , ext_id , relation , status )
			VALUES( '$orderId' , '$amount' , '$payMentType' ,
			'$ext_id' , 0 , " . self::$BALANCE_STATUS_COMPLETE . " ) ";
		$db -> execSql($sql);
		$balanceId = $trans -> lastId();

		$strNow = date('Y-m-d H:i:s');
		foreach ($chargeIdArr as $value) {
			$sql = "INSERT INTO charge2balance ( balance_id , charge_id ) VALUES ( $balanceId , $value) ";
			$db -> execSql($sql);

			$sql = "UPDATE order_charge SET status = " . self::$STATUS_PAIED . " , pay_time = '$strNow' 
				WHERE id = $value ";
			$db -> execSql($sql);
		}

		$trans -> commit();
	}

	/**
	 * 取得该订单所有押金总和
	 */
	public function getAllDepositCount($orderNo) {
		$depositArr = $this -> getAllDepositInfo($orderNo);
		if (empty($depositArr)) {
			return $depositArr;
		}

		$c = 0;
		foreach ($depositArr as $value) {
			$c += $value['amount'];
		}

		return $c;
	}

	/**
	 * 取得该订单所有未支付的押金总和
	 */
	public function getDepositWithoutPayCount($orderNo) {
		$depositArr = $this -> getAllDepositInfo($orderNo);
		if (empty($depositArr)) {
			return 0;
		}

		$c = 0;
		foreach ($depositArr as $value) {
			if (self::$STATUS_WITHOUT_PAY == $value['status']) {
				$c += $value['amount'];
			}
		}

		return $c;
	}

	/**
	 * 取得该订单未支付押金的信息
	 */
	public function getDepositWithoutPayInfo($orderNo) {
		$depositArr = $this -> getAllDepositInfo($orderNo);
		if (empty($depositArr)) {
			return $depositArr;
		}

		foreach ($depositArr as $key => &$value) {
			if (self::$STATUS_PAIED == $value['status']) {
				unset($depositArr[$key]);
			}
		}

		return $depositArr;
	}

	/**
	 * 取得用户最早未结算的违章押金
	 */
	public function getEarliestWithoutSettledVD($userId) {
		$sql = "SELECT id , item , amount , order_id , pay_time FROM order_charge 
			WHERE user_id = $userId AND item = " . self::$ITEM_VIOLATE_DEPOSIT . " 
			AND status = " . self::$STATUS_PAIED . " ORDER BY pay_time ASC ";
		$db = Frm::getInstance() -> getDb();
		$depositArr = $db -> getRow($sql);
		if (empty($depositArr)) {
			return array();
		}

		return $depositArr;
	}

	public function getViolateDepositByOrderArr($orderIdArr) {
		if (empty($orderIdArr)) {
			return array();
		}

		$sqlOrderId = implode(',', $orderIdArr);
		$sql = "SELECT id , item , amount , order_id , pay_time FROM order_charge 
			WHERE item = " . self::$ITEM_VIOLATE_DEPOSIT . " AND order_id IN ( $sqlOrderId )  
			AND status = " . self::$STATUS_PAIED;
		$db = Frm::getInstance() -> getDb();
		$ret = $db -> getAll($sql);
		if (empty($ret)) {
			return array();
		}

		$arr = array();
		foreach ($ret as $value) {
			$arr[$value['order_id']] = $value;
		}

		return $arr;
	}

	/**
	 * 取得该订单所有押金的信息
	 */
	protected function getAllDepositInfo($orderNo) {
		if (empty($orderNo)) {
			throw new Exception('', 4000);
		}

		$sql = "SELECT c.id , c.item , c.amount , c.status FROM order_charge c WHERE c.order_no = '$orderNo' AND
			( c.item = " . self::$ITEM_VIOLATE_DEPOSIT . " OR c.item = " . self::$ITEM_USE_TIME_DEPOSIT . "
			OR c.item = " . self::$ITEM_USE_MILE_DEPOSIT . " ) AND 
			( c.status = " . self::$STATUS_WITHOUT_PAY . " OR c.status = " . self::$STATUS_PAIED . " ) ";
		$db = Frm::getInstance() -> getDb();
		$ret = $db -> getAll($sql);
		if (empty($ret)) {
			return array();
		}

		return $ret;
	}

	/**
	 * 取得已经支付过的押金，按类型分好组，并求和
	 */
	public function getDepositPaiedGroupedCount($orderNo) {
		$ret = $this -> getAllDepositInfo($orderNo);
		if (empty($ret)) {
			return $ret;
		}

		$d = array();
		foreach ($ret as $row) {
			switch ($row['item']) {
				case self::$ITEM_VIOLATE_DEPOSIT :
					if ($row['status'] == self::$STATUS_PAIED) {
						$d['violateDeposit'] += $row['amount'];
					}
					break;
				case self::$ITEM_USE_TIME_DEPOSIT :
					if ($row['status'] == self::$STATUS_PAIED) {
						$d['useTimeDeposit'] += $row['amount'];
					}
					break;
				case self::$ITEM_USE_MILE_DEPOSIT :
					if ($row['status'] == self::$STATUS_PAIED) {
						$d['useMileDeposit'] += $row['amount'];
					}
					break;
			}
		}
		return $d;
	}

	/**
	 * 对订单的押金进行计费。
	 */
	protected function depositCharge($order, $oldOrder = null) {
		$db = Frm::getInstance() -> getDb();

		//对使用的奖章进行检查
		if ($order -> medal_id > 0) {
//			require_once (Frm::$ROOT_PATH . 'marketing/medal/classes/MedalContainer.php');
//			$mc = new MedalContainer();
//			$m = $mc -> getMedalObj($order -> medal_id);
//			$mc -> isMedalCanBeUse($m);
		}

		require_once (Frm::$ROOT_PATH . 'order_charge/classes/DepositComputer.php');
		$depositComputer = new DepositComputer();
		$ret = $depositComputer -> getViolateDeposit($order -> user_id, $order -> car_id);
		$violateDeposit = $ret['violateDeposit'];
		$useDeposit = $depositComputer -> getUseDeposit($order -> user_id, $order -> car_id, $order -> order_start_time, $order -> order_end_time);
		$now = time();
		$status = self::$STATUS_WITHOUT_PAY;
		//对老订单进行检查
		if ($order -> old_order_id > 0) {
			//此处的oldOrder应该是最后一次支付生效的oldOrder,如果没有已支付的订单的修改,不产生费用
			$modifyFee = $this -> getModifyFee($order, $oldOrder);
			if ($modifyFee > 0) {
				$sql = "INSERT INTO order_charge (order_no , item , amount,order_id , add_time , user_id , status ) 
					VALUES ( '{$order->order_no}' , " . self::$ITEM_MODIFY_FEE . " , $modifyFee ,
					'{$order->order_id}' , $now , {$order->user_id} , $status )";
				$db -> execSql($sql);
			}

			//获取老订单已支付的押金
			$oldOrderDeposit = $this -> getDepositPaiedGroupedCount($order -> order_no);
			$useDeposit['DayPrice'] = $useDeposit['DayPrice'] - $oldOrderDeposit['useTimeDeposit'] > 0 ? $useDeposit['DayPrice'] - $oldOrderDeposit['useTimeDeposit'] : 0;
			$useDeposit['CarPrice'] = $useDeposit['CarPrice'] - $oldOrderDeposit['useMileDeposit'] > 0 ? $useDeposit['CarPrice'] - $oldOrderDeposit['useMileDeposit'] : 0;
			if (0 == $useDeposit['DayPrice'] && 0 == $useDeposit['CarPrice'] && Order::$STATUS_NOT_PAY != $oldOrder -> order_stat) {
				//如果本次修改订单不需要增加押金，那么该订单的状态直接为已支付
				$om = new OrderManager();
				$om -> payforOrderOrder($order);
			}
		}

		$strNow = date('Y-m-d H:i:s');
		if ($violateDeposit > 0) {
			$sql = "INSERT INTO order_charge (order_no , item , amount,order_id , add_time , user_id , status )
				VALUES ( '{$order->order_no}' , " . self::$ITEM_VIOLATE_DEPOSIT . " , $violateDeposit ,
				'{$order->order_id}' , '$strNow' , {$order->user_id} , $status )";
			$db -> execSql($sql);
		}
		if ($useDeposit['DayPrice'] > 0) {
			$sql = "INSERT INTO order_charge (order_no , item , amount,order_id , add_time , user_id , status )
				VALUES ( '{$order->order_no}' , " . self::$ITEM_USE_TIME_DEPOSIT . " , 
				{$useDeposit['DayPrice']} ,'{$order->order_id}' , '$strNow' , {$order->user_id} , $status )";
			$db -> execSql($sql);
		}
		if ($useDeposit['CarPrice'] > 0) {
			$sql = "INSERT INTO order_charge (order_no , item , amount,order_id , add_time , user_id , status )
				VALUES ( '{$order->order_no}' , " . self::$ITEM_USE_MILE_DEPOSIT . " , 
				{$useDeposit['CarPrice']},'{$order->order_id}' , '$strNow' , {$order->user_id} , $status )";
			$db -> execSql($sql);
		}
	}

	public function getVialteDepositByOrderId($orderId) {
		$allItem = $this -> getAllItemMoneyByOrderId($orderId);
		return $allItem['violateDeposit'];
	}

	function getDepositByOrderId($orderId) {
		$allItem = $this -> getAllItemMoneyByOrderId($orderId);

		return $allItem['useTimeDeposit'] + $allItem['useMileDeposit'] + $allItem['violateDeposit'] + $allItem['cancelFee'] + $allItem['modifyFee'];
	}

	function getArrUseDepositByOrderId($orderId) {
		$allItem = $this -> getAllItemMoneyByOrderId($orderId);
		$useDeposit = array('timeDeposit' => $allItem['useTimeDeposit'], 'mileDeposit' => $allItem['useMileDeposit']);
		return $useDeposit;
	}

	/**
	 * 修改订单费用计算
	 * @param  [Order 对象] $order 新生成的订单对象
	 * @return [type]        [description]
	 */
	public function getModifyFee($order, $oldOrder) {
		require_once (Frm::$ROOT_PATH . 'order_charge/classes/DepositComputer.php');
		$depositComputer = new DepositComputer();
		$modifyFee = $depositComputer -> getModifyFee($order, $oldOrder);

		return $modifyFee;
	}

	public function getChargeIdsByOrderId($orderId) {
		$sql = " SELECT id FROM order_charge WHERE order_id=" . $orderId;
		$db = Frm::getInstance() -> getDb();
		$ret = $db -> getAll($sql);
		$ids = array();
		foreach ($ret as $v) {
			$ids[] = $v['id'];
		}
		return $ids;
	}

	public function getAllItemMoneyByOrderId($orderId) {
		$db = Frm::getInstance() -> getDb();
		$sql = " SELECT c.amount,c.item
				FROM order_charge c
				WHERE c.order_id='{$orderId}'";
		$ret = $db -> getAll($sql);

		$itemMoney = array();
		foreach ($ret as $v) {
			switch ($v['item']) {
				case self::$ITEM_VIOLATE_DEPOSIT :
					$itemMoney['violateDeposit'] = $v['amount'];
					break;
				case self::$ITEM_USE_TIME_DEPOSIT :
					$itemMoney['useTimeDeposit'] = $v['amount'];
					break;
				case self::$ITEM_USE_MILE_DEPOSIT :
					$itemMoney['useMileDeposit'] = $v['amount'];
					break;
				case self::$ITEM_CANCEL_FEE :
					$itemMoney['cancelFee'] = $v['amount'];
					break;
				case self::$ITEM_MODIFY_FEE :
					$itemMoney['modifyFee'] = $v['amount'];
					break;
			}
		}
		return $itemMoney;
	}

	public function getBill($orderNo) {
		$statusArr = array(self::$STATUS_WITHOUT_PAY, self::$STATUS_PAIED, self::$STATUS_SETTLED);
		$sqlStatus = implode(',', $statusArr);
		$sql = "SELECT item , amount , order_id , add_time , status , pay_time FROM order_charge 
			WHERE order_no = '$orderNo' AND status IN ( $sqlStatus ) ORDER BY add_time ";
		$db = Frm::getInstance() -> getDb();
		$ret = $db -> getAll($sql);

		return $ret;
	}

	// public function getFrozenViolateDeposit($userId) {
	// $db = Frm::getInstance() -> getDb();
	//
	// //获取未处理的交易流水号
	// $sql = " SELECT sum(vdl.money)
	// FROM edo_violate_deposit_log vdl WHERE vdl.status=0 AND vdl.user_id = $userId
	// GROUP BY vdl.user_id";
	// // echo $sql;
	// $ret = $db -> getOne($sql);
	// if (empty($ret)) {
	// return 0;
	// }
	//
	// return $ret;
	// }

	// public function addViolateDepositLog($orderId) {
	// $violateDeposit = $this -> getVialteDepositByOrderId($orderId);
	// if (empty($violateDeposit)) {
	// return;
	// }
	//
	// require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
	// $om = new OrderManager();
	// $order = $om -> getOrder($orderId);
	//
	// $db = Frm::getInstance() -> getDb();
	// $sql = "INSERT INTO edo_violate_deposit_log SET money='$violateDeposit',status=0,order_id='$orderId',user_id=" . $order -> user_id;
	// $db -> execSql($sql);
	// }

	/**
	 * 对订单进行支付
	 */
	public function twOrderPayment($orderId, $amount, $ext_id, $payMentType) {
		if (!is_numeric($orderId) || !is_numeric($amount) || !is_numeric($payMentType) || $amount < 0) {
			//不是对订单的支付操作，可能是充值
			throw new Exception('', 4000);
		}

		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$om = new OrderManager();
		$order = $om -> getOrder($orderId);

		if (null == $order) {
			throw new Exception('', 4000);
		}

		$d = $this -> getDepositWithoutPayInfo($order -> order_no);
		//		if (empty($d)) {
		//			throw new Exception('', 4000);
		//		}

		$c = 0;

		

		$idArr = array();
		foreach ($d as $value) {
			$c += $value['amount'];
			$idArr[] = $value['id'];
		}

		// if ($c != $amount) {
		// 	throw new Exception('', 4000);
		// }

		$om -> twPayforOrderOrder($order);
		
		if ($amount > 0) {
			$this -> savePayment($orderId, $amount, $idArr, $ext_id, $payMentType);

			//如果该用户以前有支付违章押金，退掉以前的违章押金
			$this -> refundOldViolateDeposit($order -> user_id);
		}
	}

}
?>