<?PHP
	require_once("orderAlg.php");

	echo "Order modify<br>";
	
	$tmStart		= $_POST['ord_timeStart'];
	$tmEnd			= $_POST['ord_timeEnd'];
	$tmStartNew	= $_POST['ord_timeStart_new'];
	$tmEndNew		= $_POST['ord_timeEnd_new'];
	$sepTime		= $_POST['separate_time'];
	$arrPreHour	= array(0 => array("date" => $_POST['one_time'], "price" => $_POST['one_price']),
											1 => array("date" => $_POST['two_time'], "price" => $_POST['two_price']),
											2 => array("date" => $_POST['three_time'], "price" => $_POST['three_price']),
											3 => array("date" => $_POST['four_time'], "price" => $_POST['four_price']),
											4 => array("date" => $_POST['five_time'], "price" => $_POST['five_price']),
											5 => array("date" => $_POST['six_time'], "price" => $_POST['six_price']),
											6 => array("date" => $_POST['seven_time'], "price" => $_POST['seven_price']));

	$order = new COrderAlg();
	$penalty	= $order->ModifyUseCarPrice(strtotime($tmStart), strtotime($tmEnd), strtotime($tmStartNew), strtotime($tmEndNew), $sepTime, $arrPreHour, true);
	echo "<br>费用信息<br>";
	echo "======================================<br>";
	echo "应支付的违约金：".$penalty;
?>
