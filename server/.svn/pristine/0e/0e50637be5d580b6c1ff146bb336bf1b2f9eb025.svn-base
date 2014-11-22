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
 * $Id: ModifyPhonenum.php 15013 2009-05-13 09:31:42Z wanglei $
 */

if(!defined('ROOT_PATH'))
{
	define('IN_ECS', true);
	require(dirname(__FILE__) . '/includes/init.php');
}

require_once( ROOT_PATH . "/../medal_container/MedalContainer.php" );

require_once( ROOT_PATH . "/../client/classes/UserManager.php" );
require_once( ROOT_PATH . "/../client/classes/ModifyPhone.php" );

/*------------------------------------------------------ */
//-- 供货商列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
     /* 检查权限 */
     admin_priv('credit_card_id');

    /* 查询 */
    $result = credit_card_list();

    /* 模板赋值 */
    $smarty->assign('ur_here', $_LANG['credit_card_list']); // 当前导航
    $smarty->assign('action_link', array('href' => 'ModifyPhonenum.php?act=add', 'text' => $_LANG['add_credit_card']));

    $smarty->assign('full_page',        1); // 翻页参数

    $smarty->assign('credit_card_list',    $result['result']);
    $smarty->assign('filter',       $result['filter']);
    $smarty->assign('record_count', $result['record_count']);
    $smarty->assign('page_count',   $result['page_count']);
    $smarty->assign('sort_credit_card_id', '<img src="images/sort_desc.gif">');

    /* 显示模板 */
    assign_query_info();
    $smarty->display('credit_card_list.htm');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    check_authz_json('credit_card_id');

    $result = credit_card_list();

    $smarty->assign('credit_card_list',    $result['result']);
    $smarty->assign('filter',       $result['filter']);
    $smarty->assign('record_count', $result['record_count']);
    $smarty->assign('page_count',   $result['page_count']);

    /* 排序标记 */
    $sort_flag  = sort_flag($result['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('credit_card_list.htm'), '',
        array('filter' => $result['filter'], 'page_count' => $result['page_count']));
}

/*------------------------------------------------------ */
//-- 列表页编辑名称
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_credit_card_id')
{
    check_authz_json('credit_card_id');

    $id     = intval($_POST['id']);
    $credit_card_id=$_POST['credit_card_id'];

    /* 判断名称是否重复 */
    $sql = "SELECT credit_card_id
            FROM " . $ecs->table('credit_card') . "
            WHERE id = '$id'
            AND credit_card_id <> '$credit_card_id' ";
    if ($db->getOne($sql))
    {
        make_json_error(sprintf($_LANG['json_str_iconv_id_exist'], $credit_card_id));
    }
    else
    {
        /* 保存供货商信息 */
        $sql = "UPDATE " . $ecs->table('credit_card') . "
                SET credit_card_id = '$credit_card_id'
                WHERE id = '$id'";
        if ($result = $db->query($sql))
        {
            /* 记日志 */
            admin_log($credit_card_id, 'edit', 'json_str_iconv');

            clear_cache_files();

            make_json_result(stripslashes($credit_card_id));
        }
        else
        {
            make_json_result(sprintf($_LANG['agency_edit_fail'], $credit_card_id));
        }
    }
}

