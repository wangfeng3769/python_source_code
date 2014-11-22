<?php
//user表  /注册审核录入:姓名(true_name)、性别（sex）、生日（birthday）、身份证号（identity）、驾照有效期(ValiFor)
define('IN_ECS', true);
require (dirname(__FILE__) . '/includes/init.php');
include_once (ROOT_PATH . 'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);

// $exc = new exchange($ecs->table("identity_approve"), $db, 'id', 'user_id');
/*------------------------------------------------------ */
//-- 审核列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list') {
	//echo $_LANG['06_equite_list'];
	$smarty -> assign('ur_here', $_LANG['12_approve_list']);
	//$smarty->assign( 'action_link',  array('text' => $_LANG['05_sim_card_add'], 'href' => 'sim_card.php?act=add'));
	$smarty -> assign('full_page', 1);
	$brand_list = get_brandlist();
	$smarty -> assign('brand_list', $brand_list['brand']);
	$smarty -> assign('filter', $brand_list['filter']);
	$smarty -> assign('record_count', $brand_list['record_count']);
	$smarty -> assign('page_count', $brand_list['page_count']);
	assign_query_info();
	$smarty -> display('identity_approve_list.htm');
} elseif ($_REQUEST['act'] == 'approve') {
	admin_priv('brand_manage');

	/*对描述处理*/
	if (!empty($_POST['brand_desc'])) {
		$_POST['brand_desc'] = $_POST['brand_desc'];
	}

	if (!empty($_GET['id']) && ceil($_GET['id']) == $_GET['id']) {
		$where = 'AND a.id = ' . $_GET['id'];
		$brand = get_brandlist($where);
		$brand = $brand['brand'][0];
		if ($brand['is_processed'] == 1) {
			$link[0]['text'] = '返回';
			$link[0]['href'] = 'identity_approve.php?act=list';
			sys_msg(' 该记录已经审核 ' . $result, 1, $link);
		}
	} else {
		echo '参数错误';
		die();
	}
	$validfor1 = $brand['validfor'];
	$validfor = date("Y-m-d", $validfor1);
	//     echo $brand['validfor'];
	//     exit();
	/*审核过程*/
	$smarty -> assign('ur_here', $_LANG['brand_edit']);
	$smarty -> assign('action_link', array('text' => $_LANG['12_approve_list'], 'href' => 'identity_approve.php?act=list'));
	$smarty -> assign('brand', $brand);
	$smarty -> assign('validfor', $validfor);
	//print_r ($brand);
	// exit();
	$smarty -> assign('form_action', 'updata');

	assign_query_info();

	$smarty -> display('identity_approving.htm');
	/*更新会员卡管理表状态*/
	// $vipCard = new exchange($ecs->table("vip_card"), $db, 'id', 'vip_card_id');
	// $param_v = "user_id = ".$brand['uid'].",allot_status = 1 ";
	// $c = $vipCard->edit($param_v,$brand['vip_card_id']);
	// assign_query_info();
	//     /* 清除缓存 */
	// clear_cache_files();

	// admin_log($_POST['brand_name'], 'edit', 'brand');
	// $link[0]['text'] = $_LANG['back_list'];
	// $link[0]['href'] = 'identity_approve.php?act=list';
	// $note = vsprintf($_LANG['brandedit_succed'], $brand['card_number']);
	// sys_msg($note, 0, $link);

}

