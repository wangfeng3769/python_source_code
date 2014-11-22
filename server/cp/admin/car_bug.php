<?php 
define('IN_ECS', true);

require (dirname(__FILE__) . '/../includes/init.php');
/* 创建 Smarty 对象。*/
require(ROOT_PATH . 'includes/cls_template.php');
require(ROOT_PATH . '/../client/classes/CarAgent.php');
require_once(ROOT_PATH . 'admin/includes/lib_main.php');
require_once(ROOT_PATH . 'includes/lib_common.php');
require_once(ROOT_PATH . 'includes/lib_base.php');


$smarty = new cls_template;

$smarty->template_dir  = ROOT_PATH . ADMIN_PATH . '/templates';
$smarty->compile_dir   = ROOT_PATH . 'temp/compiled/admin';
if ((DEBUG_MODE & 2) == 2)
{
    $smarty->force_compile = true;
}


$smarty->assign('lang', $_LANG);
$smarty->assign('help_open', $_CFG['help_open']);



/* act操作项的初始化 */
if (empty($_REQUEST['act'])) {
	$_REQUEST['act'] = 'list';
} else {
	$_REQUEST['act'] = trim($_REQUEST['act']);
}

if ($_REQUEST['act']=='list') {
	$noCloseCar=getNoBackCar();
	
	$smarty -> assign('ur_here', '未还车车辆列表');
	$smarty -> assign('full_page', 1);
	$smarty -> assign('car_info', $noCloseCar);

	/* 列表页面 */
	$smarty -> display('car_bug_list.htm');
}
elseif($_REQUEST['act']=='fix_close')
{
	/*添加链接*/
	$link[0]['text'] = '返回列表';
	$link[0]['href'] = 'car_bug.php';
	$carId=$_REQUEST['car_id'];
	if (empty($carId)) {
		sys_msg('车辆信息错误', 0, $link);
	}
	$ca=new CarAgent();
	$ca->closeDoor(array('car_id'=>$carId));
	

	sys_msg('发送关门指令完毕', 0, $link);
}
elseif($_REQUEST['act']=='change_close')
{
	$orderId=$_REQUEST['order_id'];
	/*添加链接*/
	$link[0]['text'] = '返回列表';
	$link[0]['href'] = 'car_bug.php';

	if (empty($orderId)) {
		sys_msg('订单信息错误', 0, $link);
	}
	$sql = " UPDATE edo_cp_car_order o SET o.order_stat=8 WHERE o.order_id=$orderId";
	$db->query($sql);

	sys_msg('修改订单状态为已锁门', 0, $link);
}
function getNoBackCar()
{	
	global $db;
	$now=time()-10*60;
	$sql= " SELECT o.order_id,o.order_no,o.car_id,c.name,c.number,c.icon,u.true_name,u.phone,o.order_end_time,o.real_end_time,o.order_stat
			FROM edo_cp_car_order o 
			LEFT JOIN edo_cp_car c ON c.id=o.car_id 
			LEFT JOIN edo_user u ON u.uid=o.user_id 
			WHERE (o.order_stat=6 OR o.order_stat=2) AND o.order_end_time<$now AND modify_stat=0 AND o.order_id IN
				( 	SELECT DISTINCT o.order_id 
					FROM edo_cp_car_order o LEFT JOIN edo_cp_car_cmd_record cr ON o.car_id=cr.car_id 
					WHERE ( o.order_end_time<$now AND cr.addon ='".'{"pt":"rctrl","spt":"cld"}'."')
				)
			 " ;

  	$ret=$db->getAll($sql);
  	foreach ($ret as $k => $v) {
  		$ret[$k]['order_end_time']=empty($v['order_end_time']) ? '无数据' : date('Y-m-d H:i:s',$v['order_end_time']);
  		$ret[$k]['real_end_time']=empty($v['real_end_time']) ? '无数据' : date('Y-m-d H:i:s',$v['real_end_time']);
  		switch ($v['order_stat']) 
        {
             case 0:
                 $ret[$k]['order_stat'] = '未支付';
                 break;
             case 1:
                 $ret[$k]['order_stat'] = '已预订';
                 break;
             case 2:
                 $ret[$k]['order_stat'] = '执行中';
                 break;
             case 3:
                 $ret[$k]['order_stat'] = '已完成';
                 break;
             case 4:
                 $ret[$k]['order_stat'] = '已取消';
                 break;
             case 5:
                 $ret[$k]['order_stat'] = '支付超时';
                 break;
             case 6:
                 $ret[$k]['order_stat'] = '还车超时';
                 break;
             case 8:
                 $ret[$k]['order_stat'] = '已锁门';
                 break;
             case 9:
                 $ret[$k]['order_stat'] = '已结算';
                 break;    
        }

  	}
  	return $ret;
}

 ?>