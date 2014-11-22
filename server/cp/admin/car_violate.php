<?php
define('IN_ECS', true);

require_once(dirname(__FILE__) . '/includes/init.php');

require_once(ROOT_PATH . 'includes/cls_image.php');
require_once(ROOT_PATH."../client/SmsPlatform/SmsSender.php");
$image = new cls_image($_CFG['bgcolor']);

$exc = new exchange($ecs->table("vip_card"), $db, 'id', 'serial');
/*------------------------------------------------------ */
//-- 会员卡列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    $smarty->assign( 'ur_here',      $_LANG['01_car_violate_list']);
    $smarty->assign( 'action_link',  array('text' => $_LANG['02_violate_add'], 'href' => 'car_violate.php?act=add'));
    //$smarty->assign( 'action_link2',  array('text' => $_LANG['11_vip_card_add_excel'], 'href' => 'vip_card.php?act=add_excel'));
    $smarty->assign( 'full_page',    1);
    
    $brand_list = get_brandlist();

    //print_r($brand_list['brand']);
    $smarty->assign('brand_list',   $brand_list['brand']);
    $smarty->assign('filter', $brand_list['filter']);
    $smarty->assign('record_count', $brand_list['record_count']);
    $smarty->assign('page_count',   $brand_list['page_count']);
    assign_query_info();
    $smarty->display('car_violate_list.htm');
}

/*------------------------------------------------------ */
//-- 添加违章信息
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{   
    /* 权限判断 */
    admin_priv('brand_manage');
    $smarty->assign('ur_here',     $_LANG['02_violate_add']);
    $smarty->assign('action_link', array('text' => $_LANG['01_violate_list'], 'href' => 'car_violate.php?act=list'));
    $smarty->assign('form_action', 'set_info');
    assign_query_info();
    // $smarty->assign('brand', array('sort_order'=>50, 'is_show'=>1));
    $smarty->display('car_violate_add.htm');
}
elseif ($_REQUEST['act'] == 'set_info')
{
    $a=$_POST;
    
    $smarty->assign('ur_here',     '确认信息');
    $smarty->assign('action_link', array('text' => $_LANG['01_violate_list'], 'href' => 'car_violate.php?act=list'));
    $violate_time=strtotime($_POST['violate_time']);

    /*  modify by matao 2013.4.18 
    原程序中 c.number 字段对应的 $_POST[car_number] 为字符串改为用''，
    o.order_start_time 字段对应的 $violate_time 为int 改为用{};
    */

    /* 
    $sql =  " SELECT o.order_id,u.uname, o.order_no, o.order_start_time, o.order_end_time, u.phone ".
            " FROM " . $GLOBALS['ecs']->table('car_order') . " AS o " .
            " LEFT JOIN " .$GLOBALS['sns']->table('user'). " AS u ON u.uid=o.user_id ".
            " LEFT JOIN " .$GLOBALS['ecs']->table('car'). " AS c ON o.car_id=c.id ".
            " WHERE c.number = $_POST[car_number]".
            " AND   o.order_start_time < $violate_time".
            " AND   o.order_end_time > $violate_time";
    */

    $sql =  " SELECT o.order_id,u.uname, o.order_no, o.real_start_time, o.real_end_time, u.phone ".
            " FROM " . $GLOBALS['ecs']->table('car_order') . " AS o " .
            " LEFT JOIN " .$GLOBALS['sns']->table('user'). " AS u ON u.uid=o.user_id ".
            " LEFT JOIN " .$GLOBALS['ecs']->table('car'). " AS c ON o.car_id=c.id ".
            " WHERE c.number = '$_POST[car_number]'".
            " AND   o.real_start_time < {$violate_time}".
            " AND   o.real_end_time > {$violate_time} ".
            " AND  o.modify_stat=0 ";
            
    /*modify by matao 2013.4.18*/





    $v_data=$GLOBALS['db']->getRow($sql);
    if (empty($v_data))
    {
        sys_msg('未能查询到该违规记录的订单号,请确认车牌号和违规时间是否正确',1,array(),false);
    }
    $data = array_merge($_POST,$v_data);
    
    if ($_REQUEST['next_act']=='update')
    {

        $smarty->assign('violate_id', $_REQUEST['violate_id']);
        $smarty->assign('form_action', 'update');
    }
    else
    {
        $smarty->assign('form_action', 'insert');
    }
    assign_query_info();
    $smarty->assign('data', $data);
    $smarty->display('car_violate_add.htm');
    
}    
 elseif ($_REQUEST['act'] == 'insert')
{
    /*插入违章记录*/
    //print_r($_POST);
    //die('insert');
    admin_priv('violate');
    $data['order_id']=$_POST['order_id'];
    $data['violate_time']=strtotime($_POST['violate_time']);
    $data['violate_location']=$_POST['violate_location'];
    $data['violate_type']=$_POST['violate_type'];
    $data['violate_cost']=$_POST['violate_cost']*100;
    $data['violate_point']=$_POST['violate_point'];
    $data['agency_cost']=$_POST['agency_cost']*100;
    $data['record_time']=time();
    //检查违章记录是否存在,判断时间,order_id
    $sql =  " SELECT COUNT(*) ".
            " FROM ".$ecs->table('car_violate').
            " WHERE violate_time=".$data['violate_time'].
            " AND order_id=".$data['order_id'];
    if($db->getOne($sql) > 0)
    {

        sys_msg('该违规记录已经存在,请确认车牌号和违规时间是否正确',1,array(),false);

    }
    /*插入数据*/
    if($db->autoExecute($ecs->table('car_violate'),$data))
    {
        //sys_msg('插入数据成功',0,array(),false); 禁用用户订车权限
        $sql =  " SELECT o.user_id,o.order_no,u.phone
                  FROM ".$ecs->table('car_order').' o '.
                " LEFT JOIN edo_user u ".
                " ON u.uid=o.user_id ".
                " WHERE order_id=".$data['order_id'];
        $userInfo = $db->getRow($sql);

        $db->autoExecute($sns->table('user'),array('account_stat'=>1),"update","uid=$userInfo[user_id]");

        //发短信通知用户有违章信息
        sendSms($userInfo['order_no'],$userInfo['phone']);
    }   
    else
    {
        sys_msg('插入数据失败',1,array(),false);
    }

    admin_log($_POST['uname'],'add','violate');

    /* 清除缓存 */
    clear_cache_files();

    $link[0]['text'] = '继续添加' ;
    $link[0]['href'] = 'car_violate.php?act=add' ;

    $link[1]['text'] = '返回列表';
    $link[1]['href'] = 'car_violate.php?act=list';

    sys_msg('插入数据成功', 0, $link);
}

