<?PHP
	require_once("orderAlg.php");

	echo "Order close<br>";
	
	$tmStart		= $_POST['ord_timeStart'];
	$tmEnd			= $_POST['ord_timeEnd'];
	$tmRtnCar		= $_POST['return_car'];
	$kmPrice		= $_POST['unit_price'];
	$km					= $_POST['excepted_count'];
	$sepTime		= $_POST['separate_time'];
	
	// 日封顶价格
	$arrPeriodDay	= array(0 => array("date" => $_POST['one_time_top'], "price" => $_POST['one_price_top']),
												1 => array("date" => $_POST['two_time_top'], "price" => $_POST['two_price_top']),
												2 => array("date" => $_POST['three_time_top'], "price" => $_POST['three_price_top']),
												3 => array("date" => $_POST['four_time_top'], "price" => $_POST['four_price_top']),
												4 => array("date" => $_POST['five_time_top'], "price" => $_POST['five_price_top']),
												5 => array("date" => $_POST['six_time_top'], "price" => $_POST['six_price_top']),
												6 => array("date" => $_POST['seven_time_top'], "price" => $_POST['seven_price_top']));
);

	// 每小时价格
	$arrHours	= array(0 => array("date" => $_POST['one_time_hour'], "price" => $_POST['one_price_hour']),
										1 => array("date" => $_POST['two_time_hour'], "price" => $_POST['two_price_hour']),
										2 => array("date" => $_POST['three_time_hour'], "price" => $_POST['three_price_hour']),
										3 => array("date" => $_POST['four_time_hour'], "price" => $_POST['four_price_hour']),
										4 => array("date" => $_POST['five_time_hour'], "price" => $_POST['five_price_hour']),
										5 => array("date" => $_POST['six_time_hour'], "price" => $_POST['six_price_hour']),
										6 => array("date" => $_POST['seven_time_hour'], "price" => $_POST['seven_price_hour']));

	// 包时封顶价格
	$arrPeriodHour	= array(0 => array("date" => $_POST['one_time_period'], "timeS" => $_POST['one_time_start'], "timeE" => $_POST['one_time_end'], "price" => $_POST['one_price_period']),
													1 => array("date" => $_POST['two_time_period'], "timeS" => $_POST['two_time_start'], "timeE" => $_POST['two_time_end'], "price" => $_POST['two_price_period']),
													2 => array("date" => $_POST['three_time_period'], "timeS" => $_POST['three_time_start'], "timeE" => $_POST['three_time_end'], "price" => $_POST['three_price_period']),
													3 => array("date" => $_POST['four_time_period'], "timeS" => $_POST['four_time_start'], "timeE" => $_POST['four_time_end'], "price" => $_POST['four_price_period']),
													4 => array("date" => $_POST['five_time_period'], "timeS" => $_POST['five_time_start'], "timeE" => $_POST['five_time_end'], "price" => $_POST['five_price_period']),
													5 => array("date" => $_POST['six_time_period'], "timeS" => $_POST['six_time_start'], "timeE" => $_POST['six_time_end'], "price" => $_POST['six_price_period']),
													6 => array("date" => $_POST['seven_time_period'], "timeS" => $_POST['seven_time_start'], "timeE" => $_POST['seven_time_end'], "price" => $_POST['seven_price_period']));
													);

	$order = new COrderAlg();
	$closePrice = $order->CloseOrder(strtotime($tmStart), strtotime($tmEnd), strtotime($tmRtnCar), $km, $kmPrice, $sepTime, $arrHours, $arrPeriodHour, $arrPeriodDay, true);
//	var_dump( $closePrice );
	
	echo "费用信息<br>";
	echo "======================================<br>";
	echo "租车费用：". $closePrice['UseCar']. "<br>";
	echo "超时费用：". $closePrice['Penalty']. "<br>";
	echo "行车费用：". $closePrice['kmPrice']. "<br>";
?>