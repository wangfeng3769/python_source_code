<?php

define('IN_ECS', true);

require (dirname(__FILE__) . '/includes/init.php');
$exc = new exchange($ecs -> table("car"), $db, 'id', '');
include_once (ROOT_PATH . 'includes/cls_image.php');
include_once (ROOT_PATH . '../client/classes/CarManager.php');
$image = new cls_image($_CFG['bgcolor']);

/* act操作项的初始化 */
if (empty($_REQUEST['act'])) {
	$_REQUEST['act'] = 'list';
} else {
	$_REQUEST['act'] = trim($_REQUEST['act']);
}

/*------------------------------------------------------ */
//-- 商品分类列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list') {
	/* 获取分类列表 */
	$car_list = car_list(0, 0, false);
	//print_r($car_list);
	/* 模板赋值 */
	$smarty -> assign('ur_here', $_LANG['01_car_list']);
	$smarty -> assign('action_link', array('href' => 'car.php?act=add', 'text' => $_LANG['04_category_add']));
	$smarty -> assign('full_page', 1);

	$smarty -> assign('car_info', $car_list['car']);
	$smarty->assign('filter',       $car_list['filter']);
	$smarty->assign('record_count', $car_list['record_count']);
    $smarty->assign('page_count',   $car_list['page_count']);
	/* 列表页面 */
	assign_query_info();
	$smarty -> display('car_list.htm');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query') {
	$car_list = car_list();
	$smarty -> assign('car_info', $car_list['car']);
	make_json_result($smarty -> fetch('car_list.htm'));
}
/*------------------------------------------------------ */
//-- 添加商品分类
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'add') {
	/* 权限检查 */
	admin_priv('car_manage');

	/* 模板赋值 */
	$arrgroups = get_groups();
	$smarty -> assign('arrgroups', $arrgroups);
	$smarty -> assign('ur_here', $_LANG['01_car_add']);
	$smarty -> assign('action_link', array('href' => 'car.php?act=list', 'text' => $_LANG['01_car_list']));

	//获得站点列表
	$station = getStation();

	$equipment = get_equipment();

	$smarty -> assign('equipment', $equipment);
	$smarty -> assign('station', $station);
	$smarty -> assign('car_select', car_list(0, 0, true));
	$smarty -> assign('form_act', 'insert');
	$smarty -> assign('car_info', array('is_show' => 1));

	/* 显示页面 */
	assign_query_info();
	$smarty -> display('car_info.htm');
}

/*------------------------------------------------------ */
//-- 商品分类添加时的处理
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'insert') {
	/* 权限检查 */
	admin_priv('car_manage');
	/* 初始化变量 */
	/* 入库的操作 */
	/*
	 判断是否有非法操作
	 */
	$sql = "INSERT INTO " . $ecs -> table('car') . " 
					set name='$POST[name]',
					number='$POST[number]',
					model='$POST[model]',
					gearbox='$POST[gearbox]',
					color='$POST[color]',
					`group`='$POST[group_id]',
                    `brand`='$POST[brand]',
					station='$POST[station]',
					equipment='$POST[equipment]',
					sort_order='$POST[sort_order]'";

	if ($db -> query($sql) !== false) {
		$car_id = $db -> insert_id();
		if ($car_id) {
			$db -> autoExecute('car_device', array('car_id' => $car_id), 'update', ' id=' . $POST['equipment']);
		}
		admin_log($_POST['name'], 'add', 'car');
		// 记录管理员操作
		clear_cache_files();
		// 清除缓存

		bindCar($POST[equipment], $car_id);

		/*添加链接*/
		$link[0]['text'] = $_LANG['continue_add'];
		$link[0]['href'] = 'car.php?act=add';

		$link[1]['text'] = $_LANG['back_list'];
		$link[1]['href'] = 'car.php?act=list';
		sys_msg($_LANG['catadd_succed'], 0, $link);
	}
}

