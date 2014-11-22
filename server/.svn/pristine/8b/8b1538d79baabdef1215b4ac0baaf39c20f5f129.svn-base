<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
$exc = new exchange($ecs->table("car"), $db, 'id', 'name');
include_once(ROOT_PATH . 'includes/cls_image.php');
include_once(ROOT_PATH . '../client/classes/CarManager.php');
$image = new cls_image($_CFG['bgcolor']);

/* act操作项的初始化 */
if ( empty( $_REQUEST['act'] ) )
{
    $_REQUEST['act'] = 'list' ;
}
else
{
    $_REQUEST['act'] = trim( $_REQUEST['act'] ) ;
}

/*------------------------------------------------------ */
//-- 商品分类列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{

    /* 获取分类列表 */
    $userMedalList = userMedalList() ;

    /* 模板赋值 */
    $smarty->assign('ur_here',      '会员奖章管理') ;
    // $smarty->assign('action_link',  array('href' => 'user_medal.php?act=add', 'text' =>'发放奖章')) ;
    $smarty->assign('full_page',    1) ;

    $smarty->assign('userMedalList',     $userMedalList['user_medal']) ;

    /* 列表页面 */
    assign_query_info() ;
    $smarty->display('user_medal_list.htm');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $userMedalList = userMedalList();
    $smarty->assign('userMedalList',$userMedalList['user_medal']);
    make_json_result($smarty->fetch('user_medal_list.htm'));
}
/*------------------------------------------------------ */
//-- 添加商品分类
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'add')
{
    /* 权限检查 */
    admin_priv('car_manage');


    
    /* 模板赋值 */
    $medalList=getAllMedal();
    // $userMedalList = userMedalList();

    $smarty->assign('medalList',$medalList);
    $smarty->assign('ur_here',      '添加奖章');

    $smarty->assign('action_link',  array('href' => 'user_medal.php?act=list', 'text' => "用户奖章列表")); 

    $smarty->assign('uid',  $_REQUEST['uid']) ;
    $smarty->assign('form_act', 'insert') ;
    /* 显示页面 */
    assign_query_info();
    $smarty->display('user_medal_info.htm');
}

/*------------------------------------------------------ */
//-- 商品分类添加时的处理
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'insert')
{
    /* 权限检查 */
    admin_priv('car_manage');
    /* 初始化变量 */
    /* 入库的操作 */
    /*
    判断是否有非法操作
     */
    $medalId = $_REQUEST['medal_id'];
    $userId=$_REQUEST['uid'];

    if (!empty($medalId)) {
		require_once (ROOT_PATH . "/../medal_container/MedalContainer.php");
		$m = new MedalContainer();
		$m -> allocMedal2User($userId, $medalId);

		$link[0]['text'] = '继续添加';
        $link[0]['href'] = 'user_medal.php.?act=add';

        $link[1]['text'] = '返回列表';
        $link[1]['href'] = 'user_medal.php?act=list';
        sys_msg($_LANG['catadd_succed'], 0, $link);
    }
 }




/*------------------------------------------------------ */
//-- 删除商品分类
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'remove')
{
	
    check_authz_json('id');

    /* 初始化分类ID并取得分类名称 */
    $id   = intval($_GET['id']);
    //移除某条
    $sql = 'DELETE FROM ' .$ecs->table('user2medal'). " WHERE id = '$id'";
    $row = $db->query($sql);
    unlink($icon);
       
    $link[0]['text'] = $_LANG['back_list'];
    $link[0]['href'] = 'user_medal.php?act=list';
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
    $url = 'user_medal.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
    
//     $url = 'car.php?act=query&' . str_replace('act=list', '', $_SERVER['QUERY_STRING']);

//     ecs_header("Location: $url\n");
//     exit;
}

/*------------------------------------------------------ */
//查看车辆当前位置
if ($_REQUEST['act'] == 'position')
{
    $id = intval($_GET['id']);
    $smarty->assign('ur_here',     '车辆位置');
    $smarty->assign('action_link',  array('href' => 'car.php?act=list', 'text' => $_LANG['01_car_list'])); 
    $smarty->assign('is_ajax',1);
    $smarty->display('car_position.html');
}


if ($_REQUEST['act'] == 'outlay_log')
{
    $carId = intval($_GET['id']);
    $outlay_log = outlay_log($carId);

    $smarty->assign('log_list',$outlay_log);
    $smarty->assign('ur_here',     '缴费记录列表');
    $smarty->assign('action_link',  array('href' => 'car.php?act=list', 'text' => $_LANG['01_car_list'])); 
    $smarty->assign('action_link2',  array('href' => "car.php?act=outlay_add&id={$carId}", 'text' => "添加缴费记录")); 
    $smarty->assign('full_page',1);
    $smarty->display('car_outlay_log_list.html');
}

elseif ($_REQUEST['act'] == 'outlay_add')
{
    $carId = intval($_GET['id']);


    $smarty->assign('ur_here',     '添加缴费记录');
    $smarty->assign('action_link',  array('href' => 'car.php?act=list', 'text' => $_LANG['01_car_list'])); 
    $smarty->assign('action_link2',  array('href' => "car.php?act=outlay_log&id={$carId}", 'text' => "缴费记录列表")); 
    $smarty->assign('full_page',1);
    $smarty->assign('form_act','outlay_update');
    $smarty->assign('car_id',$carId);
    $smarty->display('car_outlay_info.html');
}
elseif ($_REQUEST['act'] == 'outlay_update') {
    $carId = intval($_POST['id']);
    
    $data = array('pay_time'=>strtotime($POST['pay_time']),
                    'start_time'=>strtotime($POST['start_time']),
                    'end_time'=>strtotime($POST['end_time']),
                    );
    $data['money']=$_POST['money']*100;
    $data['purpose']=$_POST['purpose'];
    $data['car_id']=$carId;



    $db->autoExecute($ecs->table('outlay_log'),$data);
    
    /* 提示信息 */
    $links[] =array('href' => "car.php?act=outlay_log&id={$carId}", 'text' => '返回车辆管理列表');
                   ;
    sys_msg("添加车辆缴费记录成功", 0, $links);
}

