<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);

$exc = new exchange($ecs->table("vip_card_issue"), $db, 'id', 'card_number');
/*------------------------------------------------------ */
//-- sim卡列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    //echo $_LANG['06_equite_list'];
    $smarty->assign( 'ur_here',      $_LANG['11_vip_card_issue']);
    //$smarty->assign( 'action_link',  array('text' => $_LANG['05_sim_card_add'], 'href' => 'sim_card.php?act=add'));
    $smarty->assign( 'full_page',    1);
    $brand_list = get_brandlist();
    //print_r($brand_list['brand']);
    $smarty->assign('brand_list',   $brand_list['brand']);
    $smarty->assign('filter', $brand_list['filter']);
    $smarty->assign('record_count', $brand_list['record_count']);
    $smarty->assign('page_count',   $brand_list['page_count']);
    assign_query_info();
    $smarty->display('vip_card_issue_list.htm');
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


elseif ($_REQUEST['act'] == 'issue')
{
    admin_priv('brand_manage');

    /*对描述处理*/
    if (!empty($_POST['brand_desc']))
    {
        $_POST['brand_desc'] = $_POST['brand_desc'];
    }

    if (! empty($_GET['id']) && ceil($_GET['id'])==$_GET['id']) 
    {
        $where = 'AND i.id = '.$_GET['id'];
        $brand = get_brandlist($where);
        $brand = $brand['brand'][0];
    }
    else
    {
        echo '参数错误';
        die();
    }
    print_r($brand);
    /*更新会员卡管理表状态*/
    $vipCard = new exchange($ecs->table("vip_card"), $db, 'id', 'vip_card_id');
    $param_v = "user_id = ".$brand['uid'].",allot_status = 1 ";
    $c = $vipCard->edit($param_v,$brand['vip_card_id']);
    /*更新会员表状态*/
    $user = new exchange($sns->table("user"), $db, 'uid', 'vip_card_id');
    $param_u = "vip_card_id = ".$brand['vip_card_id'];
    $b = $user->edit($param_u,$brand['uid']);
    /*更新发卡表的发卡时间和发送状态*/
    $param_i = "issue_time = '".time()."',issue_status = '1'";
    $a = $exc->edit($param_i,$_GET['id']);
    assign_query_info();
        /* 清除缓存 */
    clear_cache_files();

    admin_log($_POST['brand_name'], 'edit', 'brand');
    $link[0]['text'] = $_LANG['back_list'];
    $link[0]['href'] = 'vip_card_issue.php?act=list';
    $note = vsprintf($_LANG['brandedit_succed'], $brand['card_number']);
    sys_msg($note, 0, $link);

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

    make_json_result($smarty->fetch('vip_card_issue_list.htm'), '',
        array('filter' => $brand_list['filter'], 'page_count' => $brand_list['page_count']));
}



/**
 * 获取sim卡列表列表
 *
 * @access  public
 * @return  array
 */
function get_brandlist($where='')
{
    $result = get_filter();
    if ($result === false)
    {
        /* 分页大小 */
        $filter = array();

       
            $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('vip_card_issue');
        

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
            $sql = " SELECT i.id,v.id as 'vip_card_id' ,u.uid,v.card_number,v.card_type,u.uname,u.true_name,i.buy_time,u.phone,i.issue_status,i.post_man,i.issue_time,i.location
                    FROM ".$GLOBALS['ecs']->table('vip_card_issue'). " i ".
                    " LEFT JOIN ".$GLOBALS['sns']->table('user')." u ".
                    " ON u.uid = i.user_id ".
                    " LEFT JOIN ".$GLOBALS['ecs']->table('vip_card')." v ".
                    " ON v.id = i.vip_card_id ".
                    "WHERE v.card_number like '%{$keyword}%' or v.card_type like '%{$keyword}%' or u.uname like '%{$keyword}%' or u.true_name like '%{$keyword}%'  or i.phone like '%{$keyword}%' or i.issue_status like '%{$keyword}%' or i.location like '%{$keyword}%' or i.post_man like '%{$keyword}%' ".
                    "ORDER BY i.sort_order ASC";

        
            //$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('oil_card')." WHERE card_id like '%{$keyword}%' or password like '%{$keyword}%' or provider like '%{$keyword}%' ORDER BY sort_order ASC";
        }
        else
        {//购买时间,发送状态,发卡员,发送时间,城市,邮寄地址
            $sql = " SELECT i.id,v.id as 'vip_card_id' ,u.uid,v.card_number,v.card_type,u.uname,u.true_name,i.buy_time,u.phone,i.issue_status,i.post_man,i.issue_time,i.location
                    FROM ".$GLOBALS['ecs']->table('vip_card_issue'). " i ".
                    " LEFT JOIN ".$GLOBALS['sns']->table('user')." u ".
                    " ON u.uid = i.user_id ".
                    " LEFT JOIN ".$GLOBALS['ecs']->table('vip_card')." v ".
                    " ON v.id = i.vip_card_id ".
                    " WHERE 1 ".$where.
                    " ORDER BY i.issue_status ASC";
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
        $rows['issue_time'] = date('Y-m-d H:i:s',$rows['issue_time']);
        $rows['buy_time'] = date('Y-m-d H:i:s',$rows['buy_time']);

        $arr[] = $rows;
    }
    
    
    return array('brand' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

?>