/*------------------------------------------------------ */
//-- 删除供货商
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('credit_card_id');

    $id = intval($_REQUEST['id']);
    $sql = "SELECT *
            FROM " . $ecs->table('credit_card') . "
            WHERE id = '$id'";
    $credit_card = $db->getRow($sql, TRUE);

    if ($credit_card['credit_card_id'])
    {
        $sql = "DELETE FROM " . $ecs->table('credit_card') . "
            WHERE id = '$id'";
        $db->query($sql);

        /* 记日志 */
        admin_log($credit_card['credit_card_id'], 'remove', 'credit_card');

        /* 清除缓存 */
        clear_cache_files();
    }

    $url = 'ModifyPhonenum.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
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
        admin_priv('credit_card_id');

        $ids = $_POST['checkboxes'];

        if (isset($_POST['remove']))
        {
            $sql = "SELECT *
                    FROM " . $ecs->table('credit_card') . "
                    WHERE id " . db_create_in($ids);
            $credit_card = $db->getAll($sql);

            if (empty($credit_card))
            {
                sys_msg($_LANG['batch_drop_no']);
            }

            $credit_card_ids = '';
            foreach ($credit_card as $value)
            {
                $credit_card_ids .= $value['credit_card_id'] . '|';
            }

            $sql = "DELETE FROM " . $ecs->table('credit_card') . "
                WHERE id " . db_create_in($ids);
            $db->query($sql);

            /* 更新管理员、发货单关联、退货单关联和订单关联的供货商 */
            $table_array = array('admin_user', 'delivery_order', 'back_order');
            foreach ($table_array as $value)
            {
                $sql = "DELETE FROM " . $ecs->table($value) . " WHERE id " . db_create_in($ids) . " ";
                $db->query($sql, 'SILENT');
            }

            /* 记日志 */
            foreach ($credit_card as $value)
            {
                $credit_card_ids .= $value['credit_card_id'] . '|';
            }
            admin_log($credit_card_ids, 'remove', 'credit_card');

            /* 清除缓存 */
            clear_cache_files();
			$links[] = array('href' => 'ModifyPhonenum.php?act=list', 'text' => $_LANG['back_credit_card_list']);
            sys_msg($_LANG['batch_drop_ok'],0,$links);
        }
    }
}

