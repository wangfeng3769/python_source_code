<?php

/**
 * ECSHOP 管理中心供货商管理
 * ============================================================================
 * 版权所有 2005-2010 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: wanglei $
 * $Id: car_group.php 15013 2009-05-13 09:31:42Z wanglei $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
if (empty($_REQUEST['act'])) {
    $_REQUEST['act']='list';
}
/*------------------------------------------------------ */
//-- 供货商列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
     /* 检查权限 */
     admin_priv('car_group_manage');

    /* 查询 */
    $result = car_group_list();

    /* 模板赋值 */
    $smarty->assign('ur_here', $_LANG['car_group_list']); // 当前导航
    $smarty->assign('action_link', array('href' => 'car_group.php?act=add', 'text' => $_LANG['add_car_group']));

    $smarty->assign('full_page',        1); // 翻页参数

    $smarty->assign('car_group_list',    $result['result']);
    $smarty->assign('filter',       $result['filter']);
    $smarty->assign('record_count', $result['record_count']);
    $smarty->assign('page_count',   $result['page_count']);
    $smarty->assign('sort_car_group_id', '<img src="images/sort_desc.gif">');

    /* 显示模板 */
    assign_query_info();
    $smarty->display('car_group_list.htm');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    check_authz_json('car_group_manage');

    $result = car_group_list();

    $smarty->assign('car_group_list',    $result['result']);
    $smarty->assign('filter',       $result['filter']);
    $smarty->assign('record_count', $result['record_count']);
    $smarty->assign('page_count',   $result['page_count']);

    /* 排序标记 */
    $sort_flag  = sort_flag($result['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('car_group_list.htm'), '',
        array('filter' => $result['filter'], 'page_count' => $result['page_count']));
}

/*------------------------------------------------------ */
//-- 列表页编辑名称
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_car_group_name')
{
    check_authz_json('car_group_manage');

    $id     = intval($_POST['id']);
    $name   = json_str_iconv(trim($_POST['val']));

    /* 判断名称是否重复 */
    $sql = "SELECT car_group_id
            FROM " . $ecs->table('car_group') . "
            WHERE car_group_name = '$name'
            AND car_group_id <> '$id' ";
    if ($db->getOne($sql))
    {
        make_json_error(sprintf($_LANG['car_group_name_exist'], $name));
    }
    else
    {
        /* 保存供货商信息 */
        $sql = "UPDATE " . $ecs->table('car_group') . "
                SET car_group_name = '$name'
                WHERE car_group_id = '$id'";
        if ($result = $db->query($sql))
        {
            /* 记日志 */
            admin_log($name, 'edit', 'car_group');

            clear_cache_files();

            make_json_result(stripslashes($name));
        }
        else
        {
            make_json_result(sprintf($_LANG['agency_edit_fail'], $name));
        }
    }
}

/*------------------------------------------------------ */
//-- 删除供货商
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('car_group_manage');

    $id = intval($_REQUEST['id']);
    $sql = "SELECT *
            FROM " . $ecs->table('car_group') . "
            WHERE car_group_id = '$id'";
    $car_group = $db->getRow($sql, TRUE);

    if ($car_group['car_group_id'])
    {
        /* 判断供货商是否存在订单 */
        $sql = "SELECT COUNT(*)
                FROM " . $ecs->table('order_info') . "AS O, " . $ecs->table('order_goods') . " AS OG, " . $ecs->table('goods') . " AS G
                WHERE O.order_id = OG.order_id
                AND OG.goods_id = G.goods_id
                AND G.suppliers_id = '$id'";
        $order_exists = $db->getOne($sql, TRUE);
        if ($order_exists > 0)
        {
            $url = 'car_group.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
            ecs_header("Location: $url\n");
            exit;
        }

        /* 判断供货商是否存在商品 */
        $sql = "SELECT COUNT(*)
                FROM " . $ecs->table('goods') . "AS G
                WHERE G.suppliers_id = '$id'";
        $goods_exists = $db->getOne($sql, TRUE);
        if ($goods_exists > 0)
        {
            $url = 'car_group.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
            ecs_header("Location: $url\n");
            exit;
        }

        $sql = "DELETE FROM " . $ecs->table('car_group') . "
            WHERE car_group_id = '$id'";
        $db->query($sql);

        /* 删除管理员、发货单关联、退货单关联和订单关联的供货商 */
        $table_array = array('admin_user', 'delivery_order', 'back_order');
        foreach ($table_array as $value)
        {
            $sql = "DELETE FROM " . $ecs->table($value) . " WHERE car_group_id = '$id'";
            $db->query($sql, 'SILENT');
        }

        /* 记日志 */
        admin_log($car_group['car_group_name'], 'remove', 'car_group');

        /* 清除缓存 */
        clear_cache_files();
    }

    $url = 'car_group.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
    ecs_header("Location: $url\n");

    exit;
}


