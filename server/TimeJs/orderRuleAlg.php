<?PHP

class COrderRuleAlg
{
	/*
	PayVioDeposit：下订单时，判断是否需要支付违章押金
	入参：
		$arrOrders：获取当前用户最近一次大订单之后的所有订单信息，内容如下：
			orderNumber:订单号
			orderStart:订单支付时间（2013-02-12 12:20:00）
			returnCar:是否还车（false:未还；true：已还）
			isBigOrder:是否是大订单（false:小订单；true：大订单）
			isCloseNow:是否立即释放（返回值）
	返回值：
		true:需要支付违章押金
		false:无需支付违章押金
	*/
	// public function PayVioDeposit($arrOrders)
	// {
	// 	if (0 == count($arrOrders))	{
	// 		return ture;
	// 	}
		
	// 	$bFindBigOrd = false;
	// 	for ($nCount = 0; $nCount < count($arrOrders); $nCount++)
	// 	{
	// 		// 判断如果是大订单的时候，计算支付时间和当前相比是否超过7天
	// 		if ($arrOrders[$nCount]['isBigOrder'] == true)
	// 		{
	// 			$bFindBigOrd = true;
	// 			$nTimeSpan = abs(time() - strtotime($arrOrders[$nCount]['orderStart']));
	// 			if ($nTimeSpan > 7 * 86400)	{
	// 				return true;
	// 			}
	// 		}
	// 	}
		
	// 	if ($bFindBigOrd)	{
	// 		return false;
	// 	}
		
	// 	return true;
	// }
	

	/**
	 * midify by wu
	**/
	/*
	PayVioDeposit：下订单时，判断是否需要支付违章押金
	入参：
		$arrOrders：获取当前用户最近一次大订单之后的所有订单信息，内容如下：
			orderNumber:订单号
			orderStart:订单支付时间（2013-02-12 12:20:00）
			returnCar:是否还车（false:未还；true：已还）
			isBigOrder:是否是大订单（false:小订单；true：大订单）
			isCloseNow:是否立即释放（返回值）
	返回值：
		true:需要支付违章押金
		false:无需支付违章押金
	*/
	public function PayVioDeposit($arrOrders)
	{
		if (0 == count($arrOrders))	{
			return ture;
		}

		$bFindBigOrd = false;
		$isNeedPay = true;
		$now=time();
		foreach ($arrOrders as $order) {
			// 判断如果是大订单的时候，计算支付时间和当前相比是否超过7天
			$nTimeSpan = abs($now - strtotime($order['orderStart']));
			if ($order['isBigOrder'] == true &&  $nTimeSpan<=7 * 86400)
			{
				$isNeedPay=false;	
			}

		}

		return $isNeedPay;
	}

	/*
	SettleAccount：结算时，判断是否立即释放押金
	入参：
		$arrOrders
			orderNumber:订单号
			orderStart:订单支付时间（2013-02-12 12:20:00）
			isBigOrder:是否是大订单（false:小订单；true：大订单）
			hasArrears:有欠款？
			hasViolate:有违章？
			isCloseNow:是否立即释放（返回值）
	返回值：
			true：正确
			false：失败
	*/
	public function SettleAccount(&$arrOrders)
	{
		$nOrders	= count($arrOrders);
		
		if (0 == $nOrders)	{
			return false;
		}
		
		// 结算超过7天的小订单的狭小订单
		for ($nOrder = 0; $nOrder < $nOrders; $nOrder++)
		{
			if ($nOrder > $nOrders - 1)	{
				break;
			}
			
			if ($arrOrders[$nOrder]['isBigOrder'] == true	||
					$arrOrders[$nOrder]['hasArrears'] == true	||
					$arrOrders[$nOrder]['hasViolate'] == true	)	{
				continue;
			}

			$nTimeSpan = abs(time() - strtotime($arrOrders[$nOrder]['orderStart']));
			if ($nTimeSpan > 7 * 86400)	{
				$arrOrders[$nOrder]['isCloseNow'] = true;
			}
		}
		
		// 有小订单还未结算，则直接返回
		for ($nOrder = 0; $nOrder < $nOrders; $nOrder++)
		{
			if ($arrOrders[$nOrder]['isBigOrder'] == false &&
					$arrOrders[$nOrder]['isCloseNow'] == false)	{
				return true;
			}
		}

		// 结算大订单
		for ($nOrder = 0; $nOrder < $nOrders; $nOrder++)
		{
			$nTimeSpan = abs(time() - strtotime($arrOrders[$nOrder]['orderStart']));
			if ($arrOrders[$nOrder]['isBigOrder'] == true &&
					$nTimeSpan > 7 * 86400)	{
				$arrOrders[$nOrder]['isCloseNow'] = true;
			}
		}
		
		return true;
	}
	
	/*
	CancelOrder：结算时，判断是否立即释放押金
	入参：
		$arrOrders
			orderNumber:订单号
			isClosed:是否已结算
		$arrNowOrders
			orderNumber:订单号
			isBigOrder:是否是大订单（false:小订单；true：大订单）
			
	返回值：
			true：马上释放
			false：等待释放
	*/
	public function CancelOrder($arrOrders, $arrNowOrders)
	{
		if ($arrNowOrders[0]['isBigOrder'] == false)	{
			return true;
		}
		
		for ($nOrder = 0; $nOrder < count($arrOrders); $nOrder++)
		{
			if ($arrOrders[$nOrder]['isClosed'] == false)	{
				return false;
			}
		}
		
		return true;
	}
}

?>