
<?php include 'header.html';?>

<?php 
require_once (Frm::$ROOT_PATH . 'client/conf/inc_error.php');
$time=2;
switch ($_SESSION['actionErrorNo']) {
	case '4021':
		//没绑定电话号码
		$url='bangdingshouji.php';
		break;
	case '4060':
		$url='login.php';
		$time=0;
		break;
	case '4215':
		// $url='zhanghu_view.php#order-list-click';

		$msg="<script>alert('".$errorCode[$_SESSION['actionErrorNo']]."');history.go(-1);</script>";
		echo $msg;
		die();
		break;
	default:
		$url='index.php';
		break;
}

echo $errorCode[$_SESSION['actionErrorNo']];


 $str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if ($time != 0)
            $str .= $msg;
// unset($_SESSION['actionErrorNo']);
include 'footer.html';
exit($str);
?>
<?php ?>