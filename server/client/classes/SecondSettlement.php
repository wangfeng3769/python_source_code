<?php
if (!defined('ROOT_PATH')) {
	define('IN_ECS', true);
	require_once (dirname(__FILE__) . '/../../cp/includes/init.php');
}
require_once (ROOT_PATH . "../client/SmsPlatform/SmsSender.php");
require_once (ROOT_PATH . '../moneyAccountManager/AccountManagerContainer.php');

class SecondSettlement {

	/**
	 * 二次结算
	 * @param  int  $time            检查时刻的时间,一般为现在时刻time();
	 * @param  int  $orderDayLimit   检查N天内的订单,系统要求为7天
	 * @param  int  $violateDayLimit 违章处理的时长,系统要求为3天
	 * @param  integer $userId          用户id
	 * @return [type]                   [description]
	 */
	function getDistanceDayOrderInfo($time, $orderDayLimit, $violateDayLimit, $userId = -1, &$violateDeposit = 0) {
		global $sns;
		global $db;
		$intNow = $time;
		// $intNow=time();
		//获取后面计算要用到的数据，按每个用户格式化好.包括用户的订单，交易好，违章信息。
		$userInfo = $this -> getCounterInfo($orderDayLimit, $intNow, $userId);

		// return $userInfo;

		foreach ($userInfo as $k => $v) {
			//获取最新订单的时间,计算七天后的时间
			$userInfo[$k]['lastFinishTime'] = $this -> getAfterTime($v['orderInfo']['finish_time'], $orderDayLimit);

			/************************违章处理***********************/
			//判断是否有违章
			$userInfo[$k]['isHandledViolate'] = $this -> isHandledViolate($v['violateInfo']);

			$balanceMoney = 0;
			if (!empty($v['violateInfo'])) {
				//有没未处理的违章

				foreach ($v['violateInfo'] as $key => $value) {
					//违章录入时间+3天 和 最新订单完成时+7天,对比谁比较大,则取谁的值
					$violateDayLater = $this -> getAfterTime($value['record_time'], $violateDayLimit);
					if ($violateDayLater > $userInfo[$k]['lastFinishTime']) {
						$userInfo[$k]['lastFinishTime'] = $violateDayLater;
					}

					//计算违章的费用 0=新录入,1=正在代办,2=已自行处理,3=已代办处理,4=已结算
					if ($value['process_stat'] == 3) {
						$balanceMoney += $value['violate_cost'] + $value['agency_cost'];
						//违章结算标记
						$userInfo[$k]['violateInfo'][$key]['isHandle'] = 1;
					}
					//自行处理费用
					if ($value['process_stat'] == 2) {
						//$balanceMoney += $value['violate_cost'];
						//违章结算标记
						$userInfo[$k]['violateInfo'][$key]['isHandle'] = 1;
					}
					//判断是否在用户自行处理期间的3天内,是则给用户发短信,否则修改状态为正在代办,并给管理员发内消息
					if ($intNow < $violateDayLater) {
						//发短信通知用户
						//$this -> sendSms($value['order_no'], $value['phone']);
					} else {
						if ($value['process_stat'] == 0) {
							//修改状态为正在代办
							$this -> changeViolateStat($value['id'], 1);
							//给用户发短信，告知代办
							$this -> sendSms($value['order_no'], $value['phone']);
							//给管理员发内消息
							//code

						}
					}
				}
			}

			$userInfo[$k]['balanceMoney'] = $balanceMoney;
			/******************************************************/

			/*分析用**************************/
			$userInfo[$k]['lastFinishDay0'] = date('Y-m-d H:i:s', $v['orderInfo']['finish_time']);
			$userInfo[$k]['lastFinishDay'] = date('Y-m-d H:i:s', $userInfo[$k]['lastFinishTime']);
			/*****************************/

			/********************************预处理完成后,总判断是否释放违章押金*/
			//var_dump(date('Y-m-d', $userInfo[$k]['lastFinishTime']) . '   ' . date('Y-m-d', $intNow) . '  ' . $userInfo[$k]['isHandledViolate']);

			if ($userInfo[$k]['isHandledViolate'] && $userInfo[$k]['lastFinishTime'] < $intNow && !empty($userInfo[$k]['transactionInfo'])) {

				$className = 'eduo';
				$a = AccountManagerContainer::getInstance();
				$a = $a -> getAccountManager($className);
				//循环处理 交易号 数组
				$needPayMoney = $userInfo[$k]['balanceMoney'];

				/*********************/
				$allTransactionInfoMoney = 0;
				/********************/

				for ($i = 0; $i < count($userInfo[$k]['transactionInfo']); $i++) {
					$transactionInfo = $userInfo[$k]['transactionInfo'][$i];
					$tn = $userInfo[$k]['transactionInfo'][$i]['transaction_number'];
					$operateMoney = $userInfo[$k]['transactionInfo'][$i]['money'];

					$allTransactionInfoMoney += $operateMoney;


					if ($needPayMoney > $operateMoney) {

						if ($i == count($userInfo[$k]['transactionInfo']) - 1) {
							$a -> moneyOperate('freezeComplete', '', $tn, $needPayMoney, '冻结完成,现金账户余额不够');

						} else {

							$a -> moneyOperate('freezeComplete', '', $tn, $operateMoney, '冻结完成');
						}

						$needPayMoney -= $operateMoney;
					} else {
						//$type,$accountID,$transaction_number='',$money='',$remark=''
						$a -> moneyOperate('freezeComplete', '', $tn, $needPayMoney, '冻结完成');
						$needPayMoney = 0;
					}
					$transactionInfo['status'] = 1;
					$db -> autoExecute($sns -> table('violate_deposit_log'), $transactionInfo);

				}

				//取消订单使用的变量,传址 'violateDeposit';
				$violateDeposit = $allTransactionInfoMoney-$needPayMoney;
				
				//更改违章状态为已结算
				$this -> changeProcessingStat($userInfo[$k]['violateInfo']);

			}
			/*********************************/
		}

		return $userInfo;
	}

