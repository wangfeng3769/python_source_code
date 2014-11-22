<?php

/**
 * snsHOP  管理中心管理员留言程序
 * ============================================================================
 * 版权所有 2005-2010 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.snshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: message.php 17063 2010-03-25 06:35:46Z liuhui $
*/


if(!defined('ROOT_PATH'))
{
	define('IN_ECS', true);
	require(dirname(__FILE__) . '/includes/init.php');
}

/* act操作项的初始化 */
$_REQUEST['act'] = trim($_REQUEST['act']);
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}

/*------------------------------------------------------ */
//-- 留言列表页面
/*------------------------------------------------------ */


if ($_REQUEST['act'] == 'list')
{
	/* 检查权限 */
	//admin_priv('id');

	/* 查询 */
	$result = message_list();
	/* 模板赋值 */
	$smarty->assign('ur_here', $_LANG['message_list']); // 当前导航
	

	$smarty->assign('full_page',        1); // 翻页参数

	$smarty->assign('message_list',    $result['result']);
	$smarty->assign('filter',       $result['filter']);
	$smarty->assign('record_count', $result['record_count']);
	$smarty->assign('page_count',   $result['page_count']);
	 

	/* 显示模板 */
	//assign_query_info();
	$smarty->display('message_list.htm');
}
/*------------------------------------------------------ */
//-- 翻页、排序
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $list = get_message_list();

    $smarty->assign('message_list', $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('message_list.htm'), '',
        array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}


/**
 *  获取供应商列表信息
 *
 * @access  public
 * @param
 *
 * @return void
 */
function message_list()
{
    $result = get_filter();
    if ($result === false)
    {
        $aiax = isset($_GET['is_ajax']) ? $_GET['is_ajax'] : 0;

        /* 过滤信息 */
        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'message_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'ASC' : trim($_REQUEST['sort_order']);

        $where = 'WHERE a.from_uid=b.uid ';

        /* 分页大小 */
        $filter['page'] = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);

        if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0)
        {
            $filter['page_size'] = intval($_REQUEST['page_size']);
        }
        elseif (isset($_COOKIE['snsCP']['page_size']) && intval($_COOKIE['snsCP']['page_size']) > 0)
        {
            $filter['page_size'] = intval($_COOKIE['snsCP']['page_size']);
        }
        else
        {
            $filter['page_size'] = 15;
        }

        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['sns']->table('message') . " as
                a , ".$GLOBALS['sns']->table('user')." as b " . $where;
        $filter['record_count']   = $GLOBALS['db']->getOne($sql);
        $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;

        /* 查询 */
        $sql = "SELECT a.message_id,a.content,a.is_read, b.uname
                FROM " . $GLOBALS['sns']->table("message") . " as
                a , ".$GLOBALS['sns']->table('user')." as b  
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
?>
