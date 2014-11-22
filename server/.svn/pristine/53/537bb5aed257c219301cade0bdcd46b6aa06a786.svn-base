<?PHP

class COrderAlg
{
	/*
	函数GenUseCarPrice：计算订车押金，不包含违章押金
	参数：
	$tmStart：订单开始时间（UNIX时间戳）
	$tmEnd：订单结束时间（UNIX时间戳）
	$groupDis：群组折扣百分比，例如：80=8折
	$classDis：类别折扣百分比，同上
	$kmPrice：每公里用车费用
	$km：预估每天的公里数
	$sepTime：计费时间分隔点，例如：12:00，字符串形式
	$arrCap：订单所包含天数的每天的封顶价格，封顶价格获取时，要比实际用车天数多一天，也就是N+1天
					 此参数为数组，详见样例。
	*/
	// 
	public function GenUseCarPrice($tmStart, $tmEnd, $groupDis, $classDis, $kmPrice, $km, $sepTime, $arrCap, $debug=false)
	{
		$nDayS = 0;	// 开始计费的日期，从数组第几位开始
		$nDayE = 0;	// 结束计费的日期，从数组第几位结束
		$tmS	 = $tmStart;
		
		$sepDtS = strtotime(date('Y-m-d', $tmStart). " ". $sepTime);
//		$sepDtE = strtotime(date('Y-m-d', $tmEnd). " ". $sepTime);
		
		for ($nDayS = 0; $nDayS < count($arrCap); ++$nDayS)
		{
			if (date('Y-m-d', $tmS) == $arrCap[$nDayS]['date']) {
				break;
			}
		}
		
		if (0 <= $tmS - $sepDtS)	{
			$nDayS++;
		}

		$nDayPrice = 0;
		$nCarPrice = 0;
		
		do
		{
			$nDayPrice += $arrCap[$nDayS]['price'];
			$nCarPrice += $kmPrice * $km;
			
			$nDayS++;
			$tmS += 86400;
		}while($tmS < $tmEnd);

		$TotalGroup = $nDayPrice * $groupDis / 100;
		$TotalClass = $nDayPrice * $classDis / 100;

		if ($debug)
		{
			echo "起始时间=".date('Y-m-d H:i', $tmStart)."<br>";
			echo "结束时间=".date('Y-m-d H:i', $tmEnd)."<br>";
			echo "正常价格=".$nDayPrice."<br>";
			echo "群组价格=".$TotalGroup."<br>";
			echo "分类价格=".$TotalClass."<br>";
		}
		
		return array( "DayPrice" => min($nDayPrice, min($TotalGroup, $TotalClass)),
									"CarPrice" => $nCarPrice);
	}
	