/*------------------------------------------------------ */
//-- 添加、编辑供货商
/*------------------------------------------------------ */
elseif (in_array($_REQUEST['act'], array('add', 'edit')))
{
    /* 检查权限 */
    admin_priv('credit_card_id');

    if ($_REQUEST['act'] == 'add')
    {	
        $credit_card = array();

        /* 取得所有管理员，*/
        /* 标注哪些是该供货商的('this')，哪些是空闲的('free')，哪些是别的供货商的('other') */
        /* 排除是办事处的管理员 */

        $smarty->assign('ur_here', $_LANG['add_credit_card']);
        $smarty->assign('action_link', array('href' => '../../index.php?app=admin&mod=User&act=user', 'text' => '返回会员信息列表页'));

        $smarty->assign('form_action', 'insert');
        $smarty->assign('credit_card', $credit_card);

        assign_query_info();

        $smarty->display('ModifyPhonenum_info.html');

    }
    elseif ($_REQUEST['act'] == 'edit')
    {
        $credit_card = array();

        /* 取得供货商信息 */
        $id = $_REQUEST['id'];
        $sql = "SELECT * FROM " . $ecs->table('credit_card') . " WHERE id = '$id'";
        $credit_card = $db->getRow($sql);
        if (count($credit_card) <= 0)
        {
            sys_msg('credit_card does not exist');
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
        $credit_card['admin_list'] = $db->getAll($sql);

        $smarty->assign('ur_here', $_LANG['edit_credit_card']);
        $smarty->assign('action_link', array('href' => 'ModifyPhonenum.php?act=list', 'text' => $_LANG['credit_card_list']));

        $smarty->assign('form_action', 'update');
        $smarty->assign('credit_card', $credit_card);

        assign_query_info();

        $smarty->display('credit_card_info.htm');
    }

}

/*------------------------------------------------------ */
//-- 提交添加、编辑供货商
/*------------------------------------------------------ */
elseif (in_array($_REQUEST['act'], array('insert', 'update')))
{
    /* 检查权限 */
    admin_priv('credit_card_id');

    if ($_REQUEST['act'] == 'insert')
    {
        /* 提交值 */
    	$str_a=trim($_POST['Captchaold']);
    	$str_b=trim($_POST['Captchanew']);
    	$phone1 = trim( $_POST['oldphone']);
    	$phone2	= trim( $_POST['newphone']);
			   
		$userId = UserManager::getUserIdByPhone($phone1);
		$MPhone=new ModifyPhone();
		$str=array($str_a,$str_b);
		$MPhone->check($str);
		if( empty($userId) || $userId < 0 ){
			sys_msg("用户不存在");
			return;
		}
		if($ret[0]){
			sys_msg("弃用手机验证码错误");
			return;
		}
		if( empty($userId) || $userId < 0 ){
			sys_msg("现用手机验证码错误");
			return;
		}		
		
        $sql = "UPDATE " . $sns->table('user') . "
                SET phone = '$phone2'
                WHERE uid = '$userId'";
        $result=$db->query($sql);
        if ($result)
        {
			sys_msg("修改成功");
            clear_cache_files();
        }
        else
        {
        	sys_msg("修改不成功");
            make_json_result(sprintf($_LANG['agency_edit_fail'], $credit_card_id));
        }

		}


        /* 记日志 */
        admin_log($credit_card['credit_card_id'], 'add', 'credit_card');

        /* 清除缓存 */
        clear_cache_files();

        /* 提示信息 */
        $links = array(array('href' => 'ModifyPhonenum.php?act=add',  'text' => $_LANG['continue_add_credit_card']),
                       array('href' => 'ModifyPhonenum.php?act=list', 'text' => $_LANG['back_credit_card_list'])
                       );
        sys_msg($_LANG['add_credit_card_ok'], 0, $links);

    }

    if ($_REQUEST['act'] == 'update')
    {
        /* 提交值 */
        $credit_card = array('id'   => trim($_POST['id']));

       $phone = trim( $_POST['credit_card_phone']);				   
		$userId = UserManager::getUserIdByPhone( $phone );
		if( empty($userId) || $userId < 0 ){
			sys_msg($_LANG['credit_card_NO_USER']);
			return;
		}
		
        $credit_card = array(
        				   'id'               	=> trim($_POST['id'])  ,
        				   'credit_card_id'   	=> trim($credit_card_id_merg),
                           'credit_card_year'   => trim($_POST['credit_card_year']),
        				   'credit_card_month'  => trim($_POST['credit_card_month']),
                		   'credit_card_CVN2'   => trim($_POST['credit_card_CVN2']),
                		   'user_id'			=> $userId,
						   'credit_card_bank'   => trim($_POST['credit_card_bank']),
						   'credit_card_phone'   => trim($_POST['credit_card_phone'])
                           );

        /* 取得供货商信息 */
        $sql = "SELECT * FROM " . $ecs->table('credit_card') . " WHERE id = '" . $credit_card['id'] . "'";
        $credit_card['old'] = $db->getRow($sql);
        if (empty($credit_card['old']['id']))
        {
            sys_msg('credit_card does not exist');
        }

        /* 判断名称是否重复 */
        $sql = "SELECT credit_card_id,
                FROM " . $ecs->table('credit_card') . "
                WHERE id = '" . $credit_card['new']['id'] . "'
                AND credit_card_id == '" . $credit_card['credit_card_id'] . "'";
        if ($db->getOne($sql))
        {
            sys_msg($_LANG['credit_card_id_exist']);
        }

        /* 保存供货商信息 */
        $db->autoExecute($ecs->table('credit_card'), $credit_card['new'], 'UPDATE', "credit_card_id = '" . $credit_card['credit_card_id'] . "'");


        /* 记日志 */
        admin_log($credit_card['old']['credit_card_id'], 'edit', 'credit_card');

        /* 清除缓存 */
        clear_cache_files();

        /* 提示信息 */
        $links[] = array('href' => 'ModifyPhonenum.php?act=list', 'text' => $_LANG['back_credit_card_list']);
        sys_msg($_LANG['edit_credit_card_ok'], 0, $links);
    
}


/**
 *  获取供应商列表信息
 *
 * @access  public
 * @param
 *
 * @return void
 */
function credit_card_list()
{
    $result = get_filter();
    if ($result === false)
    {
        $aiax = isset($_GET['is_ajax']) ? $_GET['is_ajax'] : 0;

        /* 过滤信息 */
        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'credit_card_id' : trim($_REQUEST['sort_by']);
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
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('credit_card') . $where;
        $filter['record_count']   = $GLOBALS['db']->getOne($sql);
        $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;

        /* 查询 */
        $sql = "SELECT id,credit_card_id,credit_card_year, credit_card_month, credit_card_CVN2,user_id,credit_card_bank
                FROM " . $GLOBALS['ecs']->table("credit_card") . "
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