/*------------------------------------------------------ */
//--
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'updata') {
	/* 权限判断 */

	if ($_POST['pass'] == 1) {

		if (empty($_POST['IDnumber']) || ($_POST['IDis'] == 0) || empty($_POST['uid']) || empty($_POST['VailFor']) || ($_POST['VailFor_or'] == 0) || ($_POST['sex'] != '0' && $_POST['sex'] != '1') || empty($_POST['true_name']) || empty($_POST['birthday']) || ($_POST['user_type'] != '0' && $_POST['user_type'] != '1')) {
			$link[0]['text'] = '返回';
			$link[0]['href'] = 'identity_approve.php?act=approve&id=' . $_REQUEST['id'];
			sys_msg('提交信息错误 ' . $result, 1, $link);
		}
		//审核通过,更新审核状态
		/*组织审核状态*/
		$approve_stat = 0;

		if ($_REQUEST['user_type'] == 0) {
			$approve_stat += pow(2, 0) + pow(2, 1);

		} else {
			$approve_stat += pow(2, 0) + pow(2, 1) + pow(2, 2);
		}

		//判断身份证号码是否有相同
		$sql = "SELECT 1 FROM " . $ecs -> table('identity_approve') . " WHERE identity_num='" . $_POST['IDnumber'] . "'";
		$ret = $db -> getOne($sql);
		if ($ret > 0) {
			sys_msg('系统中已经存在相同的身份证号码。 ' . $result, 1, $link);
			return;
		}

		$sql = " UPDATE " . $ecs -> table('identity_approve') . " SET identity_num='" . $_POST['IDnumber'] . "' ,approve_stat = " . $approve_stat . " ,is_processed = 1,first_approve_time=" . time() . " WHERE id=" . $_POST['id'];

		// modify by yangbei 2013-06-27 增加群组字段
		// $sql2 = " UPDATE " . $sns -> table('user') . " SET true_name='" . $_POST['true_name'] . "',sex = '" . $_POST['sex'] . "',user_type = '" . $_POST['user_type'] . "' ,approve_id = 1,birthday = '" . $_POST['birthday'] . "' ,validfor ='" . strtotime($_POST['VailFor']) . "' WHERE uid='" . $_POST['uid'] . "'";
		$sql2 = " UPDATE " . $sns -> table('user') . " SET true_name='" . $_POST['true_name'] . "',sex = '" . $_POST['sex'] . "',user_type = '" . $_POST['user_type'] . "' ,approve_id = 1,birthday = '" . $_POST['birthday'] . "' ,validfor ='" . strtotime($_POST['VailFor']) . "',user_group_id ='".$_POST['group_id']."' WHERE uid='" . $_POST['uid'] . "'";
		// modify by yangbei 2013-06-27 增加群组字段
		
		$db -> query($sql2);
		$db -> query($sql);

		//记录操作信息
		$sql = " INSERT INTO " . $ecs -> table('approve_log') . "(id_approve, result,reason, approve_time, admin_id,admin_name) " . " VALUES ('$_POST[id]', '$approve_stat',  '0',  " . time() . ", '$_SESSION[admin_id]','$_SESSION[admin_name]')";

		$db -> query($sql);
		$result = '审核通过';
	} else {

		/*初始华审核状态*/
		$approve_stat = 0;

		//记录操作信息
		$reason = '';
		if ($_POST[IDis] == false) {
			$reason .= ' 身份证:' . $_POST[IDis_reason] . " ";
		} else {
			$approve_stat += pow(2, 0);
		}

		if ($_POST[VailFor_or] == false) {
			$reason .= ' 驾照有效期:' . $_POST[VailFor_or_reason] . " ";
		} else {
			$approve_stat += pow(2, 1);
		}
		// if (! $_POST[student_front] && ! empty($_POST[student_front])) {
		//     $reason .= " 学生证:".$_POST[student_front_reason]." ";
		// }
		// if ($_POST[student_front]==1) {
		//     $approve_stat += pow(2,2);
		// }
		//审核不通过,更新审核状态
		$sql = " UPDATE " . $ecs -> table('identity_approve') . " SET approve_stat = $approve_stat " . " ,is_processed = 1" . " WHERE id=" . $_POST['id'];
		$db -> query($sql);

		$sql = " INSERT INTO " . $ecs -> table('approve_log') . "(id_approve, result,reason, approve_time, admin_id,admin_name) " . " VALUES ('$_POST[id]', '$approve_stat',  '$reason', " . time() . ", '$_SESSION[admin_id]','$_SESSION[admin_name]')";
		$db -> query($sql);

		$sql2 = " UPDATE " . $sns -> table('user') . " SET true_name='" . $_POST['true_name'] . "',sex = '" . $_POST['sex'] . "',user_type = '" . $_POST['user_type'] . "' ,approve_id = 1,birthday = '" . $_POST['birthday'] . "' ,validfor ='" . strtotime($_POST['VailFor']) . "' WHERE uid='" . $_POST['uid'] . "'";
		$db -> query($sql2);
		$result = '审核不通过';
	}
	admin_log($_POST['id'], 'updata', 'id_approve');

	$sql = "SELECT reason FROM " . $ecs -> table("approve_log") . " WHERE id_approve = " . $_POST['id'] . " ORDER BY approve_time DESC LIMIT 1";
	$errmsg = $db -> getOne($sql);

	$msg = "恭喜您实名认证通过，易多赠送您“免费初体验”奖章一枚，可免费用车1小时。祝您旅途愉快！";
	if (1 != $_POST['pass']) {
		$msg = "非常抱歉，您的实名认证未通过。原因：{$errmsg}";
	}

	if (1 == $_POST['pass']) {
		//赠送奖章 , //只能通过类名确定分发的奖章
		//add by yangbei 2013-06-27 增加是否颁发奖章判断
		if (1==$_POST['allocMedal']) {
			allocMedal($_POST['uid']);
		}
		
	}
	$sql = "SELECT user_id FROM " . $ecs -> table("identity_approve") . " WHERE id = " . $_POST['id'];
	$userId = $db -> getOne($sql);

	require_once (ROOT_PATH . "../client/classes/MsgBox.php");
	$msgBox = new MsgBox();
	$msgBox -> sendMsg(2, $userId, $msg);

	require_once (ROOT_PATH . "../client/classes/CarManager.php");
	$um = new UserManager();
	$user = $um -> getUser($userId);
	$smsSender = new SmsSender();
	$smsSender -> send($msg, $user['phone']);

	/* 清除缓存 */
	clear_cache_files();
	$link[0]['text'] = $_LANG['back_list'];
	$link[0]['href'] = 'identity_approve.php?act=list';

	sys_msg('审核完成 ' . $result, 0, $link);
} elseif ($_REQUEST['act'] == 'log') {

	$where = ceil($_REQUEST['uid']) == $_REQUEST['uid'] ? " AND user_id=$_REQUEST[uid]" : '';

	$brand_list = get_loglist($where);
	$smarty -> assign('ur_here', '审核记录');
	$smarty -> assign('action_link', array('text' => '审核列表', 'href' => 'identity_approve.php?act=list'));
	$smarty -> assign('full_page', 1);

	// $data=$brand_list['brand'];
	// foreach ($data as $k => $v) {
	//     $data[$k]['fm_time']=date('Y-m-d H:i:s',$v['approve_time']);
	// }
	$smarty -> assign('brand_list', $brand_list['brand']);
	$smarty -> assign('record_count', $brand_list['record_count']);
	$smarty -> assign('page_count', $brand_list['page_count']);

	assign_query_info();
	$smarty -> display('identity_approve_log.htm');
}
/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query') {
	$brand_list = get_brandlist();
	$smarty -> assign('brand_list', $brand_list['brand']);
	$smarty -> assign('filter', $brand_list['filter']);
	$smarty -> assign('record_count', $brand_list['record_count']);
	$smarty -> assign('page_count', $brand_list['page_count']);

	make_json_result($smarty -> fetch('identity_approve_list.htm'), '', array('filter' => $brand_list['filter'], 'page_count' => $brand_list['page_count']));
}