/*------------------------------------------------------ */
//-- 批量操作
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'batch')
{
    /* 取得要操作的记录编号 */
    if (empty($_POST['checkboxes']))
    {
        sys_msg($_LANG['no_record_selected']);
    }
    else
    {
        /* 检查权限 */
        admin_priv('car_group_manage');

        $ids = $_POST['checkboxes'];

        if (isset($_POST['remove']))
        {
            $sql = "SELECT *
                    FROM " . $ecs->table('car_group') . "
                    WHERE car_group_id " . db_create_in($ids);
            $car_group = $db->getAll($sql);

            foreach ($car_group as $key => $value)
            {
                /* 判断供货商是否存在订单 */
                $sql = "SELECT COUNT(*)
                        FROM " . $ecs->table('order_info') . "AS O, " . $ecs->table('order_goods') . " AS OG, " . $ecs->table('goods') . " AS G
                        WHERE O.order_id = OG.order_id
                        AND OG.goods_id = G.goods_id
                        AND G.suppliers_id = '" . $value['car_group_id'] . "'";
                $order_exists = $db->getOne($sql, TRUE);
                if ($order_exists > 0)
                {
                    unset($car_group[$key]);
                }

                /* 判断供货商是否存在商品 */
                $sql = "SELECT COUNT(*)
                        FROM " . $ecs->table('goods') . "AS G
                        WHERE G.suppliers_id = '" . $value['car_group_id'] . "'";
                $goods_exists = $db->getOne($sql, TRUE);
                if ($goods_exists > 0)
                {
                    unset($car_group[$key]);
                }
            }
            if (empty($car_group))
            {
                sys_msg($_LANG['batch_drop_no']);
            }

            $car_group_names = '';
            foreach ($car_group as $value)
            {
                $car_group_names .= $value['car_group_name'] . '|';
            }

            $sql = "DELETE FROM " . $ecs->table('car_group') . "
                WHERE car_group_id " . db_create_in($ids);
            $db->query($sql);

            /* 更新管理员、发货单关联、退货单关联和订单关联的供货商 */
            $table_array = array('admin_user', 'delivery_order', 'back_order');
            foreach ($table_array as $value)
            {
                $sql = "DELETE FROM " . $ecs->table($value) . " WHERE car_group_id " . db_create_in($ids) . " ";
                $db->query($sql, 'SILENT');
            }

            /* 记日志 */
            foreach ($car_group as $value)
            {
                $car_group_names .= $value['car_group_name'] . '|';
            }
            admin_log($car_group_names, 'remove', 'car_group');

            /* 清除缓存 */
            clear_cache_files();

//             sys_msg($_LANG['batch_drop_ok']);
        }
    }
    
    $url = 'car_group.php?act=query&' . str_replace('act=batch', '', $_SERVER['QUERY_STRING']);
    echo $url;
//     die();
    
    ecs_header("Location: $url\n");
    exit;
}
/*------------------------------------------------------ */
//-- 车群只能被某用户组预定
/*------------------------------------------------------ */
elseif ($_REQUEST['act']=='privilege')
{

    if (!isset($_REQUEST['handle'])) {
        $act='edit';
    }
    else
    {
        $act=$_REQUEST['handle'];
    }
    $carGroupId=$_REQUEST['id'];
    if (empty($carGroupId)||ceil($carGroupId)!=$carGroupId)
    {
        ecs_header("Location: car_group.php?act=list");
        // sys_msg('非法操作',0,array('href' => 'car_group.php?act=list', 'text' => $_LANG['back_car_group_list']));
    }

    if ($act=='edit')
    {
        
        $allUserGroup = allUserGroup();
        $relative=relative($allUserGroup,$carGroupId);

        $sql = " SELECT car_group_name FROM ".$ecs->table('car_group')." WHERE car_group_id=".$carGroupId;
        $carGroupName=$db->getOne($sql);
        
        $smarty->assign('car_group_name',$carGroupName);
        $smarty->assign('relative',$relative);
        // $smarty->assign('all_user_group',$allUserGroup);
        $smarty->assign('ur_here', "车群只能被指定用户群预定,不选择则任何群组可以使用"); // 当前导航
        $smarty->assign('action_link', array('href' => 'car_group.php?act=list', 'text' => "车群组列表"));
        $smarty->assign('is_ajax',1);
        $smarty->assign('form_action','update');
        $smarty->assign('car_group_id',$carGroupId);
        assign_query_info();
        $smarty->display('car_group2user_group.html');
    }
    
    elseif ($_REQUEST['handle']=='update') {
        //删除该车群组的所有特权会员群组,再插入新的配置
        $sql =  " DELETE FROM ".$ecs->table('car_group2user_group').
                " WHERE car_group=".$carGroupId;
        $db->query($sql);
        if (!empty($_POST['relative'])) {
            $values="";
            foreach ($_POST['relative'] as $v) {
                $values .= "($carGroupId,$v),";
            }
            $values=ereg_replace(",$", '', $values);

            $sql =  "INSERT INTO " . $ecs->table('car_group2user_group') . 
                    ' ( car_group,user_group) '.
                    ' VALUES '.$values;
            $db->query($sql);
        }

        /* 提示信息 */
        $links = array(array('href' => 'car_group.php?act=privilege&handle=list',  'text' => '继续修改特权车群'),
                       array('href' => 'car_group.php?act=list', 'text' => $_LANG['back_car_group_list'])
                       );
        sys_msg($_LANG['add_car_group_ok'], 0, $links);
    }


}

