<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);

$exc = new exchange($ecs->table("sim_card"), $db, 'id', 'phone_number');
/*------------------------------------------------------ */
//-- sim卡列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    //echo $_LANG['06_equite_list'];
    $smarty->assign( 'ur_here',      $_LANG['05_sim_card_list']);
    $smarty->assign( 'action_link',  array('text' => $_LANG['05_sim_card_add'], 'href' => 'sim_card.php?act=add'));
    $smarty->assign( 'full_page',    1);
    
    $brand_list = get_brandlist();
    //print_r($brand_list['brand']);
    $smarty->assign('brand_list',   $brand_list['brand']);
    $smarty->assign('filter', $brand_list['filter']);
    $smarty->assign('record_count', $brand_list['record_count']);
    $smarty->assign('page_count',   $brand_list['page_count']);
    assign_query_info();
    $smarty->display('sim_card_list.htm');
}

/*------------------------------------------------------ */
//-- 添加sim卡
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    /* 权限判断 */
    admin_priv('brand_manage');
    $smarty->assign('ur_here',     $_LANG['05_sim_card_add']);
    $smarty->assign('action_link', array('text' => $_LANG['05_sim_card_list'], 'href' => 'sim_card.php?act=list'));
    $smarty->assign('form_action', 'insert');
    assign_query_info();
    $smarty->assign('brand', array('sort_order'=>50, 'is_show'=>1));
    $smarty->display('sim_card_info.htm');
}
elseif ($_REQUEST['act'] == 'insert')
{
    /*检查sim卡名是否重复*/
    admin_priv('brand_manage');

    $status = isset($_REQUEST['status']) ? intval($_REQUEST['status']) : 0;

    /*插入数据*/

    $sql = "INSERT INTO ".$ecs->table('sim_card')."(phone_number, provider, status, service_password, login_password,phone_location,sort_order) ".
           "VALUES ('$_POST[phone_number]', '$_POST[provider]', $status,  '$_POST[service_password]',  '$_POST[login_password]', '$_POST[location]','$_POST[sort_order]')";
    $db->query($sql);

    admin_log($_POST['phone_number'],'add','sim_card');

    /* 清除缓存 */
    clear_cache_files();

    $link[0]['text'] = $_LANG['continue_add'] ;
    $link[0]['href'] = 'sim_card.php?act=add' ;

    $link[1]['text'] = $_LANG['back_list'] ;
    $link[1]['href'] = 'sim_card.php?act=list';

    sys_msg($_LANG['brandadd_succed'], 0, $link);
}

/*------------------------------------------------------ */
//-- 编辑sim卡
/*------------------------------------------------------ */
elseif($_REQUEST['act']=='edit')
{
     /* 权限判断 */
     
    admin_priv('brand_manage');
    $sql = "SELECT * ".
            "FROM " .$ecs->table('sim_card'). " WHERE id='$_REQUEST[id]'";
    $brand = $db->GetRow($sql);
    //print_r($sql);
    $smarty->assign('ur_here',     $_LANG['brand_edit']);
    $smarty->assign('action_link', array('text' => $_LANG['05_sim_card_list'], 'href' => 'sim_card.php?act=list&' . list_link_postfix()));
    $smarty->assign('brand',       $brand);
    $smarty->assign('form_action', 'updata');

    assign_query_info();
   
    $smarty->display('sim_card_info.htm');
}