/*------------------------------------------------------ */
//-- 编辑商品分类信息
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'edit') {
	admin_priv('cat_manage');
	// 权限检查

	$id = $_GET['id'];
	//分类是否存在首页推荐
	$res = $db -> getRow("SELECT * FROM " . $ecs -> table("car") . " WHERE id=" . $id);
	if (!empty($res)) {
		$cat_recommend = array();
	}
	$arrgroups = get_groups();
	$smarty -> assign('action_link', array('href' => 'car.php?act=list', 'text' => $_LANG['01_car_list']));
	$smarty -> assign('arrgroups', $arrgroups);
	$smarty -> assign('car', $res);
	$smarty -> assign('form_act', 'update');

	//获得站点列表
	$station = getStation();
	$smarty -> assign('station', $station);

	//获得设备信息
	$equipment = get_equipment();
	$smarty -> assign('equipment', $equipment);
	/* 显示页面 */
	assign_query_info();
	$smarty -> display('car_info.htm');
}

/*------------------------------------------------------ */
//-- 编辑商品分类信息
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'update') {
	/* 权限检查 */
	admin_priv('cat_manage');

	$data = $_POST;

	/*更新被使用的设备状态*/
	if ($_POST['equipment'] > 0) {
		$db -> autoExecute('car_device', array('status' => 1), "updata", " id = '$_POST[equipment]'");
		$newEquipment = $_POST['equipment'];
	} else {
		$newEquipment = '';
	}

	//如果old equipment大于0,释放equipment
	$sql = " SELECT equipment FROM " . $ecs -> table('car') . " WHERE id=" . $_POST['id'];
	$oldEquipment = $db -> getOne($sql);
	if ($oldEquipment > 0 && $newEquipment != $oldEquipment) {
		$db -> autoExecute('car_device', array('status' => 0), "updata", " id= $oldEquipment");
	}

	$data['equipment'] = $newEquipment;

	/* 初始化变量 */
	$id = !empty($_POST['id']) ? intval($_POST['id']) : 0;
	if ((isset($_FILES['link_img']['error']) && $_FILES['link_img']['error'] == 0) || (!isset($_FILES['link_img']['error']) && isset($_FILES['icon']['tmp_name']) && !empty($_FILES['icon']['tmp_name']))) {
		$img_up_info = @basename($image -> upload_image($_FILES['icon'], 'car'));
		$data['icon'] = DATA_DIR . '/car/' . $img_up_info;
		$sql = " SELECT icon FROM " . $ecs -> table('car') . " WHERE id=" . $id;
		$data['old_icon'] = $db -> getOne($sql);
	} else {
		$data['icon'] = $_POST['old_icon'];
	}

	if ($db -> autoExecute($ecs -> table('car'), $data, 'UPDATE', "id='$id'")) {

		if ($_POST['old_icon'] != $data['icon']) {

			unlink(ROOT_PATH . $data['old_icon']);
		}

		bindCar($POST['equipment'], $id);


		admin_log($_POST['id'], 'edit', 'car');
		// 记录管理员操作
		/* 提示信息 */
		$link[] = array('text' => $_LANG['back_list'], 'href' => 'car.php?act=list');
		sys_msg($_LANG['catedit_succed'], 0, $link);
	}
}

function bindCar($deviceId, $carId) {
	global $db;
	$sql = "UPDATE car_device SET car_id = $carId WHERE id = $deviceId";
	$db -> query($sql);
}