	/**
	 * 获取当前时刻与 指定天数距离前的时刻 的间隔时间(工作日)
	 * @param  int $dayNum [description]
	 * @return int         时间戳 $dayNum天前(24*$dayNum)的时间戳;
	 *
	 */
	function getDayByDistance($dayNum, $intTime) {
		$n = 0;
		while (!$this -> workingDayDistance($intTime - $n * 3600 * 24, $dayNum, $intTime)) {
			$n++;
		}
		return $intTime - $n * 3600 * 24;
	}
	
	

	/**
	 * 判断给定的两个时间是否相差dayNum个工作日(不到一天不算)
	 * @param  int $beforDay 时间戳
	 * @param  int $dayNum   天数
	 * @return bool           指定日期时间戳 大于等于指定天数距离,返回真
	 */
	function workingDayDistance($beforDay, $dayNum, $intNow) {
		global $ecs;
		global $db;
		// $intNow = time();
		$workingDayCount = $this -> workingSecondDistance($beforDay, $intNow) / (24 * 3600);
		return $workingDayCount >= $dayNum;
	}

	/**
	 * 取得指定的两个时间之间包含的工作日的秒数
	 *
	 * @param unknown_type $startTime
	 * @param unknown_type $endTime
	 * @return unknown
	 */
	function workingSecondDistance($startTime, $endTime) {
		global $db;
		global $ecs;
		$strStartDay = date('Y-m-d', $startTime);

		$strEndDay = date('Y-m-d', $endTime);
		// var_dump($strBeforDay);
		$sql = " SELECT holiday
	          FROM " . $ecs -> table('vacation') . " WHERE holiday >= " . "'{$strStartDay}'" . " AND holiday <= " . "'{$strEndDay}'";
		$ret = $db -> getAll($sql);

		$holidayDayCount = count( $ret );
		$holidaySecondCount = $holidayDayCount * 24 * 3600;

		//格式化成array('t1','t2')的结构
		foreach ($ret as $v) {
			$arrHoliday[] = $v['holiday'];
		}

		//判断开始时间有无在节假日中
		$initioSecond = 0;//从$startTime当天的00:00到$startTime所经过的秒数
		$closingSecond = 0;//从$endTime到第二天的00:00所经过的秒数
		if (!empty($arrHoliday)) {
			if (in_array($strStartDay, $arrHoliday)) {
				//开头多算的时间(秒)
				$initioSecond = $startTime - strtotime($strStartDay);
			}

			//判断查询的此刻是否在节假日中
			if (in_array($strEndDay, $arrHoliday)) {
				//结尾多算的时间(秒)
				$closingSecond = strtotime($strEndDay) + 24 * 3600 - $endTime;
			}

		}

		//startTime 的00:00:00,到intNow的24:00:00　的间隔时间戳
		//如果开始时间在节假日中，$holidaySecondCount已经把开始时间到当天00：00的时间包括进去了，所以要再加回去。结束时间同理。
		$distanceSecond = $endTime - $startTime - $holidaySecondCount + $initioSecond + $closingSecond;
		return $distanceSecond;

	}