	public function ModifyUseCarPrice($tmStart, $tmEnd, $tmStartNew, $tmEndNew, $sepTime, $arrPreHour, $debug=false)
	{
		if (count($arrPreHour) === 0) {
			return -2;
		}
		
		if ($debug)
		{
			echo "<br>调试信息<br>";
			echo "======================================<br>";
		}
		
		// 提前24小时修改，或者新订单时间涵盖了老订单，则不支付违约金
		if (24 < ($tmStart - time()) / 3600 || 
			 ($tmStart >= $tmStartNew && $tmEnd <= $tmEndNew) ) {
			return 0;
		}
		
		// 提前或提前并延长订单，则不收取费用
		if ($tmStartNew < $tmStart && ($tmEndNew - $tmStartNew) >= ($tmEnd - $tmStart))
		{
			if ($debug)
			{
				echo "订单整体提前（或者提前并延长）时间，不收取违约金。<br>";
			}
			return 0;
		}
		
		if ($tmStart < $tmStartNew && $tmEnd > $tmEndNew)
		{
			// 老订单完全包含新订单，未包含部分分为两部分，则分成2个订单进行计算
			if ($tmStartNew - $tmStart + $tmEnd - $tmEndNew >= 28800)
			{
				// 大于8小时，则取前面的价格*4小时
				return $this->ModifyUseCarPrice($tmStart, $tmEnd, $tmStart + 28800, $tmEnd + 28800, $sepTime, $arrPreHour, $debug);;
			}
			$Price1 = $this->ModifyUseCarPrice($tmStart, $tmEndNew, $tmStartNew, $tmEndNew, $sepTime, $arrPreHour, $debug);
			$Price2 = $this->ModifyUseCarPrice($tmStartNew, $tmEnd, $tmStartNew, $tmEndNew, $sepTime, $arrPreHour, $debug);
			return $Price1 + $Price2;
		}
		
		$OverTimeS = 0;	// 超出订单时间段的开始时间
		$OverTimeE = 0;	// 超出订单时间段的结束时间
		
		if ($tmStart < $tmStartNew && $tmEnd > $tmStartNew) {
			// 新订单起始时间在老订单内
			$OverTimeS = $tmStart;
			$OverTimeE = $tmStartNew;
		}
		else if ($tmStart < $tmEndNew && $tmEnd > $tmEndNew)
		{
			// 新订单结束时间在老订单内
			$OverTimeS = $tmEndNew;
			$OverTimeE = $tmEnd;
		}
		else if ($tmStart >= $tmEndNew || $tmEnd <= $tmStartNew)
		{
			// 新订单时间没有和老订单重叠
			$OverTimeS = $tmStart;
			$OverTimeE = $tmEnd;
		}
		else
			return -1;
			
		if (24 < ($OverTimeS - time()) / 3600)	{
			// 未重叠部分的起始时间在24小时后，则不收取违约金
			return 0;
		}
		if (24 < ($OverTimeE - time()) / 3600)	{
			// 为重叠部分结束时间在24小时后，则只收取24小时内的违约金
			$OverTimeE = time() + 86400;
		}
		
		$b4Hours = false;
		if (4 < ($tmEnd - $tmStart) / 3600) {
			$b4Hours = true;
		}
		
		if ($debug)
		{
			echo "未重叠部分起始时间：". date('Y-m-d H:i:s', $OverTimeS). "<br>";
			echo "未重叠部分结束时间：". date('Y-m-d H:i:s', $OverTimeE). "<br>";
		}
		
		// 找到订单违约计费初始时间
		for ($nCount = 0; $nCount < count($arrPreHour); ++$nCount)
		{
			$tmOrdHourS = strtotime(date('Y-m-d', $OverTimeS). " 0:0:0");
			$tmPreHourS = strtotime($arrPreHour[$nCount]['date']. " 0:0:0");
			if ($tmOrdHourS == $tmPreHourS) {
				break;
			}
		}

		$penalty 			= 0;
		$penalty4Hour = 0;
		$penaltyTimes	= 0;
				
		do
		{
			if ($OverTimeS < strtotime($arrPreHour[$nCount]['date']. " ". $sepTime))
			{
				$penalty += $arrPreHour[$nCount]['price'];
			}
			else
			{
				$nCount++;
				$penalty += $arrPreHour[$nCount]['price'];
			}
			
			if ($b4Hours && 4 > $penaltyTimes)		{
				$penalty4Hour += $arrPreHour[$nCount]['price'];
			}
			
			$OverTimeS += 3600;
			$penaltyTimes++;
		}while ($OverTimeS < $OverTimeE);

		if ($debug)
		{
			echo "订单违约金：". ($penalty / 2). "<br>";
			echo "4小时用车费用：". $penalty4Hour. "（为重叠部分不足4小时的，按当前未重叠时间计算）<br>";
		}
		
		if ($b4Hours)
			return min(($penalty / 2), $penalty4Hour);
			
		return $penalty / 2;
	}

	public function CancelOrder($tmStart, $tmEnd, $sepTime, $arrPreHour, $debug)
	{
		return self::ModifyUseCarPrice($tmStart, $tmEnd, $tmEnd + 1, $tmEnd * 2 - $tmStart + 1, $sepTime, $arrPreHour, $debug);
	}
	
