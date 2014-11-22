<?php

/**
 * ECSHOP 管理中心品牌管理
 * ============================================================================
 * 版权所有 2005-2010 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: deposit.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);

$exc = new exchange($ecs->table("deposit"), $db, 'id', 'brand_name');

/*------------------------------------------------------ */
//-- 品牌列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    $smarty->assign('ur_here',      $_LANG['01_deposit_config']);
    $smarty->assign('action_link',  array('text' => $_LANG['add_deposit'], 'href' => 'deposit.php?act=add'));
    $smarty->assign('full_page',    1);

    $brand_list = get_depositlist();

    $smarty->assign('brand_list',   $brand_list['brand']);
    $smarty->assign('filter',       $brand_list['filter']);
    $smarty->assign('record_count', $brand_list['record_count']);
    $smarty->assign('page_count',   $brand_list['page_count']);

    assign_query_info();
    $smarty->display('deposit_list.htm');
}

/*------------------------------------------------------ */
//-- 添加品牌
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'config')
{
    if($_POST)
    {
        foreach($_POST as $k=>$v)
        {
            if(in_array($k, array('hour_km','day_km')))
            {
                $db->query("update ".$ecs->table("shop_config")." set value='$v' where code='$k'");
            }
        }
        admin_log('order_config', 'edit', 'order_config');
        
        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = '?act=config';
        $note = $_LANG['configedit_succed'];
        sys_msg($note, 0, $link);
    }
    else
    {

        $res = $db->getAll("select * from ".$ecs->table("shop_config")." where code in ('hour_km','day_km')");
		
        foreach($res as $k=>$v)
        {
            $smarty->assign($v['code'],$v['value']) ;
        }
        $smarty->display('deposit_config.htm');
    }
}
elseif ($_REQUEST['act'] == 'add')
{
    $smarty->assign('action_link', array('text' => $_LANG['01_deposit_config'], 'href' => 'deposit.php?act=list&' . list_link_postfix()));
	require_once ROOT_PATH . 'includes/lib_car.php' ;
	$stations = getStations();

	require_once ROOT_PATH . 'includes/lib_user.php' ;
	$group = getUserGroup();
	$usertype = getUserType();
	$smarty->assign("station", $stations);
	$smarty->assign("group", $group);
	$smarty->assign("usertype", $usertype);
	$smarty->assign("form_action", "insert");
	$smarty->display('deposit_info.htm');
}
elseif ($_REQUEST['act'] == 'insert')
{
    /*检查品牌名是否重复*/
    admin_priv('brand_manage');


    /*插入数据*/
    // $_POST['deposit']=$_POST['deposit']*100;
    $sql = "INSERT INTO ".$ecs->table('deposit')."(station_id, group_id, user_type_id, deposit) ".
           "VALUES ('$_POST[station_id]', '$_POST[group_id]', '$_POST[user_type_id]', '$_POST[deposit]')";
    $db->query($sql);

    admin_log($db->insert_id(),'add','deposit');

    /* 清除缓存 */
    clear_cache_files();

    $link[0]['text'] = '继续添加';
    $link[0]['href'] = 'deposit.php?act=add';

    $link[1]['text'] = '返回列表';
    $link[1]['href'] = 'deposit.php?act=list';

    sys_msg('添加成功', 0, $link);
}

