<?php 
header("Content-type: text/html; charset=utf-8");
require_once dirname(__FILE__) . '/../../hfrm/Frm.php';
require_once (Frm::$ROOT_PATH . 'client/conf/inc_error.php');
$time=2;
session_start();
switch ($_SESSION['actionErrorNo']) {
	case '4021':
		//没绑定电话号码
		$url='index.php';
		break;
	case '4060':
		$url='index.php';
		$time=0;
		break;
	case '4215':
		// $url='zhanghu_view.php#order-list-click';

		$msg="<script>alert('".$errorCode[$_SESSION['actionErrorNo']]."');history.go(-1);</script>";
		
		echo $msg;
		
		die();
		break;
	default:
		$url='data_import.php';
		break;
}
echo"<h2>";
echo $errorCode[$_SESSION['actionErrorNo']];
echo"</h2>";

 $str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if ($time != 0)
            $str .= $msg;
unset($_SESSION['actionErrorNo']);
exit($str);
?>
<?php ?>