elseif ($_REQUEST['act'] == 'updata')
{
    admin_priv('brand_manage');

    /*对描述处理*/
    if (!empty($_POST['brand_desc']))
    {
        $_POST['brand_desc'] = $_POST['brand_desc'];
    }

    $is_show = isset($_REQUEST['staus']) ? intval($_REQUEST['is_show']) : 0;

    /* 处理图片 */
    $img_name = basename($image->upload_image($_FILES['brand_logo'],'brandlogo'));
    $param = "id = '$_POST[id]',phone_number = '$_POST[phone_number]',  provider='$_POST[provider]', status='$_POST[status]',  service_password='$_POST[service_password]', login_password='$_POST[login_password]', phone_location = '$_POST[location]' ,sort_order='$_POST[sort_order]' ";
 

    if ($exc->edit($param,  $_POST['id']))
    {
        /* 清除缓存 */
        clear_cache_files();

        admin_log($_POST['brand_name'], 'edit', 'brand');
        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'sim_card.php?act=list&' . list_link_postfix();
        $note = vsprintf($_LANG['brandedit_succed'], $_POST['phone_number']);
        sys_msg($note, 0, $link);
    }
    else
    {
        die($db->error());
    }
}

/*------------------------------------------------------ */
//-- 删除sim卡
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('brand_manage');

    $id = intval($_GET['id']);


    $exc->drop($id);


    $url = 'sim_card.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
    
    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 切换是否显示
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_show')
{
    check_authz_json('brand_manage');

    $id     = intval($_POST['id']);
    $val    = intval($_POST['val']);

    $exc->edit("status='$val'", $id);

    make_json_result($val);
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $brand_list = get_brandlist();
    $smarty->assign('brand_list',   $brand_list['brand']);
    $smarty->assign('filter',       $brand_list['filter']);
    $smarty->assign('record_count', $brand_list['record_count']);
    $smarty->assign('page_count',   $brand_list['page_count']);

    make_json_result($smarty->fetch('sim_card_list.htm'), '',
        array('filter' => $brand_list['filter'], 'page_count' => $brand_list['page_count']));
}



/**
 * 获取sim卡列表列表
 *
 * @access  public
 * @return  array
 */
function get_brandlist()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 分页大小 */
        $filter = array();

       
            $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('sim_card');
        

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
            $sql = " SELECT s.id,s.phone_number,s.provider,s.status,s.service_password,s.login_password,s.phone_location,c.number,e.serial
                        FROM ".$GLOBALS['ecs']->table('sim_card'). " s 
                        LEFT JOIN". $GLOBALS['ecs']->table('equipment'). " e on e.sim = s.id ".  
                        "LEFT JOIN".$GLOBALS['ecs']->table('car')." c on c.equipment = e.id ".
                        "WHERE s.phone_number like '%{$keyword}%' or s.provider like '%{$keyword}%' or s.phone_location like '%{$keyword}%' or c.name like '%{$keyword}%'  or c.number like '%{$keyword}%' or c.station like '%{$keyword}%'".
                        "ORDER BY s.sort_order ASC";

        
            //$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('oil_card')." WHERE card_id like '%{$keyword}%' or password like '%{$keyword}%' or provider like '%{$keyword}%' ORDER BY sort_order ASC";
        }
        else
        {//手机号码,供应商,状态,服务密码,供应商网络登录 密码,归属地

            $sql = "    SELECT s.id, s.phone_number, s.provider, s.status, s.service_password, s.login_password, s.phone_location,c.number,e.serial
                            FROM ".$GLOBALS['ecs']->table('sim_card'). " s 
                            LEFT JOIN". $GLOBALS['ecs']->table('equipment'). " e on e.sim = s.id ".  
                            "LEFT JOIN".$GLOBALS['ecs']->table('car')." c on c.equipment = e.id ".
                            " ORDER BY s.sort_order ASC";
           
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
        $brand_logo = empty($rows['brand_logo']) ? '' :
            '<a href="../' . DATA_DIR . '/brandlogo/'.$rows['brand_logo'].'" target="_brank"><img src="images/picflag.gif" width="16" height="16" border="0" alt='.$GLOBALS['_LANG']['brand_logo'].' /></a>';
        $site_url   = empty($rows['site_url']) ? 'N/A' : '<a href="'.$rows['site_url'].'" target="_brank">'.$rows['site_url'].'</a>';

        $rows['brand_logo'] = $brand_logo;
        $rows['site_url']   = $site_url;

        $arr[] = $rows;
    }    
    return array('brand' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

?>