//里程因子
if ($_REQUEST['act'] == 'gene') {

	$id = $_GET['id'];
	$res = $db -> getAll("SELECT  *  FROM " . $ecs -> table("car_order") . " WHERE car_id=" . $id . " ORDER  BY  real_end_time  DESC  limit  3");
	for ($i = 0; $i < count($res); $i++) {
		$res[$i][tid] = $i + 1;
	}
	/* 输出旧的里程因子 */
	$sql = "SELECT   mile_factor  FROM " . $ecs -> table("car") . " WHERE id = " . $id;
	$mile_factor = $db -> getOne($sql);
	/* 模板赋值 */
	$smarty -> assign('ur_here', '里程因子');
	$smarty -> assign('action_link', array('href' => 'car.php?act=list', 'text' => $_LANG['back_list']));
	$smarty -> assign('full_page', 1);

	$smarty -> assign('id', $id);
	$smarty -> assign('car_order_info', $res);

	//添加数据库
	$gene = $_POST['gene'];
	if ($gene != "") {

		$sql = " UPDATE " . $ecs -> table("car") . "  SET  mile_factor  =  " . $gene . "  WHERE  id =  " . $id;

		$row = $db -> query($sql);

		if ($row > 0) {

			$link[] = array('text' => $_LANG['back_list'], 'href' => 'car.php?act=list');
			sys_msg('保存成功！', 0, $link);
		} else {
			sys_msg('保存失败');
		}

	}
	$smarty -> assign('mile_factor', $mile_factor);
	$smarty -> display('car_mileage_gene.htm');
}
if ($_REQUEST['act'] == 'init') {
	$carId = $_GET['id'];
	$car_init_info = initial_info($carId);

	$smarty -> assign('ur_here', '初始化信息设置');
	$smarty -> assign('action_link', array('href' => 'car.php?act=list', 'text' => $_LANG['01_car_list']));
	$smarty -> assign('car_id', $carId);
	$smarty -> assign('car', $car_init_info);
	$smarty -> assign('is_ajax', 1);
	$smarty -> assign('form_action', 'init_update');
	$smarty -> display('car_init_info.html');

}

if ($_REQUEST['act'] == 'init_update') {
	$carId = $_POST['id'];
	$car_init_info = initial_info($carId);

	$data = $_POST;
	$data['car_id'] = $carId;
	if (empty($car_init_info)) {
		//插入

		$db -> autoExecute($ecs -> table('car_init_info'), $data);
	} else {
		//更新
		$db -> autoExecute($ecs -> table('car_init_info'), $data, 'update', "car_id={$carId}");
	}
	/* 提示信息 */
	$links[] = array('href' => 'car.php?act=list', 'text' => '返回车辆管理列表');
	;
	sys_msg("编辑车辆初始信息成功", 0, $links);
	// $link[] = array('text' => $_LANG['back_list'], 'href' => 'car.php?act=list');
	//    sys_msg($_LANG['catedit_succed'], 0, $link);
}
/*------------------------------------------------------ */
//-- 批量转移商品分类页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'move') {
	/* 权限检查 */
	admin_priv('cat_drop');

	$cat_id = !empty($_REQUEST['cat_id']) ? intval($_REQUEST['cat_id']) : 0;

	/* 模板赋值 */
	$smarty -> assign('ur_here', $_LANG['move_goods']);
	$smarty -> assign('action_link', array('href' => 'car.php?act=list', 'text' => $_LANG['01_car_list']));

	$smarty -> assign('cat_select', cat_list(0, $cat_id, true));
	$smarty -> assign('form_act', 'move_cat');

	/* 显示页面 */
	assign_query_info();
	$smarty -> display('category_move.htm');
}

/*------------------------------------------------------ */
//-- 编辑排序序号
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'edit_sort_order') {
	check_authz_json('cat_manage');

	$id = intval($_POST['id']);
	$val = intval($_POST['val']);

	if (cat_update($id, array('sort_order' => $val))) {
		clear_cache_files();
		// 清除缓存
		make_json_result($val);
	} else {
		make_json_error($db -> error());
	}
}

