<?php 


define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
$exc = new exchange('after_pay_group', $db, 'group_id', 'limit_money');

/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

/*------------------------------------------------------ */
//-- 投票列表页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 模板赋值 */
    $smarty->assign('ur_here',      $_LANG['list_vote']);
    $smarty->assign('action_link',  array('text' => '添加后付费群组', 'href'=>'after_pay_group.php?act=add'));
    $smarty->assign('full_page',    1);

    $group_list = afterPayGroupList();
   	

    $smarty->assign('list',            $group_list['brand']);
    $smarty->assign('filter',          $group_list['filter']);
    $smarty->assign('record_count',    $group_list['record_count']);
    $smarty->assign('page_count',      $group_list['page_count']);

    /* 显示页面 */
    assign_query_info();
    $smarty->display('after_pay_group.htm');
}
elseif ($_REQUEST['act'] == 'add')
{
    $smarty->assign('action_link', array('text' => '负费群组列表', 'href' => 'after_pay_group.php?act=list&' . list_link_postfix()));

	require_once ROOT_PATH . 'includes/lib_user.php' ;
	$group = getUserGroup();
    foreach ($group as $k => $v) {
        $group[$k]['group_id']=$v['user_group_id'];
    }
	$smarty->assign("group", $group);
	$smarty->assign("form_action", "insert");
	$smarty->display('after_pay_group_info.htm');
}
elseif ($_REQUEST['act'] == 'insert')
{
    /*检查品牌名是否重复*/
    admin_priv('brand_manage');
   
    /*插入数据*/
    // $_POST['deposit']=$_POST['deposit']*100;
    $limit_money=$_POST['limit_money']*100;
    $sql = "INSERT INTO after_pay_group (group_id, limit_money) ".
           "VALUES ('$_POST[group_id]', '$limit_money')";
    $db->query($sql);
    

    admin_log($db->insert_id(),'add','limit_money');

    /* 清除缓存 */
    clear_cache_files();

    $link[0]['text'] = '继续添加';
    $link[0]['href'] = 'after_pay_group.php?act=add';

    $link[1]['text'] = '返回列表';
    $link[1]['href'] = 'after_pay_group.php?act=list';

    sys_msg('添加成功', 0, $link);
}
/*------------------------------------------------------ */
//-- 编辑品牌
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    /* 权限判断 */
    admin_priv('brand_manage');
   
    $sql=  "SELECT d.*,g.title FROM after_pay_group d 
            LEFT JOIN edo_user_group g ON d.group_id=g.user_group_id WHERE group_id=".$_REQUEST['id'];
    $brand = $db->GetRow($sql);
    $brand['limit_money']=$brand['limit_money']/100;
    $smarty->assign('ur_here',     $_LANG['brand_edit']);
    $smarty->assign('action_link', array('text' => '后付费群组列表', 'href' => 'after_pay_group.php?act=list&' . list_link_postfix()));
	
    $smarty->assign('brand',       $brand);
    $smarty->assign('group',       array($brand));
    $smarty->assign('form_action', 'updata');

	require_once ROOT_PATH . 'includes/lib_user.php' ;

    assign_query_info();
    $smarty->display('after_pay_group_info.htm');
}
elseif ($_REQUEST['act'] == 'updata')
{
    admin_priv('brand_manage');
  
    // $_POST['deposit']=$_POST['deposit']*100;
    $limit_money=$_POST['limit_money']*100;
    $param = " limit_money='$limit_money'";

    if ($exc->edit($param,  $_POST['group_id']))
    {
        /* 清除缓存 */
        clear_cache_files();
        admin_log($_POST['id'], 'edit', 'after_pay_group');

        $link[0]['text'] = '返回列表';
        $link[0]['href'] = 'after_pay_group.php?act=list&' . list_link_postfix();
        $note = vsprintf('更新成功', $_POST['brand_name']);
        sys_msg($note, 0, $link);
    }
    else
    {
        die($db->error());
    }
}

/*------------------------------------------------------ */
//-- 编辑品牌名称
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_brand_name')
{
    check_authz_json('brand_manage');

    $id     = intval($_POST['id']);
    $name   = json_str_iconv(trim($_POST['val']));

    /* 检查名称是否重复 */
    if ($exc->num("brand_name",$name, $id) != 0)
    {
        make_json_error(sprintf($_LANG['brandname_exist'], $name));
    }
    else
    {
        if ($exc->edit("brand_name = '$name'", $id))
        {
            admin_log($name,'edit','brand');
            make_json_result(stripslashes($name));
        }
        else
        {
            make_json_result(sprintf($_LANG['brandedit_fail'], $name));
        }
    }
}