	function getAfterTime($time, $dayNum) {
		$n = 0;
		while ($this -> workingSecondDistance($time, $time + $n * 3600 * 24) / (24 * 3600) < $dayNum) {
			$n++;
		}
		return $time + $n * 3600 * 24;
	}

	/**
	 * 取得所有用户最晚的订单，截至到七个工作日前。
	 *
	 * @param  [type] $dayNum [description]
	 * @return [type]         [description]
	 */
	function getAllOrderBefor($dayNum, $intNow, $where) {
		global $db;
		global $ecs;
		//取得7个工作日前的时间戳
		$beforDay = $this -> getDayByDistance($dayNum, $intNow);
		//获取7个工作日前,往后的所有订单 订单状态：0:未支付、1:已预定、2:执行中、3:已完成、4:已取消、5:支付超时、6:还车超时
		$sql = " SELECT o.user_id,o.order_id,o.order_end_time,o.real_end_time,o.order_stat,o.car_id" . " FROM " . 
			$ecs -> table('car_order') . ' o ' . " WHERE (o.order_stat = 1 OR o.order_stat = 2 OR o.order_stat=3 OR o.order_stat=4)
	        AND ( o.order_end_time >{$beforDay} OR o.real_end_time >{$beforDay}  ) " . $where;
		$orderRet = $db -> getAll($sql);
		
		//格式化汇总,取得每个用户最新的订单
		$orderInfoTmp = array();
		$orderInfo = array();
		foreach ($orderRet as $k => $v) {
			//保证临时数据每次获取循环数组
			$orderInfoTmp[$v['user_id']] = $v;
			$orderInfoTmp[$v['user_id']]['finish_time'] = $v['real_end_time'];
			if ($v['order_end_time'] > $v['real_end_time']) {
				$orderInfoTmp[$v['user_id']]['finish_time'] = $v['order_end_time'];
			}

			//判断本次和上次循环的结束时间大小
			if (empty($orderInfo[$v['user_id']])) {
				$orderInfo[$v['user_id']] = $orderInfoTmp[$v['user_id']];
			} else {
				if ($orderInfo[$v['user_id']]['finish_time'] < $orderInfoTmp[$v['user_id']]['finish_time']) {
					$orderInfo[$v['user_id']] = $orderInfoTmp[$v['user_id']];
				}
			}
		}
		unset($orderInfoTmp);
		return $orderInfo;
	}

