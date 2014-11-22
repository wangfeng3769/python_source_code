<?PHP
	require_one("orderAlg.php");
	echo "Order modify<br>";
	
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
											3 => array("date" => $_POST['four_time'], "price" => $_POST['four_price']));
	
	$dPrice = GenUseCarPrice(strtotime($tmStart), strtotime($tmEnd), $groupDis, $classDis, $unitPrice, $excepCount, $sepTime, $arrCap);
	var_dump( $dPrice );
?>
