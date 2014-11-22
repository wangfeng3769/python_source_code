<?php

class Settlement {

	public function __construct() {
	}

	/**
	 * 由定时器每天定时调用，对所有用户进行结算
	 */
	public function settleAll($start,$limit,$userId=-1) {
		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$om = new OrderManager();
		$orderList = $om -> getWithoutSettledOrderList($userId,$start,$limit);
	
		if (empty($orderList)) {
			return;
		}

		$carIdArr = array();
		$orderIdArr = array();
		foreach ($orderList as $order) {
			$carIdArr[] = $order['car_id'];
			$orderIdArr[] = $order['order_id'];
		}
		require_once (Frm::$ROOT_PATH . 'resource/classes/StationManager.php');
		$sm = new StationManager();
		$cityIdArr = $sm -> getCarArrCityId($carIdArr);

		require_once (Frm::$ROOT_PATH . 'order_charge/classes/OrderCharge.php');
		$oc = new OrderCharge();
		$depositArr = $oc -> getViolateDepositByOrderArr($orderIdArr);
	
		$userCityIndexOL = array();
		foreach ($orderList as $order) {
			//@formatter:off
			$alOrder = array(
				'orderNumber' => $order['order_no'], 
				'orderStart' => $depositArr[$order['order_id']]['pay_time'], 
				'isBigOrder' => !empty($depositArr[$order['order_id']]), 
				'chargeId' => $depositArr[$order['order_id']]['id'],
				'amount' => $depositArr[$order['order_id']]['amount']
			);
			//@formatter:on

			$cityId = $cityIdArr[$order['car_id']];
			$userCityIndexOL[$order['user_id']][$cityId][] = $alOrder;
		}


		require_once (Frm::$ROOT_PATH . 'TimeJs/orderRuleAlg.php');
		$alg = new COrderRuleAlg();
		foreach ($userCityIndexOL as $userId => $row) {
			foreach ($row as $cityId => $arrOrders) {
				$alg -> SettleAccount($userCityIndexOL[$userId][$cityId]);
				foreach ($arrOrders as $alOrder) {
					if ($alOrder['isCloseNow']) {
						// $this -> settlement($alOrder['orderNumber']);
						// print_r($alOrder);
						// foreach ($orderList as $v) {

						// 	if ($v['order_no']==$alOrder['orderNumber']&&$v['order_stat']==4) {
						// 		// print_r($v);
						// 		$orderNo=$v['order_no'];
						// 		require_once (Frm::$ROOT_PATH . 'order_charge/classes/OrderCharge.php');
						// 		$statusArr = array(OrderCharge::$STATUS_WITHOUT_PAY, OrderCharge::$STATUS_PAIED);
						// 		$sqlStatus = implode(',', $statusArr);
						// 		$sql = "SELECT id , amount , item , status FROM order_charge WHERE order_no = '$orderNo' 
						// 			AND status IN ( $sqlStatus ) ";
								
						// 		$db = Frm::getInstance() -> getDb();
						// 		$ret = $db -> getAll($sql);
						// 		echo $orderNo;
						// 		print_r($ret);

						// 	}	
						// }
					}
				}
			}
		}

		$userCityIndexOL['count']=$orderList['count'];
		return $userCityIndexOL;
	}

	/**
	 * 对订单进行结算
	 *
	 * @param string $orderNo
	 * @param string $payMoney 需要多少钱
	 */
	public function settlement($orderNo,$payMoney=-1) {
		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$om = new OrderManager();
		
		// die();

		require_once (Frm::$ROOT_PATH . 'order_charge/classes/OrderCharge.php');
		$statusArr = array(OrderCharge::$STATUS_WITHOUT_PAY, OrderCharge::$STATUS_PAIED);
		$sqlStatus = implode(',', $statusArr);
		$sql = "SELECT id , amount , item , status FROM order_charge WHERE order_no = '$orderNo' 
			AND status IN ( $sqlStatus ) ";
		$db = Frm::getInstance() -> getDb();
		$ret = $db -> getAll($sql);
		if (empty($ret)) {
			throw new Exception('', 5005);
		}

		$costCount = 0;
		$paiedCount = 0;
		$paiedChargeIdArr = array();
		foreach ($ret as $row) {
			if (OrderCharge::$ITEM_USE_MILE_DEPOSIT == $row['item'] || OrderCharge::$ITEM_USE_TIME_DEPOSIT == $row['item'] || OrderCharge::$ITEM_VIOLATE_DEPOSIT == $row['item']) {
				if (OrderCharge::$STATUS_PAIED == $row['status']) {
					$paiedChargeIdArr[] = $row['id'];
					$paiedCount += $row['amount'];
				}
			} else {//除了押金，其他的其实只有两个状态：未支付、已结算，但前面选的时候，没有选择已结算的
				$costCount += $row['amount'];
			}
		}

		if ($payMoney>-1) {
			$costCount=$payMoney;
		}


		if ($paiedCount >= $costCount) {
			$this -> refund($paiedChargeIdArr, $paiedCount - $costCount);
		} else {//交的押金不够了，直接扣现金账户的钱
			throw new Exception('押金不足', 5000);
		}

		$sql = "UPDATE order_charge SET status = " . OrderCharge::$STATUS_SETTLED . " WHERE order_no = '$orderNo'";
		$db -> execSql($sql);
		$om -> settle($orderNo);
	}

	/**
	 * 退款
	 *
	 * @param int $chargeIdArr 支付时合并付款的chargeId数组
	 * @param int $amount 要退多少钱
	 * @return  void
	 */
	public function refund($chargeIdArr, $amount) {
		$sqlChargeId = implode(',', $chargeIdArr);
		$sql = "SELECT b.id , b.pay_amount , b.account_type , b.ext_id FROM order_balance b 
			WHERE b.id IN 
			( SELECT cb.balance_id FROM charge2balance cb WHERE cb.charge_id IN ( $sqlChargeId ) ) 
			ORDER BY b.id DESC ";
		$db = Frm::getInstance() -> getDb();
		$ret = $db -> getAll($sql);
		if (empty($ret)) {
			throw new Exception('can not found source payment log.', 5000);
		}

		foreach ($ret as $row) {
			$refundAmount = $amount;
			if ($row['pay_amount'] < $refundAmount) {
				$refundAmount = $row['pay_amount'];
			}

			require (Frm::$ROOT_PATH . 'order_charge/conf/payment_list.php');
			$apiInfo = $paymentWay[$row['account_type']];
			require_once ($apiInfo['classPath']);
			$obj = new $apiInfo['className']();
			$excp = null;
			
			$refundInfo = $obj -> onRefund($row['ext_id'], $row['pay_amount'] - $refundAmount);

			
			$status = OrderCharge::$BALANCE_STATUS_REQ;
			if ('000' === $refundInfo['rspCode']) {
				$status = OrderCharge::$BALANCE_STATUS_COMPLETE;
			}

			$sql = "INSERT INTO order_balance( pay_amount , account_type , status , relation , ext_id ) 
				VALUES ( -$amount , {$row['account_type']} ,  $status , 1 , '{$refundInfo['logId']}' )";
			$db -> execSql($sql);

			if (null != $excp) {
				throw $excp;
			}
		}
	}

}
?>