/*------------------------------------------------------ */
//-- 添加、编辑供货商
/*------------------------------------------------------ */
elseif (in_array($_REQUEST['act'], array('add', 'edit')))
{
    /* 检查权限 */
    admin_priv('car_group_manage');

    if ($_REQUEST['act'] == 'add')
    {
        $car_group = array();

        /* 取得所有管理员，*/
        /* 标注哪些是该供货商的('this')，哪些是空闲的('free')，哪些是别的供货商的('other') */
        /* 排除是办事处的管理员 */
        $sql = "SELECT user_id, user_name, CASE
                WHEN suppliers_id = 0 THEN 'free'
                ELSE 'other' END AS type
                FROM " . $ecs->table('admin_user') . "
                WHERE agency_id = 0
                AND action_list <> 'all'";
        $car_group['admin_list'] = $db->getAll($sql);

        $smarty->assign('ur_here', $_LANG['add_car_group']);
        $smarty->assign('action_link', array('href' => 'car_group.php?act=list', 'text' => $_LANG['car_group_list']));

        $smarty->assign('form_action', 'insert');
        $smarty->assign('car_group', $car_group);

        assign_query_info();

        $smarty->display('car_group_info.htm');

    }
    elseif ($_REQUEST['act'] == 'edit')
    {
        $car_group = array();

        /* 取得供货商信息 */
        $id = $_REQUEST['id'];
        $sql = "SELECT * FROM " . $ecs->table('car_group') . " WHERE car_group_id = '$id'";
        $car_group = $db->getRow($sql);
        if (count($car_group) <= 0)
        {
            sys_msg('car_group does not exist');
        }

        /* 取得所有管理员，*/
        /* 标注哪些是该供货商的('this')，哪些是空闲的('free')，哪些是别的供货商的('other') */
        /* 排除是办事处的管理员 */
        $sql = "SELECT user_id, user_name, CASE
                WHEN suppliers_id = '$id' THEN 'this'
                WHEN suppliers_id = 0 THEN 'free'
                ELSE 'other' END AS type
                FROM " . $ecs->table('admin_user') . "
                WHERE agency_id = 0
                AND action_list <> 'all'";
        $car_group['admin_list'] = $db->getAll($sql);

        $smarty->assign('ur_here', $_LANG['edit_car_group']);
        $smarty->assign('action_link', array('href' => 'car_group.php?act=list', 'text' => $_LANG['car_group_list']));

        $smarty->assign('form_action', 'update');
        $smarty->assign('car_group', $car_group);

        assign_query_info();

        $smarty->display('car_group_info.htm');
    }

}

