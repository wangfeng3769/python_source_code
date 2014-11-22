<?php

/**
 * ECSHOP 订单管理
 * ============================================================================
 * 版权所有 2005-2010 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: sxc_shop $
 * $Id: order.php 17151 2010-05-05 08:58:23Z sxc_shop $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/cls_image.php');
// $image = new cls_image($_CFG['bgcolor']);
/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

/* 初始化$exc对象 */
$exc = new exchange($ecs->table('invoice_log'), $db, 'id', 'user_id');


//-- 红包类型列表页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    $smarty->assign('ur_here',    '发票列表');
    //$smarty->assign('action_link', array('text' => $_LANG['bonustype_add'], 'href' => 'bonus.php?act=add'));
    $smarty->assign('full_page',   1);
    $list = get_invoice_info();
    $smarty->assign('invoice_list',    $list['brand']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);


    assign_query_info();
    $smarty->display('invoice_list.htm');
}

/*------------------------------------------------------ */
//-- 翻页、排序
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'query')
{
    $list = get_invoice_info();

    $smarty->assign('invoice_list',    $list['brand']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('invoice_list.htm'), '',
        array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

elseif ($_REQUEST['act']=='make_invoice') 
{
    //更新发票状态为已开发票
    $where = ' 0 ';
    if (strpos($_REQUEST['id'], ',') > 0)
    {

        $arrOrderID = explode(',',$_REQUEST['id'] );
        foreach ($arrOrderID as $v) {
            $where = $where." or id = ".$v;
        }
    }
    else
    {
        $where =" id=".$_REQUEST['id'];
    }

    $data = array('admin_id' => $_SESSION['admin_id'], 'is_processed'=>1,'processed_time'=>time() );
    $db->autoExecute($ecs->table('invoice_log'),$data,'update',$where);
        /* 提示信息 */

    $link[0]['text'] = '返回发票列表';
    $link[0]['href'] = 'invoice.php?act=list';

    sys_msg('操作成功,返回发票列表',0, $link);
    
}


/**
 * 获取品牌列表
 *
 * @access  public
 * @return  array
 */
function get_invoice_info()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 分页大小 */
        $filter = array();
        
        if ($_POST['composite_status']!=-1 && isset($_POST['composite_status']) )
        {
            $where .= ' AND i.is_processed='.$_POST['composite_status'];
        }

        if (!empty($_POST['order_no']))
        {
            $where .= ' AND o.order_no like '." '%".$_POST['order_no']."%' ";
        }

        if (!empty($_POST['user_name']))
        {
            $where .= ' AND u.uname like '." '%".$_POST['user_name']."%' ";
        }
       /* 记录总数以及页数 */

        if (isset($where)) {
            $where = ' WHERE 1 '.$where;
           # code...
        }
        else{
            $where='';
        }
        $sql =  " SELECT COUNT(1) FROM ".$GLOBALS['ecs']->table('invoice_log').' i '.
                " left join ".$GLOBALS['sns']->table('user')." u on u.uid=i.user_id ".
                " left join ".$GLOBALS['ecs']->table('car_order')." o on o.order_id=i.order_id ".
                " LEFT JOIN ".$GLOBALS['ecs']->table('admin_user')." a on a.user_id=i.admin_id ".
                $where;

         
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);
        /* 查询记录 */
        if ($where != '')
        {  
            
            $sql =  " SELECT i.id,u.true_name as uname,o.order_real_cost,a.user_name as admin_name,i.invoice_address,i.invoice_title ,o.order_no,i.is_processed,i.processed_time,i.zip FROM ".$GLOBALS['ecs']->table('invoice_log').' i '.
                    " left join ".$GLOBALS['sns']->table('user')." u on u.uid=i.user_id ".
                    " left join ".$GLOBALS['ecs']->table('car_order')." o on o.order_id=i.order_id ".
                    " LEFT JOIN ".$GLOBALS['ecs']->table('admin_user')." a on a.user_id=i.admin_id ".
                    $where.
                    " ORDER BY i.id ASC" ;
        }
        else
        {
            $sql =  " SELECT i.id,u.true_name as uname,o.order_real_cost,a.user_name as admin_name,i.invoice_address,i.invoice_title ,o.order_no ,i.is_processed ,i.processed_time,i.zip FROM ".$GLOBALS['ecs']->table('invoice_log').' i '.
                    " left join ".$GLOBALS['sns']->table('user')." u on u.uid=i.user_id ".
                    " left join ".$GLOBALS['ecs']->table('car_order')." o on o.order_id=i.order_id ".
                    " LEFT JOIN ".$GLOBALS['ecs']->table('admin_user')." a on a.user_id=i.admin_id ".
                    " ORDER BY i.id ASC" ;
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
        $rows['processed_time_fm']=date('Y-m-d',$rows['processed_time']);
        $arr[] = $rows;
    }

    return array('brand' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}