elseif ($_REQUEST['act'] == 'edit')
{
    $smarty->assign('ur_here',     '修改违章记录');
    $smarty->assign('action_link', array('text' => $_LANG['01_violate_list'], 'href' => 'car_violate.php?act=list'));
    $sql =  " SELECT v.* ,c.number as 'car_number',u.uname".
            " FROM " . $GLOBALS['ecs']->table('car_violate') . " AS v " .
            " LEFT JOIN " .$GLOBALS['ecs']->table('car_order'). " AS o ON o.order_id=v.order_id ".
            " LEFT JOIN " .$GLOBALS['sns']->table('user'). " AS u ON u.uid=o.user_id ".
            " LEFT JOIN " .$GLOBALS['ecs']->table('car'). " AS c ON o.car_id=c.id ".
            " WHERE v.id = $_REQUEST[id] ";
    $data=$GLOBALS['db']->getRow($sql);
    $data['violate_time'] = date('Y-m-d H:i:s',$data['violate_time']);
    $data['violate_id']=$_REQUEST['id'];
    $data['violate_cost']=$data['violate_cost']/100;
    $data['agency_cost']=$data['agency_cost']/100;
    
    $smarty->assign('next_act', 'update');
    $smarty->assign('form_action', 'set_info');
    // assign_query_info();
    $smarty->assign('data', $data);
    $smarty->display('car_violate_edit.htm');
    
}
elseif ($_REQUEST['act'] == 'update')
{
    /*插入违章记录*/
    //print_r($_POST);
    //die('insert');
    admin_priv('violate');
    $data['order_id']=$_POST['order_id'];
    $data['violate_time']=strtotime($_POST['violate_time']);
    $data['violate_location']=$_POST['violate_location'];
    $data['violate_type']=$_POST['violate_type'];
    $data['violate_cost']=$_POST['violate_cost']*100;
    $data['violate_point']=$_POST['violate_point'];
    $data['agency_cost']=$_POST['agency_cost']*100;
    $data['process_stat']=$_POST['violate_stat'];
    
    //检查违章记录是否存在,判断时间,order_id

    /*更新数据*/
    if($db->autoExecute($ecs->table('car_violate'),$data,"update","id=".$_REQUEST['violate_id']))
    {
        //0=新录入,1=正在代办,2=已自行处理,3=已代办处理,4=已结算
        //sys_msg('更新数据成功',0,array(),false);

    }
    else
    {
        clear_cache_files();
        sys_msg('更新数据失败',1,array(),false);
    }

    admin_log($_POST['uname'],'add','violate');

    /* 清除缓存 */
    clear_cache_files();


    $link[0]['text'] = '返回列表';
    $link[0]['href'] = 'car_violate.php?act=list';

    sys_msg('更新数据成功', 0, $link);
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

    make_json_result($smarty->fetch('car_violate_list.htm'), '',
        array('filter' => $brand_list['filter'], 'page_count' => $brand_list['page_count']));
}



/**
 * 获取违章列表
 *
 * @access  public
 * @return  array
 */