/*------------------------------------------------------ */
//-- 提交添加、编辑供货商
/*------------------------------------------------------ */
elseif (in_array($_REQUEST['act'], array('insert', 'update')))
{
    /* 检查权限 */
    admin_priv('car_group_manage');

    if ($_REQUEST['act'] == 'insert')
    {
        /* 提交值 */
        $car_group = array('car_group_name'   => trim($_POST['car_group_name']),
                           'car_group_desc'   => trim($_POST['car_group_desc']),
                           'parent_id'        => 0
                           );

        /* 判断名称是否重复 */
        $sql = "SELECT car_group_id
                FROM " . $ecs->table('car_group') . "
                WHERE car_group_name = '" . $car_group['car_group_name'] . "' ";
        if ($db->getOne($sql))
        {
            sys_msg($_LANG['car_group_name_exist']);
        }

        $db->autoExecute($ecs->table('car_group'), $car_group, 'INSERT');
        $car_group['car_group_id'] = $db->insert_id();

        if (isset($_POST['admins']))
        {
            $sql = "UPDATE " . $ecs->table('admin_user') . " SET suppliers_id = '" . $car_group['car_group_id'] . "', action_list = '" . car_group_ACTION_LIST . "' WHERE user_id " . db_create_in($_POST['admins']);
            $db->query($sql);
        }

        /* 记日志 */
        admin_log($car_group['car_group_name'], 'add', 'car_group');

        /* 清除缓存 */
        clear_cache_files();

        /* 提示信息 */
        $links = array(array('href' => 'car_group.php?act=add',  'text' => $_LANG['continue_add_car_group']),
                       array('href' => 'car_group.php?act=list', 'text' => $_LANG['back_car_group_list'])
                       );
        sys_msg($_LANG['add_car_group_ok'], 0, $links);

    }

    if ($_REQUEST['act'] == 'update')
    {
        /* 提交值 */
        $car_group = array('id'   => trim($_POST['id']));

        $car_group['new'] = array('car_group_name'   => trim($_POST['car_group_name']),
                           'car_group_desc'   => trim($_POST['car_group_desc'])
                           );

        /* 取得供货商信息 */
        $sql = "SELECT * FROM " . $ecs->table('car_group') . " WHERE car_group_id = '" . $car_group['id'] . "'";
        $car_group['old'] = $db->getRow($sql);
        if (empty($car_group['old']['car_group_id']))
        {
            sys_msg('car_group does not exist');
        }

        /* 判断名称是否重复 */
        $sql = "SELECT car_group_id
                FROM " . $ecs->table('car_group') . "
                WHERE car_group_name = '" . $car_group['new']['car_group_name'] . "'
                AND car_group_id <> '" . $car_group['id'] . "'";
        if ($db->getOne($sql))
        {
            sys_msg($_LANG['car_group_name_exist']);
        }

        /* 保存供货商信息 */
        $db->autoExecute($ecs->table('car_group'), $car_group['new'], 'UPDATE', "car_group_id = '" . $car_group['id'] . "'");

        /* 清空供货商的管理员 */
        $sql = "UPDATE " . $ecs->table('admin_user') . " SET suppliers_id = 0, action_list = '" . car_group_ACTION_LIST . "' WHERE suppliers_id = '" . $car_group['id'] . "'";
        $db->query($sql);

        /* 添加供货商的管理员 */
        if (isset($_POST['admins']))
        {
            $sql = "UPDATE " . $ecs->table('admin_user') . " SET car_group_id = '" . $car_group['old']['car_group_id'] . "' WHERE user_id " . db_create_in($_POST['admins']);
            $db->query($sql);
        }

        /* 记日志 */
        admin_log($car_group['old']['car_group_name'], 'edit', 'car_group');

        /* 清除缓存 */
        clear_cache_files();

        /* 提示信息 */
        $links[] = array('href' => 'car_group.php?act=list', 'text' => $_LANG['back_car_group_list']);
        sys_msg($_LANG['edit_car_group_ok'], 0, $links);
    }

}

