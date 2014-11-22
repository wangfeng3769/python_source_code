<?php
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);

$exc = new exchange('car_device', $db, 'id', 'sim');

/*------------------------------------------------------ */
//-- 品牌列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    $smarty->assign( 'ur_here',      $_LANG['06_equite_list']);
    $smarty->assign( 'action_link',  array('text' => $_LANG['07_equipment_add'], 'href' => 'equipment.php?act=add'));
    $smarty->assign( 'full_page',    1);

    $brand_list = get_brandlist();

    $smarty->assign('brand_list',   $brand_list['brand']);
    $smarty->assign('filter',       $brand_list['filter']);
    $smarty->assign('record_count', $brand_list['record_count']);
    $smarty->assign('page_count',   $brand_list['page_count']);

    assign_query_info();
    $smarty->display('equipment_list.htm');
}

/*------------------------------------------------------ */
//-- 添加品牌
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    /* 权限判断 */
    admin_priv('brand_manage');
     $simCardList=getSimcardList();

    $smarty->assign('sims',     $simCardList);
    $smarty->assign('ur_here',     $_LANG['07_equipment_add']);
    $smarty->assign('action_link', array('text' => $_LANG['06_equite_list'], 'href' => 'equipment.php?act=list'));
    $smarty->assign('form_action', 'insert');

    assign_query_info();
    $smarty->assign('brand', array('sort_order'=>50, 'is_show'=>1));
    $smarty->display('equipment_info.htm');
}
elseif ($_REQUEST['act'] == 'insert')
{
    /*检查品牌名是否重复*/
    admin_priv('brand_manage');

    $status = isset($_REQUEST['status']) ? intval($_REQUEST['status']) : 0;


    /*插入数据*/
    //若sim大于0更新使用的sim卡状态为启用
    if ($_POST['sim']>0) {
        $db->autoExecute($ecs->table('sim_card'),array('status'=>1),"updata"," id = '$_POST[sim]'");
    }

    $sql = "INSERT INTO ".'car_device'."(software_version, serial, sim, model, status) ".
           "VALUES ('$_POST[version]', '$_POST[serial]', '$_POST[sim]', '$_POST[model]', '$status')";
    $db->query($sql);

    admin_log($_POST['sim'],'add','equipment');

    /* 清除缓存 */
    clear_cache_files();

    $link[0]['text'] = $_LANG['continue_add'] ;
    $link[0]['href'] = 'equipment.php?act=add' ;

    $link[1]['text'] = $_LANG['back_list'] ;
    $link[1]['href'] = 'equipment.php?act=list';

    sys_msg("增加设备成功", 0, $link);
}

/*------------------------------------------------------ */
//-- 编辑品牌
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    /* 权限判断 */
    admin_priv('brand_manage');
    $sql = "SELECT * ".
            "FROM " .'car_device'. " WHERE id='$_REQUEST[id]'";
    $brand = $db->GetRow($sql);

    $simCardList=getSimcardList();

    $smarty->assign('sims',     $simCardList);
    $smarty->assign('ur_here',     $_LANG['brand_edit']);
    $smarty->assign('action_link', array('text' => $_LANG['06_equite_list'], 'href' => 'equipment.php?act=list&' . list_link_postfix()));
    $smarty->assign('brand',       $brand);
    $smarty->assign('form_action', 'updata');

    assign_query_info();
    $smarty->display('equipment_info.htm');
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

    //若sim大于0更新使用的sim卡状态为启用
    if ($_POST['sim']>0) {
        $db->autoExecute($ecs->table('sim_card'),array('status'=>1),"updata"," id = '$_POST[sim]'");
        $newSim=$_POST['sim'];
    }
    else
    {
       $newSim=''; 
    }

    //如果old sim大于0,释放oldsim
    $sql =  " SELECT sim FROM ".'car_device'." WHERE id=".$_POST['id'];
    $oldSim=$db->getOne($sql);
    if ($oldSim>0&&$oldSim!=$newSim) {
        $db->autoExecute($ecs->table('sim_card'),array('status'=>0),"updata"," id= $oldSim");
    }

    $param = "software_version = '$_POST[version]',  serial='$_POST[serial]', model='$_POST[model]', sim='$newSim' ";

    if ($exc->edit($param,  $_POST['id']))
    {
        /* 清除缓存 */
        clear_cache_files();

        admin_log($_POST['brand_name'], 'edit', 'brand');

        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'equipment.php?act=list&' . list_link_postfix();
        $note = vsprintf($_LANG['brandedit_succed'], $_POST['sim']);
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
        $sql = "INSERT INTO " . 'car_device' . "(brand_name)" .
               "VALUES ( '$brand')";

        $db->query($sql);
        $brand_id = $db->insert_id();

        $arr = array("id"=>$brand_id, "brand"=>$brand);

        make_json_result($arr);
    }
}
/*------------------------------------------------------ */


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
//-- 删除品牌
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('brand_manage');

    $id = intval($_GET['id']);

    //如果old sim大于0,释放oldsim
    $sql =  " SELECT sim FROM ".'car_device'." WHERE id=".$id;
    $oldSim=$db->getOne($sql);
    if ($oldSim>0) {
        $db->autoExecute($ecs->table('sim_card'),array('status'=>0),"updata"," id= $oldSim");
    }
    //删除汽车对应的车载设备
    $db->autoExecute($ecs->table('car'),array('equipment'=>''),'update',"equipment=$id ");
    $exc->drop($id);


    $url = 'equipment.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

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
    $sql = "SELECT brand_logo FROM " .'car_device'. " WHERE brand_id = '$brand_id'";
    $logo_name = $db->getOne($sql);

    if (!empty($logo_name))
    {
        @unlink(ROOT_PATH . DATA_DIR . '/brandlogo/' .$logo_name);
        $sql = "UPDATE " .'car_device'. " SET brand_logo = '' WHERE brand_id = '$brand_id'";
        $db->query($sql);
    }
    $link= array(array('text' => $_LANG['brand_edit_lnk'], 'href' => 'equipment.php?act=edit&id=' . $brand_id), array('text' => $_LANG['brand_list_lnk'], 'href' => 'equipment.php?act=list'));
    sys_msg($_LANG['drop_brand_logo_success'], 0, $link);
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

    make_json_result($smarty->fetch('equipment_list.htm'), '',
        array('filter' => $brand_list['filter'], 'page_count' => $brand_list['page_count']));
}

/**
 * 获取品牌列表
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

       
            $sql = "SELECT COUNT(*) FROM ".'car_device';
        

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
            $sql =  " SELECT eq.*,sc.phone_number FROM ".'car_device'.' eq '.
                    " LEFT JOIN ".$GLOBALS['ecs']->table('sim_card').' sc '.
                    " ON eq.sim=sc.id ".
                    " WHERE eq.serial like '%{$keyword}%' or sc.phone_number like '%{$keyword}%' or eq.model like '%{$keyword}%' ORDER BY id ASC";
        }
        else
        {
            $sql = " SELECT eq.*,sc.phone_number FROM ".'car_device'.' eq '.
                    " LEFT JOIN ".$GLOBALS['ecs']->table('sim_card').' sc '.
                    " ON eq.sim=sc.id ".
                    " ORDER BY id ASC";
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


function getSimcardList()
{
    global $db;
    global $ecs;

    $sql =  " SELECT id,phone_number,status".
            " FROM ".$ecs->table('sim_card');
    return $db->getAll($sql);
}
?>