elseif($_REQUEST['act'] == 'add_brand')
{
    $brand = empty($_REQUEST['brand']) ? '' : json_str_iconv(trim($_REQUEST['brand']));

    if(brand_exists($brand))
    {
        make_json_error($_LANG['brand_name_exist']);
    }
    else
    {
        $sql = "INSERT INTO " . $ecs->table('deposit') . "(brand_name)" .
               "VALUES ( '$brand')";

        $db->query($sql);
        $brand_id = $db->insert_id();

        $arr = array("id"=>$brand_id, "brand"=>$brand);

        make_json_result($arr);
    }
}
/*------------------------------------------------------ */
//-- 编辑排序序号
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_sort_order')
{
    check_authz_json('brand_manage');

    $id     = intval($_POST['id']);
    $order  = intval($_POST['val']);
    $name   = $exc->get_name($id);

    if ($exc->edit("sort_order = '$order'", $id))
    {
        admin_log(addslashes($name),'edit','brand');

        make_json_result($order);
    }
    else
    {
        make_json_error(sprintf($_LANG['brandedit_fail'], $name));
    }
}
/*------------------------------------------------------ */
//-- 切换是否显示
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_show')
{
    check_authz_json('brand_manage');

    $id     = intval($_POST['id']);
    $val    = intval($_POST['val']);

    $exc->edit("is_show='$val'", $id);

    make_json_result($val);
}
/*------------------------------------------------------ */
//-- 删除品牌
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('brand_manage');

    $group_id = intval($_GET['id']);

    $exc->drop($group_id);

    $url = 'after_pay_group.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}
/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $brand_list = afterPayGroupList();
    $smarty->assign('list',   $brand_list['brand']);
    $smarty->assign('filter',       $brand_list['filter']);
    $smarty->assign('record_count', $brand_list['record_count']);
    $smarty->assign('page_count',   $brand_list['page_count']);

    make_json_result($smarty->fetch('after_pay_group.htm'), '',
        array('filter' => $brand_list['filter'], 'page_count' => $brand_list['page_count']));
}


function afterPayGroupList()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 分页大小 */
        $filter = array();

        /* 记录总数以及页数 */
        if (isset($_POST['brand_name']))
        {
            $sql = "SELECT COUNT(*) FROM after_pay_group " ;
        }
        else
        {
            $sql = "SELECT COUNT(*) FROM after_pay_group ";
        }

        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);

        /* 查询记录 */
        if (isset($_POST['brand_name']))
        {  
            if(strtoupper(EC_CHARSET) == 'GBK')
            {
                $keyword = iconv("UTF-8", "gb2312", $_POST['brand_name']);
            }
            else
            {
                $keyword = $_POST['brand_name'];
            }
            
            $sql =  " SELECT d.*,g.title FROM after_pay_group d 
            		LEFT JOIN edo_user_group g ON d.group_id=g.user_group_id ".
                    " WHERE  g.title like '%{$keyword}%' ".
                    " ORDER BY d.group_id ASC" ;
        }
        else
        {
            $sql = "SELECT d.*,g.title FROM after_pay_group d 
            		LEFT JOIN edo_user_group g ON d.group_id=g.user_group_id
            		 " ;
        }
        // echo $sql;
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
        $groupIds[]= $rows['group_id'];
        $rows['limit_money'] = $rows['limit_money']/100;
        $arr[] = $rows;
    }

    $amount=getGroupAmountByGroupIds($groupIds);
    foreach ($arr as $k => $v) {
        $arr[$k]['amount'] = $amount[$v['group_id']]/100;
    }
    //print_r($arr);
    return array('brand' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);


}

function getGroupAmountByGroupIds($groupIds)
{
    global $db;
    if (empty($groupIds)) {
        return;
    }

    $groupIdStr = implode(',', $groupIds);
    $sql = " SELECT u.user_group_id,SUM(ca.amount) -SUM(ca.freeze_money) as amount 
                FROM edo_cash_account ca
                LEFT JOIN edo_user u ON u.uid=ca.user_id 
                where u.user_group_id in ($groupIdStr) 
                GROUP BY u.user_group_id ";
    $ret=$db->getAll($sql);
    $amount = array();
    foreach ($ret as $v) {
        $amount[$v['user_group_id']] = $v['amount'];
    }
    // $amount = $ret['amount']-$ret['freeze_money'];
    
    return $amount;
}

 ?>