/**
 *  获取供应商列表信息
 *
 * @access  public
 * @param
 *
 * @return void
 */
function car_group_list()
{
    $result = get_filter();
    if ($result === false)
    {
        $aiax = isset($_GET['is_ajax']) ? $_GET['is_ajax'] : 0;

        /* 过滤信息 */
        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'car_group_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'ASC' : trim($_REQUEST['sort_order']);

        $where = 'WHERE 1 ';

        /* 分页大小 */
        $filter['page'] = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);

        if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0)
        {
            $filter['page_size'] = intval($_REQUEST['page_size']);
        }
        elseif (isset($_COOKIE['ECSCP']['page_size']) && intval($_COOKIE['ECSCP']['page_size']) > 0)
        {
            $filter['page_size'] = intval($_COOKIE['ECSCP']['page_size']);
        }
        else
        {
            $filter['page_size'] = 15;
        }

        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('car_group') . $where;
        $filter['record_count']   = $GLOBALS['db']->getOne($sql);
        $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;

        /* 查询 */
        $sql = "SELECT car_group_id, car_group_name, car_group_desc
                FROM " . $GLOBALS['ecs']->table("car_group") . "
                $where
                ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']. "
                LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'] . " ";

        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $row = $GLOBALS['db']->getAll($sql);

    $arr = array('result' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

function allUserGroup()
{
    global $db;
    global $ecs;
    global $sns;

    $sql =  " SELECT user_group_id,title ".
            " FROM ".$sns->table('user_group');
    $ret = $db->getAll($sql);
    $allUserGroup=array();
    foreach ($ret as $v) {
        $allUserGroup[$v['user_group_id']]=$v;
    }

    return $allUserGroup;
}

function relative($allUserGroup,$carGroupId)
{
    global $db;
    global $ecs;

    $sql =  " SELECT cgu.user_group FROM ".$ecs->table('car_group2user_group').' cgu '.
            " WHERE cgu.car_group=".$carGroupId;
    $relativeTemp = $db->getAll($sql);

    $relative=array();
    foreach ($relativeTemp as $v) {
        $relative[]=$v['user_group'];
    }

    foreach ($allUserGroup as $k => $v) {
        if (in_array($v['user_group_id'], $relative)) {
           $allUserGroup[$k]['is_checked'] = 1;
        }
        else
        {
            $allUserGroup[$k]['is_checked'] = 0;
        }
    }
    return $allUserGroup;

}
?>