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
define(desposit_violate, 2);
require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);

$exc = new exchange($ecs->table("deposit_violate_city"), $db, 'id', 'city_id');

/*------------------------------------------------------ */
//-- 品牌列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    $smarty->assign('ur_here',      '违章押金策略列表');
    $smarty->assign('action_link',  array('text' => '添加违章押金策略', 'href' => 'deposit_violate_city.php?act=add'));
    $smarty->assign('full_page',    1);

    $brand_list = get_depositlist();

    $smarty->assign('brand_list',   $brand_list['brand']);
    $smarty->assign('filter',       $brand_list['filter']);
    $smarty->assign('record_count', $brand_list['record_count']);
    $smarty->assign('page_count',   $brand_list['page_count']);

    assign_query_info();
    $smarty->display('deposit_violate_city_list.htm');
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
    $smarty->assign('ur_here',     '添加违章押金策略');
    $smarty->assign('action_link', array('text' => '返回 违章押金 策略列表', 'href' => 'deposit_violate_city.php?act=list&' . list_link_postfix()));
    //require_once ROOT_PATH . 'includes/lib_car.php' ;
    //$stations = getStations();
    assignRegion();
    //require_once ROOT_PATH . 'includes/lib_user.php' ;
    //$group = getUserGroup();
    //$usertype = getUserType();
    //$smarty->assign("station", $stations);
    //$smarty->assign("group", $group);
    //$smarty->assign("usertype", $usertype);
    $smarty->assign("form_action", "insert");
    $smarty->display('deposit_violate_city_info.htm');
}
elseif ($_REQUEST['act'] == 'insert')
{
    /*检查品牌名是否重复*/
    admin_priv('brand_manage');
    /*插入数据*/
    $sql = " SELECT COUNT(*) FROM ".$ecs->table('deposit_violate_city').
           " WHERE  city_id = '$_POST[city]'";
    
    /*判断是否已存在该违章押金策略*/
    if ($db->getOne($sql) < 1)
    {
        $data=array(
            'city_id' =>$_POST['city'] ,
            'deposit' =>$_POST['deposit']*100
             );
        $db->autoExecute($ecs->table('deposit_violate_city'),$data);
        admin_log($db->insert_id(),'add','deposit_violate_city');

        /* 清除缓存 */
        clear_cache_files();

        $link[1]['text'] = '继续添加';
        $link[1]['href'] = 'deposit_violate_city.php?act=add';

        $link[0]['text'] = '返回违章押金列表';
        $link[0]['href'] = 'deposit_violate_city.php?act=list';

        sys_msg('添加成功', 1, $link);
    }
    else
    {
        $link[1]['text'] = '重新添加';
        $link[1]['href'] = 'deposit_violate_city.php?act=add';

        $link[0]['text'] = '返回违章押金列表';
        $link[0]['href'] = 'deposit_violate_city.php?act=list';
        sys_msg('添加违章押金策略失败,因为已存在该策略', 1, $link);
    }



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
/*------------------------------------------------------ */
//-- 编辑品牌
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    /* 权限判断 */
    admin_priv('brand_manage');
    $smarty->assign('ur_here',     '编辑 违章押金 策略');
    $smarty->assign('action_link', array('text' => '返回 违章押金 策略列表', 'href' => 'deposit_violate_city.php?act=list&' . list_link_postfix()));
    $sql = "SELECT * ".
            "FROM " .$ecs->table('deposit_violate_city'). " WHERE id='$_REQUEST[id]'";
    $brand = $db->GetRow($sql);
    $brand['deposit']=$brand['deposit']/100;
    $smarty->assign('ur_here',   '编辑违章押金策略');
    //$smarty->assign('action_link', array('text' => $_LANG['01_deposit_config'], 'href' => 'deposit_violate.php?act=list&' . list_link_postfix()));
    $regionTree = getRegionTree($brand['city_id']) ;
    assignRegion();
    $smarty->assign( 'region_tree',     $regionTree ) ;
    $smarty->assign('brand',       $brand);
    $smarty->assign('form_action', 'updata');

    assign_query_info();
    $smarty->display('deposit_violate_city_info.htm');
}
elseif ($_REQUEST['act'] == 'updata')
{
    admin_priv('brand_manage');
  

    $param = "city_id = '$_POST[city]', deposit='$_POST[deposit]'";

    $data=array(
            'city_id' =>$_POST['city'] ,
            'deposit' =>$_POST['deposit']*100
             );
    //$db->autoExecute($ecs->table('deposit_violate_city'),$data,'update','id=$_POST[city]');
    if ($db->autoExecute($ecs->table('deposit_violate_city'),$data,'update',"id=$_POST[id]"))
    {
        /* 清除缓存 */
        clear_cache_files();
        admin_log($_POST['id'], 'edit', 'brand');

        $link[0]['text'] = '添加违章押金策略成功';
        $link[0]['href'] = 'deposit_violate_city.php?act=list&' . list_link_postfix();
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
//-- 删除
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    //check_authz_json('brand_manage');

    $id = intval($_GET['id']);
    $exc->drop($id);

    /* 更新商品的品牌编号 */

    $url = 'deposit_violate_city.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

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

    make_json_result($smarty->fetch('deposit_violate_city_list.htm'), '',
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
            $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('deposit_violate_city') ;
        }
        else
        {
            $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('deposit_violate_city');
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
            $sql =  " SELECT d.id,d.deposit,r.region_name FROM ".$GLOBALS['ecs']->table('deposit_violate_city').' d '.
                    " LEFT JOIN ".$GLOBALS['ecs']->table('region')." r on r.region_id=d.city_id ".
                    " WHERE r.region_name like '%{$keyword}%'  or d.deposit like '%{$keyword}%'
                        ORDER BY d.id ASC" ;
        }
        else
        {
            $sql = " SELECT d.id,d.deposit,r.region_name FROM ".$GLOBALS['ecs']->table('deposit_violate_city')." d
                    LEFT JOIN ".$GLOBALS['ecs']->table('region')." r on r.region_id=d.city_id ";
                   
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
        $rows['deposit']=$rows['deposit']/100;
        $arr[] = $rows;
    }

    return array('brand' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

?>