/*------------------------------------------------------ */
//-- 删除商品分类
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'remove') {

	check_authz_json('id');

	/* 初始化分类ID并取得分类名称 */
	$id = intval($_GET['id']);
	//如果old equipment大于0,释放equipment
	$sql = " SELECT equipment FROM " . $ecs -> table('car') . " WHERE id=" . $id;
	$oldEquipment = $db -> getOne($sql);
	if ($oldEquipment > 0) {
		$db -> autoExecute('car_device', array('status' => 0), "updata", " id= $oldEquipment");
	}

	$sql = "select icon from" . $ecs -> table('car') . "where id = '$id'";
	$icon = $db -> getOne($sql);
	//移除某条
	// $sql = 'DELETE FROM ' . $ecs -> table('car') . " WHERE id = '$id'";
	$sql = 'UPDATE ' . $ecs -> table('car') . " SET delete_stat=1 WHERE id='$id'";
	$row = $db -> query($sql);
	unlink($icon);
	if ($row > 0) {
		echo "移除成功！";

	} else {
		echo "移除失败！";
	}

	$link[0]['text'] = $_LANG['back_list'];
	$link[0]['href'] = 'car.php?act=list';
	//sys_msg($_LANG['catadd_succed'], 0, $link);

	//     $name = $db->getOne('SELECT name FROM ' .$ecs->table('car'). " WHERE cat_id='$id'");

	//     /* 当前分类下是否存在商品 */
	//     $goods_count = $db->getOne('SELECT COUNT(*) FROM ' .$ecs->table('goods'). " WHERE cat_id='$cat_id'");

	//     /* 如果不存在下级子分类和商品，则删除之 */
	//     if ($cat_count == 0 && $goods_count == 0)
	//     {
	//         /* 删除分类 */
	//         $sql = 'DELETE FROM ' .$ecs->table('category'). " WHERE cat_id = '$cat_id'";
	//        echo $sql;
	//         if ($db->query($sql))
	//         {
	//             $db->query("DELETE FROM " . $ecs->table('nav') . "WHERE ctype = 'c' AND cid = '" . $cat_id . "' AND type = 'middle'");
	//             clear_cache_files();
	//             admin_log($cat_name, 'remove', 'category');
	//         }
	//     }
	//     else
	//     {
	//         make_json_error($cat_name .' '. $_LANG['cat_isleaf']);
	//     }
	$url = 'car.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

	ecs_header("Location: $url\n");
	exit ;

	//     $url = 'car.php?act=query&' . str_replace('act=list', '', $_SERVER['QUERY_STRING']);

	//     ecs_header("Location: $url\n");
	//     exit;
}

/*------------------------------------------------------ */
//查看车辆当前位置
if ($_REQUEST['act'] == 'position') {
	$id = intval($_GET['id']);

	$sql= " SELECT longitude,latitude FROM car_device WHERE car_id=".$id;
	$positionArr=$db->getRow($sql);

	$smarty->assign('longitude',$positionArr['longitude']/1000000);
	$smarty->assign('latitude',$positionArr['latitude']/1000000);

	$smarty -> assign('ur_here', '车辆位置');
	$smarty -> assign('action_link', array('href' => 'car.php?act=list', 'text' => $_LANG['01_car_list']));
	$smarty -> assign('is_ajax', 1);
	$smarty -> display('car_position.html');
}

if ($_REQUEST['act'] == 'outlay_log') {
	$carId = intval($_GET['id']);
	$outlay_log = outlay_log($carId);

	$smarty -> assign('log_list', $outlay_log);
	$smarty -> assign('ur_here', '缴费记录列表');
	$smarty -> assign('action_link', array('href' => 'car.php?act=list', 'text' => $_LANG['01_car_list']));
	$smarty -> assign('action_link2', array('href' => "car.php?act=outlay_add&id={$carId}", 'text' => "添加缴费记录"));
	$smarty -> assign('full_page', 1);
	$smarty -> display('car_outlay_log_list.html');
} elseif ($_REQUEST['act'] == 'outlay_add') {
	$carId = intval($_GET['id']);

	$smarty -> assign('ur_here', '添加缴费记录');
	$smarty -> assign('action_link', array('href' => 'car.php?act=list', 'text' => $_LANG['01_car_list']));
	$smarty -> assign('action_link2', array('href' => "car.php?act=outlay_log&id={$carId}", 'text' => "缴费记录列表"));
	$smarty -> assign('full_page', 1);
	$smarty -> assign('form_act', 'outlay_update');
	$smarty -> assign('car_id', $carId);
	$smarty -> display('car_outlay_info.html');
} elseif ($_REQUEST['act'] == 'outlay_update') {
	$carId = intval($_POST['id']);

	$data = array('pay_time' => strtotime($POST['pay_time']), 'start_time' => strtotime($POST['start_time']), 'end_time' => strtotime($POST['end_time']), );
	$data['money'] = $_POST['money'] * 100;
	$data['purpose'] = $_POST['purpose'];
	$data['car_id'] = $carId;

	$db -> autoExecute($ecs -> table('outlay_log'), $data);

	/* 提示信息 */
	$links[] = array('href' => "car.php?act=outlay_log&id={$carId}", 'text' => '返回车辆管理列表');
	;
	sys_msg("添加车辆缴费记录成功", 0, $links);
}

