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
 * $Id: station.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);

$exc = new exchange($ecs->table("station"), $db, 'id', 'name');

/*------------------------------------------------------ */
//-- 品牌列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    // print_r(stationFollowSum());
    // die();
    $smarty->assign('ur_here',      $_LANG['06_goods_brand_list']);
    $smarty->assign('action_link',  array('text' => $_LANG['add_station'], 'href' => 'station.php?act=add'));
    $smarty->assign('full_page',    1) ;

    $brand_list = get_stationlist() ;

    $smarty->assign('brand_list',   $brand_list['brand']);
    $smarty->assign('filter',       $brand_list['filter']);
    $smarty->assign('record_count', $brand_list['record_count']);
    $smarty->assign('page_count',   $brand_list['page_count']);

    assign_query_info();
    $smarty->display('station_list.htm');
}

/*------------------------------------------------------ */
//-- 添加品牌
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    /* 权限判断 */
    admin_priv('brand_manage');

    $smarty->assign('ur_here',     $_LANG['add_station']);
    $smarty->assign('action_link', array('text' => $_LANG['06_goods_brand_list'], 'href' => 'station.php?act=list'));
    $smarty->assign('form_action', 'insert');

    assign_query_info();
	assignRegion();
    $smarty->assign('brand', array('sort_order'=>50, 'is_show'=>1));
    $smarty->display('station_info.htm');
}
elseif ($_REQUEST['act'] == 'insert')
{
    /*检查品牌名是否重复*/
    admin_priv('brand_manage');

    $longitude=$POST['longitude']*1000000;
    $latitude=$POST['latitude']*1000000;

	$sql = "insert into ".$ecs->table("station")." set 
				name='$POST[name]',
				longitude='$longitude',
				latitude='$latitude',
				radius='$POST[radius]',
				city='$POST[city]',
				is_show='$POST[is_show]'
				" ;
    $db->query($sql);

    admin_log($_POST['name'], 'add', 'station');

    /* 清除缓存 */
    clear_cache_files();

    $link[0]['text'] = $_LANG['continue_add'];
    $link[0]['href'] = 'station.php?act=add';

    $link[1]['text'] = $_LANG['back_list'];
    $link[1]['href'] = 'station.php?act=list';

    sys_msg($_LANG['brandadd_succed'], 0, $link);
}

/*------------------------------------------------------ */
//-- 编辑品牌
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    /* 权限判断 */
    admin_priv('brand_manage') ;
    $sql = "SELECT * " .
           "FROM " . $ecs->table('station') . " WHERE id='$_REQUEST[id]'" ;
    $station = $db->GetRow( $sql ) ;
    $station['longitude']=$station['longitude']/1000000;
    $station['latitude']=$station['latitude']/1000000;
    
	$regionTree = getRegionTree($station['city']) ;
	$smarty->assign( 'region_tree',     $regionTree ) ;

	//print_r( $regionTree ) ;
    $smarty->assign( 'ur_here', $_LANG['brand_edit'] ) ;
    $smarty->assign( 'action_link', array( 'text' => $_LANG['06_goods_brand_list'], 'href' => 'station.php?act=list&' . list_link_postfix()) ) ;
    $smarty->assign('brand',       $station ) ;
    $smarty->assign('form_action', 'updata') ;

	assignRegion();
    assign_query_info() ;
    $smarty->display('station_info.htm') ;
}
elseif ($_REQUEST['act'] == 'updata')
{
    admin_priv('brand_manage');
    $is_show = isset( $_REQUEST['is_show'] ) ? intval( $_REQUEST['is_show'] ) : 0 ;

    $longitude=$POST['longitude']*1000000;
    $latitude=$POST['latitude']*1000000;
	/*处理URL*/
    if ($exc->edit( "name='$_POST[name]', longitude='$longitude', latitude='$latitude', radius='$_POST[radius]', city='$_POST[city]', sort_order='$_POST[sort_order]', is_show='$_POST[is_show]'",  $_POST['id'] ))
    {
        /* 清除缓存 */
        clear_cache_files() ;
        admin_log($_POST['name'], 'edit', 'station') ;
        $link[0]['text'] = $_LANG['back_list'] ;
        $link[0]['href'] = 'station.php?act=list&' . list_link_postfix() ;
        $note = vsprintf( $_LANG['brandedit_succed'], $_POST['name'] ) ;
        sys_msg( $note, 0, $link ) ;
    }
    else
    {
        die( $db->error() ) ;
    }
}