	/**
	 *
	 * @example orderClose.php
	 * @param int $tmStart
	 * @param int $tmEnd
	 * @param int $tmReturn
	 * @param int $km 行驶里程(单位千米)
	 * @param int $kmPrice 没千米的价格(单位千米分)
	 * @param int $sepTime 计费时间分割点(格式12:00)
	 * @param array $arrHours 基本时租金，从起始日之前一天到结束日之后一天，每天的价格都列出来。
	 * @param array $arrPeriodHour 特殊时段封顶价
	 * @param array $arrPeriodDay 日封顶价
	 * @param boolean $debug
	 * @return array
	 */
	public function CloseOrder($tmStart, $tmEnd, $tmReturn, $km, $kmPrice, $sepTime, $arrHours, $arrPeriodHour, $arrPeriodDay, $debug=false)
	{
		$nNonDisPrice = $this->CountNonDiscount($tmStart, $tmEnd, $sepTime, $arrHours, $debug);
		$nHourDisPrice= $this->CountHourDiscount($tmStart, $tmEnd, $sepTime, $arrHours, $arrPeriodHour);
		$nDayDisPrice	= $this->CountDayDiscount($tmStart, $tmEnd, $sepTime, $arrPeriodDay, $arrHours, true);
		
		if ($nHourDisPrice > 0)	{
			$basePrice = min($nNonDisPrice, min($nHourDisPrice, $nDayDisPrice));
		}
		else
		{
			$basePrice = min($nNonDisPrice, $nDayDisPrice);
		}

		if ($debug)
		{
			echo "<br>调试信息<br>";
			echo "==========================================<br>";
			echo "无优惠费用：". $nNonDisPrice. "<br>";
			echo "时段封顶费用：". $nHourDisPrice. "<br>";
			echo "日封顶费用：". $nDayDisPrice. "<br>";
			echo "<br>";
		}

		$penalty	= 0;
		
		// 罚金的计算
		if ($tmReturn > $tmEnd)
		{
			$tmE		= $tmEnd;
			$nCount	= 0;
			
			for ($nCount = 0; $nCount < count($arrHours); ++$nCount)
			{
				if (date('Y-m-d', $tmE) == $arrHours[$nCount]['date']) {
					break;
				}
			}
			
			do
			{
				if ($tmE < strtotime($arrHours[$nCount]['date']. " ". $sepTime))
				{
					$penalty += $arrHours[$nCount]['price'] / 2;
				}
				else
				{
					$nCount++;
					continue;
				}
				
				$tmE += 900;
			}while ($tmE < $tmReturn);
		}
		
		return array("UseCar" => $basePrice, "Penalty" => $penalty, "kmPrice" => $km * $kmPrice);
	}
	
	// 计算无优惠状态的价格
	protected function CountNonDiscount($tmStart, $tmEnd, $sepTime, $arrHours, $debug=false)
	{
		$price = 0;
		$nCount= 0;
		$tmS	 = $tmStart;
		
		for ($nCount = 0; $nCount < count($arrHours); ++$nCount)
		{
			if (date('Y-m-d', $tmS) == $arrHours[$nCount]['date']) {
				break;
			}
		}

		do
		{
			if ($tmS < strtotime($arrHours[$nCount]['date']. " ". $sepTime))
			{
				$price += $arrHours[$nCount]['price'];
			}
			else
			{
				$nCount++;
				$price += $arrHours[$nCount]['price'];
			}
			
			$tmS += 3600;
		}while($tmS < $tmEnd);
		
		return $price;
	}
	