if ($_REQUEST['act'] == 'car_status') {
	check_authz_json('car_manage');

	$id = intval($_POST['id']);

	//获取车辆状态
	$sql = " SELECT status FROM edo_cp_car WHERE id=".$id;
	$carStatus= $db->getOne($sql);

	$val = intval($_POST['val']);
	$exc -> edit("status='$val'", $id);
	make_json_result($val);
}

if ($_REQUEST['act'] == 'car_halt') {
	check_authz_json('car_manage');

	$id = intval($_POST['id']);
	$val = $_POST['val'];
	$admin_id = $_SESSION['admin_id'];

	$data = array('car_id' => $id, 'reason' => $val, 'time' => time(), 'admin_id' => $admin_id);
	$db -> autoExecute($ecs -> table('car_halt'), $data);
	make_json_result($id);
}

if ($_REQUEST['act'] == 'park_edit') {
	$carId = intval($_GET['id']);

	include_once (ROOT_PATH . '/../hfrm/Frm.php');
	include_once (Frm::$ROOT_PATH . '/resource/classes/CarPosManager.php');
	$cpm=new CarPosManager();
	$position=$cpm->getCarParkPosition($carId);
	$position['longitude']=$position['longitude']/1000000;
	$position['latitude']=$position['latitude']/1000000;
	$smarty -> assign('ur_here', $position['name'].'停车位设置');
	$smarty -> assign('brand', $position);
	$smarty -> assign('action_link', array('href' => 'car.php?act=list', 'text' => $_LANG['01_car_list']));
	$smarty -> assign('full_page', 1);
	$smarty -> assign('form_action', 'park_update');
	$smarty -> assign('car_id', $carId);
	$smarty -> display('park_edit.html');
}
elseif ($_REQUEST['act'] == 'park_update') {
	$carId=$POST['car_id'];
	include_once (ROOT_PATH . '/../hfrm/Frm.php');
	include_once (Frm::$ROOT_PATH . '/resource/classes/CarPosManager.php');
	$cpm=new CarPosManager();
	$position=$cpm->getCarParkPosition($carId);

 	$longitude=$POST['longitude']*1000000;
    $latitude=$POST['latitude']*1000000;
	/*处理URL*/
	$link[0]['text'] = $_LANG['back_list'] ;
    $link[0]['href'] = 'car.php?act=list&' . list_link_postfix() ;
    $note = vsprintf('%s的停车位设置成功', $position['name'] ) ;
	if (!empty($position)) {
		if($db->autoExecute('T_Car_Pos',array('FCar_id'=>$POST['car_id'],'FLongitude'=>$longitude,'FLatitude'=>$latitude,'FRadius'=>$_POST['radius']),'update','FCar_id='.$carId))
		{
        	sys_msg( $note, 0, $link ) ;
		}
	}
	else
	{
		if($db->autoExecute('T_Car_Pos',array('FCar_id'=>$POST['car_id'],'FLongitude'=>$longitude,'FLatitude'=>$latitude,'FRadius'=>$_POST['radius'])))
		{
			sys_msg( $note, 0, $link ) ;
		}
	}
    admin_log($_POST['name'], 'edit', 'station');      
}


function outlay_log($carId) {
	global $ecs;
	global $db;

	$sql = " SELECT * FROM " . $ecs -> table('outlay_log') . " WHERE car_id=" . $carId;

	$ret = $db -> getAll($sql);
	$fmData = array();
	$result = array();
	foreach ($ret as $v) {
		$fmData['pay_time'] = date('Y-m-d', $v['pay_time']);
		$fmData['start_time'] = date('Y-m-d', $v['start_time']);
		$fmData['end_time'] = date('Y-m-d', $v['end_time']);
		$fmData['money'] = $v['money'] / 100;
		$fmData['purpose'] = $v['purpose'];
		$fmData['id'] = $v['id'];
		$result[] = $fmData;
	}
	return $result;

}

