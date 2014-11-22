<?php 

require_once (dirname(__FILE__) . '/../../../hfrm/Frm.php');
require_once (Frm::$ROOT_PATH . 'PayAPI/UPPop/classes/UnionWebPayManager.php');
//orderNo处理方式未定
if ($_REQUEST['q']=='back_notify') {
	$uwp=new UnionWebPayManager();
	$uwp->backNotify($_REQUEST);

}
elseif ($_REQUEST['q']=='front_notify') {
	$uwp=new UnionWebPayManager();
	// unset($_REQUEST['q']);
	$uwp->frontNotify($_REQUEST);
}
elseif ($_REQUEST['q']=='query') {
	$uwp=new UnionWebPayManager();
	$uwp->query($_REQUEST);
}
elseif ($_REQUEST['q']=='refund') {
	
}




 ?>