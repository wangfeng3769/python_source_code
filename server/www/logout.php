<?php  
require_once (dirname(__FILE__) . '/../hfrm/Frm.php');
require_once (Frm::$ROOT_PATH . 'user/classes/UserManager.php');
$um=new UserManager();
include 'header.html';



echo '注销成功..';

$um->logout();
$time=1;
$url='index.php';

 $str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if ($time != 0)
            $str .= $msg;
include 'footer.html';
        exit($str);
header("location:/");
?>