<?php 
define('IN_ECS', true);
    

require(dirname(__FILE__) . '/includes/init.php');

require_once(ROOT_PATH . 'includes/lib_car_order.php');
require_once(ROOT_PATH . 'includes/lib_goods.php');

require_once (ROOT_PATH . '/../hfrm/Frm.php');
require_once (Frm::$ROOT_PATH . 'order_charge/classes/Settlement.php');

$filter['page'] = empty($_REQUEST['page']) ? '1' : $_REQUEST['page'];
// $filter['page_size'] = '15';
// $filter['record_count'] = '471';
// $filter['page_count'] = '32';

/* act操作项的初始化 */
if (empty($_REQUEST['act'])) {
	$_REQUEST['act'] = 'list';
} else {
	$_REQUEST['act'] = trim($_REQUEST['act']);
}


if ($_REQUEST['act'] == 'list')
{

	$orderInfo=getSettleList();

	 /* 模板赋值 */
	
    $smarty->assign('ur_here', '订单结算');
    $smarty->assign('full_page',        1);
    $smarty->assign('order_list',$orderInfo['order_list']);
    $smarty->assign('filter',$orderInfo['filter']);
    $smarty->assign('record_count', $orderInfo['filter']['record_count']);
    $smarty->assign('page_count',   $orderInfo['filter']['page_count']);
    assign_query_info();
    $smarty->display('settlement_list.htm');

	// print_r(implode(',', $userIdList));
}
elseif ('settle'==$_REQUEST['act']) {
	$s = new Settlement();
	$oderNo=$_REQUEST['order_no'];
	$needMoney=$_REQUEST['money'];
	try
	{
		$errno = 2000;
		$errstr = '';
		$s -> settlement($oderNo,$needMoney);
	}
	catch(Exception $e)
	{
		$errno = $e -> getCode();
		if ($e -> getMessage() == '') {
			require_once (Frm::$ROOT_PATH . 'client/conf/inc_error.php');
			$errstr = $errorCode[$errno];
		} else {
			$errstr = $e -> getMessage();
		}
	}

	echo json_encode(array("errno" => $errno, "errstr" => $errstr, "content" => $ret));
	die();

}
elseif ($_REQUEST['act'] == 'query')
{
    /* 检查权限 */
    // admin_priv('order_view');
    $orderInfo=getSettleList();
    $smarty->assign('order_list',   $orderInfo['order_list']);
    $smarty->assign('filter',       $orderInfo['filter']);
    $smarty->assign('record_count', $orderInfo['filter']['record_count']);
    $smarty->assign('page_count',   $orderInfo['filter']['page_count']);
    make_json_result($smarty->fetch('settlement_list.htm'), '', array('filter' => $orderInfo['filter'], 'page_count' => $orderInfo['filter']['page_count']));
}
elseif ($_REQUEST['act']=='test') {
	die();
	include_once(Frm::$ROOT_PATH . 'PayAPI/UPPop/classes/UnionWebPayManager.php');
	$up=new UnionWebPayManager();
	try{
		$up->onRefund('201304130041569116672',20100);
	}
	catch(Exception $e)
	{
		// print_r($e);
	}
	

}

