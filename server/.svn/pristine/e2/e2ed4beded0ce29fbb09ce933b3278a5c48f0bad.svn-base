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
 * $Id: deposit_violate.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);

$exc = new exchange($ecs->table("deposit_violate"), $db, 'id', 'brand_name');

/*------------------------------------------------------ */
//-- 品牌列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    $smarty->assign('ur_here',      '特定违章押金策略列表');
    $smarty->assign('action_link',  array('text' => '添加 特定违章押金策略', 'href' => 'deposit_violate.php?act=add'));
    $smarty->assign('full_page',    1);

    $brand_list = get_depositlist();

    $smarty->assign('brand_list',   $brand_list['brand']);
    $smarty->assign('filter',       $brand_list['filter']);
    $smarty->assign('record_count', $brand_list['record_count']);
    $smarty->assign('page_count',   $brand_list['page_count']);

    assign_query_info();
    $smarty->display('deposit_violate_list.htm');
}


elseif ($_REQUEST['act'] == 'getRegions')
{
    $regions = $db->getAll("select * from ".$ecs->table('region')." where parent_id=".$_POST['region_id']);
    $result = array();
    $result['obj'] = $regions ;

    $result['region_type'] = $_REQUEST['region_type'] ;
    if ($result['region_type']<2) {
        make_json_result($result);
    }
    
}
elseif ($_REQUEST['act'] == 'add')
{
    $smarty->assign('ur_here',     '增加 特定违章押金 策略');
    $smarty->assign('action_link', array('text' => '特定违章押金策略列表', 'href' => 'deposit_violate.php?act=list&' . list_link_postfix()));
	require_once ROOT_PATH . 'includes/lib_user.php' ;
	$group = getUserGroup();
	$usertype = getUserType();
    assignRegion();
	$smarty->assign("group", $group);
	$smarty->assign("usertype", $usertype);
	$smarty->assign("form_action", "insert");
	$smarty->display('deposit_violate_info.htm');
}
elseif ($_REQUEST['act'] == 'insert')
{
 /*检查品牌名是否重复*/
    admin_priv('brand_manage');
    /*插入数据*/
    $sql = " SELECT COUNT(*) FROM ".$ecs->table('deposit_violate').
           " WHERE  city_id = '$_POST[city]' ".
           " AND user_type_id='$_POST[user_type_id]'".
           " AND group_id='$_POST[group_id]'";
    /*判断是否已存在该违章押金策略*/
    if ($db->getOne($sql) < 1)
    {
        $data=array(
            'city_id' =>$_POST['city'] ,
            'user_type_id' =>$_POST['user_type_id'] ,
            'group_id' =>$_POST['group_id'],
            'adjust_money' =>$_POST['adjust_money']*100
             );
        $db->autoExecute($ecs->table('deposit_violate'),$data);
        admin_log($db->insert_id(),'add','deposit_violate');

        /* 清除缓存 */
        clear_cache_files();

        $link[1]['text'] = '继续添加';
        $link[1]['href'] = 'deposit_violate.php?act=add';

        $link[0]['text'] = '返回违章押金列表';
        $link[0]['href'] = 'deposit_violate.php?act=list';

        sys_msg('添加成功', 1, $link);
    }
    else
    {
        $link[1]['text'] = '重新添加';
        $link[1]['href'] = 'deposit_violate.php?act=add';

        $link[0]['text'] = '返回违章押金列表';
        $link[0]['href'] = 'deposit_violate.php?act=list';
        sys_msg('添加违章押金策略失败,因为已存在该策略', 1, $link);
    }
}

/*------------------------------------------------------ */
//-- 编辑品牌
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    /* 权限判断 */
    admin_priv('brand_manage');
    $sql = "SELECT * ".
            "FROM " .$ecs->table('deposit_violate'). " WHERE id='$_REQUEST[id]'";
    $brand = $db->GetRow($sql);
    $brand['adjust_money']=$brand['adjust_money']/100;
    assignRegion();
    $regionTree = getRegionTree($brand['city_id']) ;
    $smarty->assign( 'region_tree',     $regionTree ) ;
    $smarty->assign('ur_here',     '编辑 特定违章押金 策略');
    $smarty->assign('action_link', array('text' => '特定违章押金策略列表', 'href' => 'deposit_violate.php?act=list&' . list_link_postfix()));
	
    $smarty->assign('brand',       $brand);
    $smarty->assign('form_action', 'updata');
	require_once ROOT_PATH . 'includes/lib_user.php' ;
	$group = getUserGroup();
	$usertype = getUserType();
	$smarty->assign("station", $stations);
	$smarty->assign("group", $group);
	$smarty->assign("usertype", $usertype);

    assign_query_info();
    $smarty->display('deposit_violate_info.htm');
}

elseif ($_REQUEST['act'] == 'updata')
{
    admin_priv('brand_manage');
    $data=array(
            'city_id' =>$_POST['city'] ,
            'group_id' =>$_POST['group_id'] ,
            'user_type_id' =>$_POST['user_type_id'] ,
            'adjust_money' =>$_POST['adjust_money']*100
             );
    //$db->autoExecute($ecs->table('deposit_violate_city'),$data,'update','id=$_POST[city]');
    if ($db->autoExecute($ecs->table('deposit_violate'),$data,'update',"id=$_POST[id]"))
    {
        /* 清除缓存 */
        clear_cache_files();
        admin_log($_POST['id'], 'edit', 'deposit_violate');

        $link[0]['text'] = '添加 特殊 违章押金策略成功';
        $link[0]['href'] = 'deposit_violate.php?act=list&' . list_link_postfix();
        $note = vsprintf($_LANG['brandedit_succed'], $_POST['brand_name']);
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
    
    $id = intval($_GET['id']);
    $exc->drop($id);

    /* 更新商品的品牌编号 */

    $url = 'deposit_violate.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

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

    make_json_result($smarty->fetch('deposit_violate_list.htm'), '',
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
            $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('deposit_violate') ;
        }
        else
        {
            $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('deposit_violate');
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
            
            $sql = "SELECT d.id,d.adjust_money,r.region_name,g.title,t.name as user_type FROM ".$GLOBALS['ecs']->table('deposit_violate')." d
                    left join ".$GLOBALS['ecs']->table('region')." r on r.region_id=d.city_id
                    left join ".$GLOBALS['sns']->table('user_group')." g on g.user_group_id=d.group_id
                    left join ".$GLOBALS['sns']->table('user_type')." t on t.id=d.user_type_id".
                   " WHERE t.name like '%{$keyword}%' or r.region_name like '%{$keyword}%' 
                    or g.title like '%{$keyword}%'  ORDER BY d.id ASC" ;
        }
        else
        {
            $sql = "SELECT d.id,d.adjust_money,r.region_name,g.title,d.user_type_id FROM ".$GLOBALS['ecs']->table('deposit_violate')." d
					left join ".$GLOBALS['ecs']->table('region')." r on r.region_id=d.city_id
					left join ".$GLOBALS['sns']->table('user_group')." g on g.user_group_id=d.group_id";
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
      if ($rows['user_type_id']==0) {
            $rows['user_type']='个人';
        }
        else if ($rows['user_type_id']==1) {
            $rows['user_type']='学生';
        }
        else if ($rows['user_type_id']==-1) {
            $rows['user_type']='所有';
        }
        $rows['adjust_money']=$rows['adjust_money']/100;
        $arr[] = $rows;

    }

    return array('brand' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

?>