/*------------------------------------------------------ */
//-- 编辑品牌名称
/*------------------------------------------------------ */
elseif ( $_REQUEST['act'] == 'edit_brand_name' )
{
    check_authz_json('brand_manage') ;

    $id     = intval($_POST['id']) ;
    $name   = json_str_iconv( trim( $_POST['val'] ) ) ;

    /* 检查名称是否重复 */
    if ($exc->num( "brand_name", $name, $id) != 0 )
    {
        make_json_error(sprintf($_LANG['brandname_exist'], $name));
    }
    else
    {
        if ($exc->edit("brand_name = '$name'", $id))
        {
            admin_log( $name, 'edit', 'brand' );
            make_json_result( stripslashes($name) );
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
        $sql = "INSERT INTO " . $ecs->table('station') . "(brand_name)" .
               "VALUES ( '$brand')";

        $db->query($sql);
        $brand_id = $db->insert_id();

        $arr = array( "id"=>$brand_id, "brand"=>$brand ) ;

        make_json_result($arr) ;
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
	//trace($id."S");
    if ($exc->edit("sort_order = '$order'", $id))
    {
        admin_log( addslashes( $name ),'edit','brand' );
        make_json_result( $order ) ;
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
    check_authz_json('station');

    $id = intval($_GET['id']);
    $sql = 'DELETE FROM ' .$ecs->table('station'). " WHERE id = '$id'";
    $row = $db->query($sql);
  
    if($row>0)
    {
    	echo "移除成功！";
    	 
    	 
    }else
    {
    	echo "移除失败！";
    }

   /* 删除该品牌的图标 */
//     $sql = "SELECT brand_logo FROM " .$ecs->table('station'). " WHERE brand_id = '$id'";
//     $logo_name = $db->getOne($sql);
//     if (!empty($logo_name))
//     {
//         @unlink(ROOT_PATH . DATA_DIR . '/brandlogo/' .$logo_name);
//     }

//     $exc->drop($id);

//     /* 更新商品的品牌编号 */
//     $sql = "UPDATE " .$ecs->table('goods'). " SET brand_id=0 WHERE brand_id='$id'";
//     $db->query($sql);

    $url = 'station.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 删除品牌图片
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_logo')
{
    /* 权限判断 */
    admin_priv('brand_manage');
    $brand_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    /* 取得logo名称 */
    $sql = "SELECT brand_logo FROM " .$ecs->table('station'). " WHERE brand_id = '$brand_id'";
    $logo_name = $db->getOne($sql);

    if (!empty($logo_name))
    {
        @unlink(ROOT_PATH . DATA_DIR . '/brandlogo/' .$logo_name);
        $sql = "UPDATE " .$ecs->table('station'). " SET brand_logo = '' WHERE brand_id = '$brand_id'";
        $db->query($sql);
    }
    $link= array(array('text' => $_LANG['brand_edit_lnk'], 'href' => 'station.php?act=edit&id=' . $brand_id), array('text' => $_LANG['brand_list_lnk'], 'href' => 'station.php?act=list'));
    sys_msg($_LANG['drop_brand_logo_success'], 0, $link);
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $brand_list = get_stationlist();
    $smarty->assign('brand_list',   $brand_list['brand']);
    $smarty->assign('filter',       $brand_list['filter']);
    $smarty->assign('record_count', $brand_list['record_count']);
    $smarty->assign('page_count',   $brand_list['page_count']);

    make_json_result($smarty->fetch('station_list.htm'), '',
        array('filter' => $brand_list['filter'], 'page_count' => $brand_list['page_count']));
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

/**
 * 获取品牌列表
 *
 * @access  public
 * @return  array
 */
function get_stationlist()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 分页大小 */
        $filter = array();

        /* 记录总数以及页数 */
        if ( isset( $_POST['brand_name'] ) )
        {
            $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('station') .' WHERE name = \''.$_POST['brand_name'].'\'' ;
        }
        else
        {
            $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('station');
        }

        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter) ;

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
            $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('station')." s
					LEFT JOIN ".$GLOBALS['ecs']->table('region')." r on s.city=r.region_id WHERE name like '%{$keyword}%' ORDER BY sort_order ASC" ;
        }
        else
        {
            $sql = "SELECT s.*,r.region_name FROM ".$GLOBALS['ecs']->table('station')." s
					LEFT JOIN ".$GLOBALS['ecs']->table('region')." r on s.city=r.region_id
					ORDER BY sort_order ASC" ;
        }
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
	//echo $sql."<BR>";
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    $arr = array();
    $stationFollows = stationFollowSum();
    while ($rows = $GLOBALS['db']->fetchRow($res))
    {

        $rows['follow'] = isset($stationFollows[$rows['id']]) ? $stationFollows[$rows['id']] : 0;
        $rows['longitude']=$rows['longitude']/1000000;
        $rows['latitude']=$rows['latitude']/1000000;
        $arr[] = $rows;
    }

    return array('brand' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

function stationFollowSum()
{
    global $db;
    global $sns;
    global $ecs;
    // " SELECT s.id,count(1) as station_sum FROM ".$ecs->table('car').' c '.
    // " LEFT JOIN ".$ecs->table('station').' s '.
    // " ON c.station=s.id ";



    $sql =  " SELECT f.station, COUNT( f.user_id ) as sum FROM (

            SELECT f.user_id AS user_id, c.station AS station, COUNT( c.id ) AS sum
            FROM ".$sns->table('car_follow').' f '.
            " LEFT JOIN ".$ecs->table('car')." c ON f.car_id = c.id
            GROUP BY f.user_id
            ) AS f
            GROUP BY f.station ";
    $ret = $db->getAll($sql);
    $stationSum=array();
    foreach ($ret as $v) {
        $stationSum[$v['station']] = $v['sum'];
    }

    return  $stationSum;
}












?>
