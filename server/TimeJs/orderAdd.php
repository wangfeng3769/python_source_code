<?PHP
	require_once("orderAlg.php");
	echo "Order add<br>";
	
	$tmStart		= $_POST['ord_timeStart'];
	$tmEnd			= $_POST['ord_timeEnd'];
	$groupDis		= $_POST['group_discount'];
	$classDis		= $_POST['class_discount'];
	$unitPrice	= $_POST['unit_price'];
	$excepCount	= $_POST['excepted_count'];
	$sepTime		= $_POST['separate_time'];
	$arrCap			= array(0 => array("date" => $_POST['one_time'], "price" => $_POST['one_price']),
											1 => array("date" => $_POST['two_time'], "price" => $_POST['two_price']),
											2 => array("date" => $_POST['three_time'], "price" => $_POST['three_price']),
											3 => array("date" => $_POST['four_time'], "price" => $_POST['four_price']),
											4 => array("date" => $_POST['five_time'], "price" => $_POST['five_price']),
											5 => array("date" => $_POST['six_time'], "price" => $_POST['six_price']),
											6 => array("date" => $_POST['seven_time'], "price" => $_POST['seven_price']));
	
	$order = new COrderAlg();
	$dPrice = $order->GenUseCarPrice(strtotime($tmStart), strtotime($tmEnd), $groupDis, $classDis, $unitPrice, $excepCount, $sepTime, $arrCap, true);
	var_dump( $dPrice );
?>