function getStation() {
	global $db, $ecs;
	$station = $db -> getAll("select * from " . $ecs -> table("station") . " order by id");
	return $station;
}

function car_list() {
	$result = get_filter();
	if ($result === false) {
		/* 分页大小 */
		$filter = array();

		/* 记录总数以及页数 */
		if (isset($_POST['keyword'])) {
			$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs'] -> table('car') . ' WHERE name = \'' . $_POST['keyword'] . '\'';
		} else {
			$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs'] -> table('car')." WHERE delete_stat=0";
		}

		$filter['record_count'] = $GLOBALS['db'] -> getOne($sql);

		$filter = page_and_size($filter);

		/* 查询记录 */
		if (isset($_POST['keyword'])) {
			if (strtoupper(EC_CHARSET) == 'GBK') {
				$keyword = iconv("UTF-8", "gb2312", $_POST['keyword']);
			} else {
				$keyword = $_POST['keyword'];
			}
			$sql = " SELECT c.*,s.name as station_name ,g.car_group_name,g.car_group_id,r.region_name,c.brand " . " FROM " . $GLOBALS['ecs'] -> table('car') . " c " . " LEFT JOIN " . $GLOBALS['ecs'] -> table('car_group') . " g " . " ON g.car_group_id = c.group" . " LEFT JOIN " . $GLOBALS['ecs'] -> table('station') . " s " . " ON s.id = c.station " . " LEFT JOIN " . $GLOBALS['ecs'] -> table('region') . " r " . " ON r.region_id=s.city " . " LEFT JOIN " . 'car_device' . " eq " . " ON eq.car_id=c.id " . " LEFT JOIN " . $GLOBALS['ecs'] -> table('sim_card') . " sc " . " ON sc.id=eq.sim " . " WHERE c.name like '%{$keyword}%' or c.number like '%{$keyword}%' or c.model like '%{$keyword}%' 
                         or c.brand like '%{$keyword}%' or g.car_group_name like '%{$keyword}%' or r.region_name like '%{$keyword}%' 
                         or s.name like '%{$keyword}%' or sc.phone_number like '%{$keyword}%' " . " ORDER BY sort_order ASC";
		} else {
			$sql = " SELECT c.*,s.name as station_name ,g.car_group_name,g.car_group_id ,r.region_name,c.brand " . " FROM " . $GLOBALS['ecs'] -> table('car') . " c " . " LEFT JOIN " . $GLOBALS['ecs'] -> table('car_group') . " g " . " ON g.car_group_id = c.group" . " LEFT JOIN " . $GLOBALS['ecs'] -> table('station') . " s " . " ON s.id = c.station " . " LEFT JOIN " . $GLOBALS['ecs'] -> table('region') . " r " . " ON r.region_id=s.city WHERE c.delete_stat=0" . " ORDER BY c.sort_order ASC";
		}

		set_filter($filter, $sql);
	} else {
		$sql = $result['sql'];
		$filter = $result['filter'];
	}
	$res = $GLOBALS['db'] -> selectLimit($sql, $filter['page_size'], $filter['start']);

	$arr = array();
	$cm = new CarManager();
	while ($rows = $GLOBALS['db'] -> fetchRow($res)) {
		$rows['followSum'] = $cm -> howManyFollowsOfcar($rows['id']);
		$arr[] = $rows;
	}
	return array('car' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

function get_groups() {
	$sql = " SELECT car_group_id,car_group_name " . " FROM " . $GLOBALS['ecs'] -> table('car_group');
	return $GLOBALS['db'] -> getAll($sql);
}

function get_equipment() {
	$sql = " SELECT id,serial ,status " . " FROM " . 'car_device';

	return $GLOBALS['db'] -> getAll($sql);
}

function initial_info($carId) {
	global $db;
	global $ecs;

	$sql = " SELECT * FROM " . $ecs -> table('car_init_info') . " WHERE car_id=" . $carId;

	return $db -> getRow($sql);

}
?>