/*------------------------------------------------------ */
//-- 编辑品牌
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    /* 权限判断 */
    admin_priv('brand_manage');
    $sql = "SELECT * ".
            "FROM " .$ecs->table('deposit'). " WHERE id='$_REQUEST[id]'";
    $brand = $db->GetRow($sql);

    $smarty->assign('ur_here',     $_LANG['brand_edit']);
    $smarty->assign('action_link', array('text' => $_LANG['01_deposit_config'], 'href' => 'deposit.php?act=list&' . list_link_postfix()));
	
    $smarty->assign('brand',       $brand);
    $smarty->assign('form_action', 'updata');

	require_once ROOT_PATH . 'includes/lib_car.php' ;
	$stations = getStations();

	require_once ROOT_PATH . 'includes/lib_user.php' ;
	$group = getUserGroup();
	$usertype = getUserType();
	$smarty->assign("station", $stations);
	$smarty->assign("group", $group);
	$smarty->assign("usertype", $usertype);

    assign_query_info();
    $smarty->display('deposit_info.htm');
}
elseif ($_REQUEST['act'] == 'updata')
{
    admin_priv('brand_manage');
  
    // $_POST['deposit']=$_POST['deposit']*100;
    $param = "station_id = '$_POST[station_id]',  group_id='$_POST[group_id]', user_type_id='$_POST[user_type_id]', deposit='$_POST[deposit]'";

    if ($exc->edit($param,  $_POST['id']))
    {
        /* 清除缓存 */
        clear_cache_files();
        admin_log($_POST['id'], 'edit', 'brand');

        $link[0]['text'] = '返回列表';
        $link[0]['href'] = 'deposit.php?act=list&' . list_link_postfix();
        $note = vsprintf('添加成功', $_POST['brand_name']);
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

    $id = intval($_GET['id']);

    /* 删除该品牌的图标 */
    $sql = "SELECT COUNT(*) FROM " .$ecs->table('deposit'). " WHERE id = '$id'";
    $logo_name = $db->getOne($sql);
    if (!empty($logo_name))
    {
        @unlink(ROOT_PATH . DATA_DIR . '/brandlogo/' .$logo_name);
    }

    $exc->drop($id);

    $url = 'deposit.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}


/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $brand_list = get_depositlist();
    $smarty->assign('brand_list',   $brand_list['brand']);
    $smarty->assign('filter',       $brand_list['filter']);
    $smarty->assign('record_count', $brand_list['record_count']);
    $smarty->assign('page_count',   $brand_list['page_count']);

    make_json_result($smarty->fetch('deposit_list.htm'), '',
        array('filter' => $brand_list['filter'], 'page_count' => $brand_list['page_count']));
}

/**
 * 获取品牌列表
 *
 * @access  public
 * @return  array
 */
function get_depositlist()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 分页大小 */
        $filter = array();

        /* 记录总数以及页数 */
        if (isset($_POST['brand_name']))
        {
            $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('deposit') ;
        }
        else
        {
            $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('deposit');
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
            
            $sql =  " SELECT d.id,d.deposit,s.name as station_name,g.title,t.name as user_type,d.user_type_id ,d.group_id FROM ".$GLOBALS['ecs']->table('deposit').' d '.
                    " left join ".$GLOBALS['ecs']->table('station')." s on s.id=d.station_id
                    left join ".$GLOBALS['sns']->table('user_group')." g on g.user_group_id=d.group_id
                    left join ".$GLOBALS['sns']->table('user_type')." t on t.id=d.user_type_id".
                    " WHERE s.name like '%{$keyword}%' "." OR g.title like '%{$keyword}%' "." OR t.name like '%{$keyword}%' ".
                    " ORDER BY d.id ASC" ;
        }
        else
        {
            $sql = "SELECT d.id,d.deposit,s.name as station_name,g.title,t.name as user_type,d.user_type_id,d.group_id FROM ".$GLOBALS['ecs']->table('deposit')." d
					left join ".$GLOBALS['ecs']->table('station')." s on s.id=d.station_id
					left join ".$GLOBALS['sns']->table('user_group')." g on g.user_group_id=d.group_id
					left join ".$GLOBALS['sns']->table('user_type')." t on t.id=d.user_type_id" ;
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
        if ($rows['group_id']==-1) {
            $rows['title']="所有";
        }

        if ($rows['user_type_id']==-1) {
            $rows['user_type']="所有";
        }
        $arr[] = $rows;
    }
    //print_r($arr);
    return array('brand' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

?>