/**
 * 获取sim卡列表列表
 *
 * @access  public
 * @return  array
 */
function get_brandlist($where = '') {
	$result = get_filter();
	if ($result === false) {
		/* 分页大小 */
		$filter = array();

		$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs'] -> table('identity_approve');

		$filter['record_count'] = $GLOBALS['db'] -> getOne($sql);

		$filter = page_and_size($filter);

		/* 查询记录 */
		if (isset($_POST['brand_name'])) {
			if (strtoupper(EC_CHARSET) == 'GBK') {
				$keyword = iconv("UTF-8", "gb2312", $_POST['brand_name']);
			} else {
				$keyword = $_POST['brand_name'];
			}
			$sql = " SELECT a.id,u.uid,u.birthday,u.validfor,a.identity_num,u.sex,u.true_name,u.user_type,u.phone,a.sort_order,u.phone,a.post_time,a.approve_stat,a.img_student,a.img_others,a.img_identity_front,a.is_processed
                    FROM " . $GLOBALS['ecs'] -> table('identity_approve') . " a " . " LEFT JOIN " . $GLOBALS['sns'] -> table('user') . " u " . " ON u.uid = a.user_id " . "WHERE u.phone like '%{$keyword}%' or a.approve_stat like '%{$keyword}%' " . " ORDER BY a.post_time DESC";

			//die($sql);
			//$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('oil_card')." WHERE card_id like '%{$keyword}%' or password like '%{$keyword}%' or provider like '%{$keyword}%' ORDER BY sort_order ASC";
		} else {//购买时间,发送状态,发卡员,发送时间,城市,邮寄地址
			$sql = " SELECT a.id,u.uid,u.birthday,u.validfor,a.identity_num,u.sex,u.true_name,u.user_type,u.phone,a.sort_order,u.uname,a.post_time,a.approve_stat,a.img_student,a.img_others,a.img_identity_front,a.is_processed
                    FROM " . $GLOBALS['ecs'] -> table('identity_approve') . " a " . " LEFT JOIN " . $GLOBALS['sns'] -> table('user') . " u " . " ON u.uid = a.user_id " . " WHERE 1 " . $where . " ORDER BY a.post_time DESC";
		}

		set_filter($filter, $sql);
	} else {
		$sql = $result['sql'];
		$filter = $result['filter'];
	}
	$res = $GLOBALS['db'] -> selectLimit($sql, $filter['page_size'], $filter['start']);

	$sql2 = " SELECT g.reason
        FROM " . $GLOBALS['ecs'] -> table('identity_approve') . " a " . " LEFT JOIN " . $GLOBALS['sns'] -> table('user') . " u " . " ON u.uid = a.user_id " . " LEFT JOIN " . $GLOBALS['ecs'] -> table('approve_log') . " g " . " ON g.id_approve = a.id " . " WHERE 1 " . $where . " ORDER BY g.approve_time DESC";
	/*格式化数据*/
	$arr2 = $GLOBALS['db'] -> getAll($sql2);

	$arr = array();
	while ($rows = $GLOBALS['db'] -> fetchRow($res)) {
		$brand_logo = empty($rows['brand_logo']) ? '' : '<a href="../' . DATA_DIR . '/brandlogo/' . $rows['brand_logo'] . '" target="_brank"><img src="images/picflag.gif" width="16" height="16" border="0" alt=' . $GLOBALS['_LANG']['brand_logo'] . ' /></a>';
		$site_url = empty($rows['site_url']) ? 'N/A' : '<a href="' . $rows['site_url'] . '" target="_brank">' . $rows['site_url'] . '</a>';

		$rows['brand_logo'] = $brand_logo;
		$rows['site_url'] = $site_url;
		$rows['reason'] = $arr2[0];
		$rows['birthday'] = $rows['birthday'];
		$rows['validfor'] = $rows['validfor'];
		$rows['identity_num'] = $rows['identity_num'];
		$rows['true_name'] = $rows['true_name'];
		$rows['phone'] = $rows['phone'];
		$rows['approve_stat'] = $rows['approve_stat'];
		$rows['user_type'] = $rows['user_type'];
		$rows['sex'] = $rows['sex'];
		$rows['img_student'] = $rows['img_student'];
		$rows['img_others'] = $rows['img_others'];
		$rows['img_identity_front'] = $rows['img_identity_front'];
		$rows['fm_time'] = date('Y-m-d H:i:s', $rows['post_time']);
		$arr[] = $rows;
		/*格式化url数据*/
		// print_r($rows['reason']);eixt;
	}
	// print_r($arr2);exit();
	// print_r($arr);exit;
	return array('brand' => $arr, 'arand' => $arr2, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

// function get_list()
// {
//     $arr = array();
//      $sql = " SELECT a.id,u.uid,u.birthday,u.validfor,u.identity_num,u.sex,u.true_name,a.sort_order,u.uname,a.post_time,a.approve_stat,a.img_student,a.img_others,a.img_identity_front,a.is_processed
//             FROM ".$GLOBALS['ecs']->table('identity_approve'). " a ".
//             " LEFT JOIN ".$GLOBALS['sns']->table('user')." u ".
//             " ON u.uid = a.user_id ".
//             "WHERE u.uname like '%{$keyword}%' or a.approve_stat like '%{$keyword}%' ".
//             " ORDER BY a.approve_stat ASC";
//     $res = $GLOBALS['db']->getOne($sql);
// }

function get_loglist($where = '') {
	$result = get_filter();
	if ($result === false) {
		/* 分页大小 */
		$filter = array();

		$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs'] -> table('approve_log');

		$filter['record_count'] = $GLOBALS['db'] -> getOne($sql);

		$filter = page_and_size($filter);

		/* 查询记录 */
		if (isset($_POST['brand_name'])) {
			if (strtoupper(EC_CHARSET) == 'GBK') {
				$keyword = iconv("UTF-8", "gb2312", $_POST['brand_name']);
			} else {
				$keyword = $_POST['brand_name'];
			}
			$sql = " SELECT *
                    FROM " . $GLOBALS['ecs'] -> table('approve_log') . " l " . " LEFT JOIN " . $GLOBALS['ecs'] -> table('identity_approve') . " a " . " ON a.id=l.id_approve " . " WHERE 1 AND l.admin_name like '%{$keyword}%'" . $where . " ORDER BY l.id DESC ";

			//$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('oil_card')." WHERE card_id like '%{$keyword}%' or password like '%{$keyword}%' or provider like '%{$keyword}%' ORDER BY sort_order ASC";
		} else {
			$sql = " SELECT *
                    FROM " . $GLOBALS['ecs'] -> table('approve_log') . " l " . " LEFT JOIN " . $GLOBALS['ecs'] -> table('identity_approve') . " a " . " ON a.id=l.id_approve " . " WHERE 1 " . $where . " ORDER BY l.id DESC ";
		}

		set_filter($filter, $sql);
	} else {
		$sql = $result['sql'];
		$filter = $result['filter'];
	}
	$res = $GLOBALS['db'] -> selectLimit($sql, $filter['page_size'], $filter['start']);

	$arr = array();
	while ($rows = $GLOBALS['db'] -> fetchRow($res)) {
		$brand_logo = empty($rows['brand_logo']) ? '' : '<a href="../' . DATA_DIR . '/brandlogo/' . $rows['brand_logo'] . '" target="_brank"><img src="images/picflag.gif" width="16" height="16" border="0" alt=' . $GLOBALS['_LANG']['brand_logo'] . ' /></a>';
		$site_url = empty($rows['site_url']) ? 'N/A' : '<a href="' . $rows['site_url'] . '" target="_brank">' . $rows['site_url'] . '</a>';

		$rows['brand_logo'] = $brand_logo;
		$rows['site_url'] = $site_url;
		$rows['fm_time'] = date('Y-m-d H:i:s', $rows['approve_time']);
		$arr[] = $rows;
	}

	return array('brand' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

function allocMedal($userID) {
	global $db;
	global $ecs;
	$sql = "SELECT id FROM " . $ecs -> table('medal') . " WHERE class_name = 'FirstExperienceMedal' LIMIT 1;";
	$allocMedalID = $db -> getOne($sql);
	if (empty($allocMedalID)) {
		return;
	}
	$sql = " SELECT COUNT(1) FROM " . $ecs -> table('user2medal') . " WHERE user_id=" . $userID . " AND medal_id=" . $allocMedalID;
	if ($db -> getOne($sql) == 0) {
		require_once (ROOT_PATH . "/../medal_container/medals/FirstExperienceMedal.php");
		$m = new MedalContainer();
		$m -> allocMedal2User($userID, $allocMedalID);
	}
}
?>