function get_brandlist()
{
    $result = get_filter();
    if ($result === false)
    {
        $filter['uname'] = empty($_REQUEST['uname']) ? '' : trim($_REQUEST['uname']);
        $filter['violate_time'] = empty($_REQUEST['violate_time']) ? '' : strtotime($_REQUEST['violate_time']);
        $filter['process_stat'] = empty($_REQUEST['process_stat']) ? '' : trim($_REQUEST['process_stat']);

        $filter['car_group_name'] = empty($_REQUEST['car_group_name']) ? '' : trim($_REQUEST['car_group_name']);
        $filter['car_name'] = empty($_REQUEST['car_name']) ? '' : trim($_REQUEST['car_name']);
        $filter['car_number'] = empty($_REQUEST['car_number']) ? '' : trim($_REQUEST['car_number']);
        $filter['car_region'] = empty($_REQUEST['car_region']) ? '' : trim($_REQUEST['car_region']);
        $filter['car_station'] = empty($_REQUEST['car_station']) ? '' : trim($_REQUEST['car_station']);
        // var_dump(empty($_REQUEST['process_stat']));die();
        //die($filter['uname']);车辆群组,车辆昵称,车牌号,城市,站点
        $where = 'WHERE 1 ';
        if ($filter['uname'] )
        {
            $where .= " AND u.uname LIKE '%" . mysql_like_quote($filter['uname']) . "%'";
        }
        if ($filter['violate_time'])
        {
            $where .= " AND v.violate_time > ".$filter['violate_time'].
                      " AND v.violate_time < ".($filter['violate_time']+3600*24).' ';
        }
        if ($_REQUEST['process_stat']!='')
        {
            
            $where .= " AND v.process_stat = " . $_REQUEST['process_stat'].' ';
        }
        if ($filter['car_group_name'])
        {
            $where .= " AND g.car_group_name LIKE '%" . mysql_like_quote($filter['car_group_name']) . "%'";
        }
        if ($filter['car_name'])
        {
            $where .= " AND c.name LIKE '%" . mysql_like_quote($filter['car_name']) . "%'";
        }
        if ($filter['car_number'])
        {
            $where .= " AND c.number LIKE '%" . mysql_like_quote($filter['car_number']) . "%'";
        }
        if ($filter['car_station'])
        {
            $where .= " AND s.name LIKE '%" . mysql_like_quote($filter['car_station']) . "%'";
        }
        if ($filter['car_region'])
        {
            $where .= " AND r.region_name LIKE '%" . mysql_like_quote($filter['car_region']) . "%'";
        }
        /* 分页大小 */
        $strJoin='';
        if($filter['car_group_name'])
        {
            $strJoin .= " LEFT JOIN " .$GLOBALS['ecs']->table('car_group'). " AS g ON c.group=g.car_group_id ";   
        }
        if($filter['car_station'])
        {
            $strJoin .=" LEFT JOIN " .$GLOBALS['ecs']->table('station'). " AS s ON c.station=s.id ";
        }
        if($filter['car_region'])
        {
            $strJoin .=" LEFT JOIN " .$GLOBALS['ecs']->table('station'). " AS s ON c.station=s.id ";
            $strJoin.=" LEFT JOIN " .$GLOBALS['ecs']->table('region'). " AS r ON r.region_id=s.city ";
        }
        $filter = array();

        $sql = "SELECT COUNT(*) 
                FROM ". $GLOBALS['ecs']->table('car_violate') . " AS v " .
                " LEFT JOIN " .$GLOBALS['ecs']->table('car_order'). " AS o ON o.order_id=v.order_id ".
                " LEFT JOIN " .$GLOBALS['sns']->table('user'). " AS u ON u.uid=o.user_id ".
                " LEFT JOIN " .$GLOBALS['ecs']->table('car'). " AS c ON o.car_id=c.id ".
                $strJoin.
                $where;     
        
        // echo $sql;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);

        /* 查询记录 */
 
        $sql =  " SELECT  v.id, u.uname, c.number, v.violate_time,v.process_stat,v.violate_cost,v.agency_cost".
                " FROM " . $GLOBALS['ecs']->table('car_violate') . " AS v " .
                " LEFT JOIN " .$GLOBALS['ecs']->table('car_order'). " AS o ON o.order_id=v.order_id ".
                " LEFT JOIN " .$GLOBALS['sns']->table('user'). " AS u ON u.uid=o.user_id ".
                " LEFT JOIN " .$GLOBALS['ecs']->table('car'). " AS c ON o.car_id=c.id ".
                $strJoin.
                $where.                    
                "ORDER BY v.id ASC";
        //echo $sql;
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
        $rows['fm_violate_time']=date('Y-m-d H:i:s',$rows['violate_time']);
        $rows['brand_logo'] = $brand_logo;
        $rows['site_url']   = $site_url;
        $rows['violate_cost']=$rows['violate_cost']/100;
        $rows['agency_cost']=$rows['agency_cost']/100;
        $arr[] = $rows;
    }

    return array('brand' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}


function sendSms($order_no,$phone)
{
    $smsContent = " 你好,你在易多汽车共享租用的车出现违章 订单号为 ".$order_no.
                      " 即日起,3天内可自行处理, 超过3天后将自动转为客服代办,详情请进入我们的网站查询";
    $sms = new SmsSender();
    $sms->send($smsContent,$phone);
}
?>