function getSettleList($serach='')
{
	$db = Frm::getInstance() -> getDb();
	 /* 分页大小 */
	$userId=-1;
	if(!empty($_REQUEST['user_name']))
	{
		$sql= " SELECT uid FROM edo_user WHERE true_name='".$_REQUEST['user_name']."'";
		$userId= $db->getOne($sql);
	}


	$filter['page'] = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);
	if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0)
    {
        $filter['page_size'] = intval($_REQUEST['page_size']);
    }
    else
    {
        $filter['page_size'] = 50;
    }


	$orderLang=array('未支付','已预定','进行中','已完成','已取消','支付超时','换车超时','','锁门','已结算');
	$s = new Settlement();
	$orderList=$s -> settleAll(($filter['page']-1)*$filter['page_size'],$filter['page_size'],$userId);

    /* 记录总数 */

    $filter['record_count']   = $orderList['count'];
    $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;
    //-----------
    unset($orderList['count']);
	$userIdList=array();
	$cityIdList=array();
	$orderNoList=array();

	foreach ($orderList as $k=>$v) {
		$userIdList[]=empty($k) ? 0 : $k;
		foreach ($v as $kk => $vv) {
			$cityIdList[]=empty($kk) ? 0 : $kk;
			foreach ($vv as $vvv) {
				$orderNoList[]=empty($vvv['orderNumber']) ? 0 : $vvv['orderNumber'];
			}
		}
	}

	require_once (Frm::$ROOT_PATH . 'order/classes/Order.php');
	//取消的订单也应该包含在里，因为现在取消是不立即结算的
	//@formatter:off
	$statArr = array(Order::$STATUS_NOT_PAY, Order::$STATUS_ORDERED, Order::$STATUS_STARTED, Order::$STATUS_COMPLETED, Order::$STATUS_REVERT_TIMEOUT, Order::$STATUS_DOOR_CLOSED, Order::$STATUS_CANCELED);
	//@formatter:on
	$sql = "SELECT * FROM edo_cp_car_order WHERE order_stat IN( " . implode(',', $statArr) . " )
		AND modify_stat 
		IN ( " . Order::$MODIFY_STAT_NOT_MODIFY . " , " . Order::$MODIFY_STAT_IN_MODIFY . " ) ".
		" AND order_no IN (".implode(',', $orderNoList).") ".
		" ORDER BY user_id,order_end_time ";
	$orderInfo = $db -> getAll($sql);

	//金额数组
	require_once (Frm::$ROOT_PATH . 'order_charge/classes/OrderCharge.php');
	$statusArr = array(OrderCharge::$STATUS_WITHOUT_PAY, OrderCharge::$STATUS_PAIED);
	$sqlStatus = implode(',', $statusArr);
	$sql = "SELECT order_no,id , amount , item , status FROM order_charge WHERE status IN ( $sqlStatus ) AND order_no in (".implode(',', $orderNoList).") ORDER BY order_no";
	
	$chargeTempInfo = $db -> getAll($sql);

	foreach ($chargeTempInfo as $v) {
		$chargeInfo[$v['order_no']][]=$v;


	}


	//支付类型
	
	require_once( Frm::$ROOT_PATH . 'order_charge/classes/OrderCharge.php' );
	$accounTtypeLang=array('现金账户','手机银联支付','web银联');
	foreach ($chargeInfo as $k => $v) {
		$chargeIdArr=array();
		foreach ($v as $kk => $vv) {
			$chargeIdArr[]=$vv['id'];
		}

		$sqlChargeId = implode(',', $chargeIdArr);
		$sql = "SELECT b.account_type FROM order_balance b 
			WHERE b.id IN 
			( SELECT cb.balance_id FROM charge2balance cb WHERE cb.charge_id IN ( $sqlChargeId ) ) 
			ORDER BY b.id DESC ";
		$db = Frm::getInstance() -> getDb();
		$accounTtype = $db -> getOne($sql);
		$chargeType[$k]=$accounTtypeLang[$accounTtype];
	}
	
	

	//费用合计
	$costCount = 0;
	$paiedCount = 0;
	foreach ($chargeInfo as $k=>$c) {
		foreach ($c as $row) {
			if (OrderCharge::$ITEM_USE_MILE_DEPOSIT == $row['item'] || OrderCharge::$ITEM_USE_TIME_DEPOSIT == $row['item'] || OrderCharge::$ITEM_VIOLATE_DEPOSIT == $row['item']) {
				if (OrderCharge::$STATUS_PAIED == $row['status']) {
					$chargeInfo[$k]['paied_count']+= $row['amount'];
				}
			} else {//除了押金，其他的其实只有两个状态：未支付、已结算，但前面选的时候，没有选择已结算的
				$chargeInfo[$k]['cost_count']+= $row['amount'];
			}
		}
		
	}



	//获取违章信息
	$sql= " SELECT *,o.order_no FROM edo_cp_car_violate v LEFT JOIN edo_cp_car_order o ON o.order_id=v.order_id 
			WHERE o.order_no IN(".implode(',', $orderNoList).")";
	$violateTempInfo=$db->getAll($sql);

	foreach ($violateTempInfo as $v) {
		$violateInfo[$v['order_no']][]=$v;
	}





	$userIdList=array_unique($userIdList);
	$cityIdList=array_unique($cityIdList);

	$userStrList=implode(',', $userIdList);
	$sql= "SELECT uid,true_name FROM edo_user WHERE uid IN($userStrList)";
	foreach ($db->getAll($sql) as $k => $v) {
		$userInfo[$v['uid']]=$v['true_name'];
	}

	foreach ($orderInfo as $k => $v) {
		$orderInfo[$k]['order_start_time']=date('Y-m-d H:m:i',$v['order_start_time']);
		$orderInfo[$k]['order_end_time']=date('Y-m-d H:m:i',$v['order_end_time']);
		$orderInfo[$k]['real_end_time']=$v['real_end_time']==0 ? '无' : date('Y-m-d H:m:i',$v['real_end_time']);
		$orderInfo[$k]['order_stat_lang']=$orderLang[$v['order_stat']];

		$orderInfo[$k]['true_name']=$userInfo[$v['user_id']];
		$orderInfo[$k]['chargeInfo']=$chargeInfo[$v['order_no']];
		$orderInfo[$k]['viloateInfo']=$violateInfo[$v['order_no']];
		$orderInfo[$k]['chargeType']=$chargeType[$v['order_no']];
		
	}
	return array('order_list'=>$orderInfo,'filter'=>$filter);
}

 ?>