	// 计算优惠时段价格
	protected function CountHourDiscount($tmStart, $tmEnd, $sepTime, $arrHours, $arrPeriodHour)
	{
		$price = 0;
		$nCountPeriod = 0;
		$nCountHour		= 0;
		$tmS	 = $tmStart;
		
		if (count($arrPeriodHour) == 0)	{
			return 0;
		}
		
		for ($nCountPeriod = 0; $nCountPeriod < count($arrPeriodHour); ++$nCountPeriod)
		{
			if (date('Y-m-d', $tmS) == $arrPeriodHour[$nCountPeriod]['date']) {
				break;
			}
		}
		for ($nCountHour = 0; $nCountHour < count($arrHours); ++$nCountHour)
		{
			if (date('Y-m-d', $tmS) == $arrHours[$nCountHour]['date']) {
				break;
			}
		}
		
//		echo "nCountPeriod=". $nCountPeriod. "<br>";
//		echo "nCountHour=". $nCountHour. "<br>";

		do
		{
			$periodTmS = $arrPeriodHour[$nCountPeriod]['date']. " ". $arrPeriodHour[$nCountPeriod]['timeS'];
			$periodTmE = $arrPeriodHour[$nCountPeriod]['date']. " ". $arrPeriodHour[$nCountPeriod]['timeE'];

			if ($tmS > strtotime($periodTmE)) {
				$nCountPeriod++;
			}
			
//			echo "包时起始时间：". $periodTmS. " 包时结束时间：". $periodTmE. "<br>";
//			echo "当前计算时间：". date('Y-m-d H:i:s', $tmS). "<br>";
			if ($tmS >= strtotime($periodTmS) && $tmS <= strtotime($periodTmE))
			{
				// 当前时间在包时时间段内
				$price += $arrPeriodHour[$nCountPeriod]['price'];
				while($tmS < strtotime($periodTmE))
				{
					$tmS += 3600;
				}
				$nCountPeriod++;
				continue;
			}
			else
			{
				if ($tmS < strtotime($arrHours[$nCountHour]['date']. " ". $sepTime))
				{
					$price += $arrHours[$nCountHour]['price'];
				}
				else
				{
					$nCountHour++;
					$price += $arrHours[$nCountHour]['price'];
				}
			}
			
			$tmS += 3600;
		}while($tmS < $tmEnd);
		
		return $price;
	}
	
	protected function CountDayDiscount($tmStart, $tmEnd, $sepTime, $arrPeriodDay, $arrHours, $debug)
	{
		$sepDtS = strtotime(date('Y-m-d', $tmStart). " ". $sepTime);
		$nDayS  = 0;	// 开始计费的日期，从数组第几位开始
		$tmS		= $tmStart;
		for ($nDayS = 0; $nDayS < count($arrPeriodDay); ++$nDayS)
		{
			if (date('Y-m-d', $tmStart) == $arrPeriodDay[$nDayS]['date']) {
				break;
			}
		}

		if (0 <= $tmStart - $sepDtS)
		{
			// 起始时间大于计费时间分隔点，按照第二天的费用计算
			$nDayS++;
		}
		
		$nDayPrice 			= 0;
		$nHourPrice			= 0;
		$nLastDayPrice 	= 0;
		$bOverSepTime		= false;
		
		// 计算日封顶价格
		do
		{
			if ($tmS + 86400 <= $tmEnd)	{
				$nDayPrice += $arrPeriodDay[$nDayS]['price'];
				$tmS += 86400;
				$nDayS++;
				continue;
			}
			else
			{
				if ($tmS >= $tmEnd)	{
					break;
				}
				
				if (0 == $nLastDayPrice)	{
					$nLastDayPrice = $arrPeriodDay[$nDayS + 1]['price'];
				}

				// 计算不足一天的小时费用
				$sepDtS = strtotime(date('Y-m-d', $tmS). " ". $sepTime);
				if (0 <= $tmS - $sepDtS && false == $bOverSepTime)	{
					$nDayS++;
					$bOverSepTime	= true;
				}
				else
				{
					$bOverSepTime	= false;
				}

				$nHourPrice += $arrHours[$nDayS]['price'];
				$tmS += 3600;
				
				if ($debug)
				{
					echo date('Y-m-d H:i', $tmS). "  ";
					echo $arrHours[$nDayS]['price']. "<br>";
				}
			}
			
		}while(true);
		
		if ($debug)
		{
			echo "剩余不足一天的小时金额：". $nHourPrice. "<br>";
		}
		
		if ($nLastDayPrice < $nHourPrice)	{
			$nDayPrice += $nLastDayPrice;
		}
		else	{
			$nDayPrice += $nHourPrice;
		}
		
		return $nDayPrice;
	}
}
?>