if ($_REQUEST['act'] == 'car_status')
{
    check_authz_json('car_manage');

    $id     = intval($_POST['id']);
    $val    = intval($_POST['val']);

    $exc->edit("status='$val'", $id);
    make_json_result($val);
}

if ($_REQUEST['act']=='car_halt') {
    check_authz_json('car_manage');

    $id     = intval($_POST['id']);
    $val    = $_POST['val'];
    $admin_id = $_SESSION['admin_id'];

    $data =array('car_id'=>$id,'reason'=>$val,'time'=>time(),'admin_id'=>$admin_id);
    $db->autoExecute($ecs->table('car_halt'),$data);
    make_json_result($id);
}

function outlay_log($carId)
{
    global $ecs;
    global $db;

    $sql =  " SELECT * FROM ".$ecs->table('outlay_log').
            " WHERE car_id=".$carId;

    $ret=$db->getAll($sql);
    $fmData=array();
    $result=array();
    foreach ($ret as $v) {
        $fmData['pay_time']=date('Y-m-d',$v['pay_time']) ;
        $fmData['start_time']=date('Y-m-d',$v['start_time']) ;
        $fmData['end_time']=date('Y-m-d',$v['end_time']) ;
        $fmData['money']=$v['money']/100 ; 
        $fmData['purpose']=$v['purpose'] ;
        $fmData['id']=$v['id'] ;
        $result[]=$fmData;
    }
    return $result;

}


function getStation()
{
	global $db, $ecs;
	$station = $db->getAll("select * from ".$ecs->table("station")." order by id");
	return $station ;
}

function userMedalList()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 分页大小 */
        $filter = array();

        if ($_REQUEST['uid']) {
        	$where = " AND u.uid=".$_REQUEST['uid'];
        }
        else
        {
        	$where='';
        }
        /* 记录总数以及页数 */
        if (isset($_POST['keyword'])&&!empty($_POST['keyword']))
        {
        	$keyword=$_POST['keyword'];
            $sql = " SELECT COUNT(*) ".
                    " FROM ".$GLOBALS['ecs']->table('user2medal'). " u2m ".
                    " LEFT JOIN ".$GLOBALS['ecs']->table('medal')." m ".
                    " ON u2m.medal_id = m.id ".
                    " LEFT JOIN ".$GLOBALS['sns']->table('user')." u ".
                    " ON u.uid = u2m.user_id ".
                    " WHERE (m.name like '%{$keyword}%' OR u.phone like '%{$keyword}%' ) ".$where;
        }
        else
        {
            $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('user2medal');
        }

        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);

        /* 查询记录 */
        if (isset($_POST['keyword']))
        {
            if(strtoupper(EC_CHARSET) == 'GBK')
            {
                $keyword = iconv("UTF-8", "gb2312", $_POST['keyword']);
            }
            else
            {
                $keyword = $_POST['keyword'];
            }
            //奖章名称,生效时间,有效期,授予数量,是否用于优 惠,使用数量,累计优惠金额!
            $sql =  " SELECT u.uid,u2m.id,m.name,u2m.use_time,u2m.time, u.phone,u.uid ".
                    " FROM ".$GLOBALS['ecs']->table('user2medal'). " u2m ".
                    " LEFT JOIN ".$GLOBALS['ecs']->table('medal')." m ".
                    " ON u2m.medal_id = m.id ".
                    " LEFT JOIN ".$GLOBALS['sns']->table('user')." u ".
                    " ON u.uid = u2m.user_id ".
                    " WHERE ( m.name like '%{$keyword}%' OR u.phone like '%{$keyword}%') ".$where.
                    " ORDER BY u.uid ASC";
        }
        else
        {
            $sql =  " SELECT u.uid,u2m.id, m.name,u2m.use_time, u2m.time, u.phone ,u.uid".
                    " FROM ".$GLOBALS['ecs']->table('user2medal'). " u2m ".
                    " LEFT JOIN ".$GLOBALS['ecs']->table('medal')." m ".
                    " ON u2m.medal_id = m.id ".
                    " LEFT JOIN ".$GLOBALS['sns']->table('user')." u ".
                    " ON u.uid = u2m.user_id ".
                    " WHERE 1 ".$where.
                    " ORDER BY u.uid ASC";
        }

        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    $arr = array();

    while ($rows = $GLOBALS['db']->fetchRow($res))
    {   
    	$rows['use_time_fm']=empty($rows['use_time']) ? '未使用' : date('Y-m-d',$rows['use_time']);
    	$rows['time_fm']=date('Y-m-d',$rows['time']);
        $arr[] = $rows;
    }

    return array('user_medal' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}


function getAllMedal()
{
	global $db;
	global $ecs;

	$sql =  " SELECT * FROM ".$ecs->table('medal');
	$ret = $db->getAll($sql);

	return $ret;


}


function allocMedal($userID) {
	global $db;
	global $ecs;
	
	// $allocMedalID 
	$sql = " SELECT COUNT(1) FROM " . $ecs -> table('user2medal') . " WHERE user_id=" . $userID . " AND medal_id=" . $allocMedalID;
	if ($db -> getOne($sql) == 0) {
		require_once (ROOT_PATH . "/../medal_container/medals/FirstExperienceMedal.php");
		$m = new MedalContainer();
		$m -> allocMedal2User($userID, $allocMedalID);
	}
}
?>