	/**
	 * 获取所有未处理的冻结违章押金
	 * @return [type] [description]
	 */
	function getTransactionInfo($where) {
		global $sns;
		global $db;
		//获取未处理的交易流水号。因为同一个transaction_number有两条记录，一条为已处理，一条为未处理。
		$sql = " SELECT transaction_number,order_id ,user_id,money
	            FROM " . $sns -> table('violate_deposit_log') . " WHERE transaction_number NOT IN ( " . 
	            " SELECT transaction_number FROM " . $sns -> table('violate_deposit_log') . " WHERE status = 1 ) " . $where;
		$ret = $db -> getAll($sql);
		$transactionInfo = array();
		foreach ($ret as $k => $v) {
			$transactionInfo[$v['user_id']][] = $v;
		}
		return $transactionInfo;
	}

	/**
	 * 获取所有未处理的违章记录
	 * @return [type] [description]
	 */
	function getViolateInfo($userID = -1) {
		global $db;
		global $ecs;
		global $sns;
		//获取所有未处理的违章记录,根据userID查询是否有违章
		$where = $userID == -1 ? '' : ' AND uid=' . $userID;
		$sql = " SELECT cv.id,co.user_id,co.order_no,u.phone,cv.record_time,cv.process_stat,cv.order_id,
			cv.violate_cost,cv.agency_cost FROM " . $ecs -> table('car_violate') . ' cv ' . 
			" LEFT JOIN " . $ecs -> table('car_order') . ' co ' . " ON co.order_id=cv.order_id" . 
			" LEFT JOIN " . $sns -> table('user') . ' u ' . " ON u.uid=co.user_id " . 
			" WHERE (cv.process_stat=0 or cv.process_stat=1 or cv.process_stat=2 or cv.process_stat=3)  ";
		$arrViolateRet = $db -> getAll($sql . $where);
		//格式化˙违章信息,userID=>array();
		$violateInfo = array();
		foreach ($arrViolateRet as $k => $v) {
			$violateInfo[$v['user_id']][] = $v;
		}
		return $violateInfo;
	}

	function changeViolateStat($violateID, $stat) {
		global $db;
		global $ecs;
		$db -> autoExecute($ecs -> table('car_violate'), array('process_stat' => $stat), 'update', "id=$violateID");
	}

	/**
	 * 获取后面计算要用到的数据，并格式化好.包括用户的订单，交易好，违章信息。
	 *
	 * @param unknown_type $dayNum
	 * @param unknown_type $intTime
	 * @param unknown_type $userId
	 * @return unknown
	 */
	function getCounterInfo($dayNum, $intTime, $userId = -1) {

		$where = $userId == -1 ? '' : ' AND user_id=' . $userId;

		$orderInfo = $this -> getAllOrderBefor($dayNum, $intTime, $where);
		// echo 'orderInfo:';
		// print_r($orderInfo);
		$transactionInfo = $this -> getTransactionInfo($where);
		// echo 'transactionInfo:';
		// print_r($transactionInfo)

		$violateInfo = $this -> getViolateInfo($userId);
		// echo 'violateInfo:';
		// print_r($violateInfo);
		//格式化数组,汇总有未结算流水号的用户信息,信息包括订单,违章,交易流水号信息
		$userInfo = array();

		foreach ($orderInfo as $k => $v) {
			$userInfo[$k]['orderInfo'] = $v;

			if (empty($transactionInfo[$k])) {
				$userInfo[$k]['transactionInfo'] = array();

			} else {
				$userInfo[$k]['transactionInfo'] = $transactionInfo[$k];
			}

			if (empty($violateInfo[$k])) {
				$userInfo[$k]['violateInfo'] = array();

			} else {
				$userInfo[$k]['violateInfo'] = $violateInfo[$k];
			}

		}
		return $userInfo;
	}

	function sendSms($order_no, $phone) {
		$smsContent = " 你好,你在易多汽车共享租用的车出现违章 订单号为 " . $order_no . "，已超过3天未处理，自动转为客服代办,详情请进入我们的网站查询。";
		// $sms = new SmsSender();
		// $sms->send($smsContent,$phone);
	}

	/**
	 * 检查违章状态,并返回违章状态
	 * @param  array $arrViolateInfo 违章信息数组,可多条
	 * @return [type]                 [description]
	 */
	function isHandledViolate($arrViolateInfo) {
		if (!empty($arrViolateInfo)) {
			foreach ($arrViolateInfo as $key => $value) {
				if ($value['process_stat'] == 0 || $value['process_stat'] == 1) {//0：新录入，1：正在代办
					return false;
				}
			}
		} 
		
		return true;
	}

	function changeProcessingStat($arrViolate) {
		global $db;
		global $ecs;
		$where = " 0 ";
		foreach ($arrViolate as $v) {
			$where .= ' OR id=' . $v['id'];
		}
		$db -> autoExecute($ecs -> table('car_violate'), array('process_stat' => 4), 'update', $where);

	}

	/**
	 * 取得dayCount个工作日之前的时间
	 *
	 * @param unknown_type $dayCount
	 * @param unknown_type $t
	 */
	function getWorkingDayBefore( $dayCount , $t ){
		global  $ecs , $db;
		
		$sDate = date( 'Y-m-d' , $t );
		while( true ){
			$sBeforDay30 = date( 'Y-m-d' , $t - 24 * 3600 );
			$sql = "SELECT holiday FROM " . $ecs->table( "vacation" ) . " WHERE holiday >= $sDate AND holiday <= $sBeforDay30" ;
			$ret = $db->getAll( $sql );
			$arrHoliday = array();
			foreach ( $ret as $row ){
				$arrHoliday[] = $row['holiday'];
			}
			
			$tWorkingDayCount = 0;
			$st = $t;
			while ( $tWorkingDayCount < $dayCount * 24 * 3600 ) {
				$sd = date( 'Y-m-d' , $st );
				if( ! in_array( $sd , $arrHoliday ) ){
					$tWorkingDayCount += 24 * 3600;
				}
				
				$st -= 24 * 3600;
			}
		}
	}
}
