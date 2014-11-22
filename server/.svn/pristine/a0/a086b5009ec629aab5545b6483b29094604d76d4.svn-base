<?php
//recharge表  id(主键) user_id(用户id) amount(充值金额) operator_id(操作人id) recharge_time(充值时间) reason(充值原因)
define('IN_ECS', true);
require (dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . '../hfrm/Frm.php');
include_once (ROOT_PATH . 'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);
/*------------------------------------------------------ */
//-- 充值记录列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list') 
{  
	$smarty -> assign('full_page', 1);

    /* 获取充值记录数据 */
	$array = get_recharge_records();
	//print_r($array);
	$smarty -> assign('recharge', $array['recharge']);
	$smarty -> assign('filter', $array['filter']);
	$smarty -> assign('record_count', $array['record_count']);
	$smarty -> assign('page_count', $array['page_count']);
	assign_query_info(); 
	$smarty -> display('recharge_list.htm');
}
/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    /* 获取充值记录数据 */
    $array = get_recharge_records();

    $smarty->assign('recharge',      $array['recharge']);
    $smarty->assign('filter',        $array['filter']);
    $smarty->assign('record_count',  $array['record_count']);
    $smarty->assign('page_count',    $array['page_count']);

    make_json_result($smarty -> fetch('recharge_list.htm'), '',
    array('filter' => $array['filter'], 'page_count' => $array['page_count']));
}


function get_recharge_records($where = '') 
{    
	$result = get_filter();
	$db = Frm::getInstance() -> getDb();
	if ($result === false) 
	{


        $filter['userName'] = empty($_REQUEST['userName']) ? '' : trim($_REQUEST['userName']);
        $filter['phone'] = empty($_REQUEST['phone']) ? '' : trim($_REQUEST['phone']);
        $filter['operator'] = empty($_REQUEST['operator']) ? '' : trim($_REQUEST['operator']);
        
        $filter['reason'] = empty($_REQUEST['reason']) ? '' : trim($_REQUEST['reason']);
        $filter['amount1'] = empty($_REQUEST['amount1']) ? '' : intval($_REQUEST['amount1']);
        $filter['amount2'] = empty($_REQUEST['amount2']) ? '' : intval($_REQUEST['amount2']);

        $filter['start_time'] = empty($_REQUEST['start_time']) ? -1 : strtotime($_REQUEST['start_time']);
        $filter['end_time'] = empty($_REQUEST['end_time']) ? -1 : strtotime($_REQUEST['end_time']);
        $where = 'WHERE 1 ';

        if ($filter['userName'])
        {
            $where .= " AND u.true_name LIKE '%" . mysql_like_quote($filter['userName']) . "%'";
        }
        if ($filter['phone'])
        {
            $where .= " AND u.phone LIKE '%" . mysql_like_quote($filter['phone']) . "%'";
        } 
        if ($filter['operator'])
        {
            $where .= " AND a.true_name LIKE '%" . mysql_like_quote($filter['operator']) . "%'";
        }
        if ($filter['reason'])
        {
            $where .= " AND r.reason LIKE '%" . mysql_like_quote($filter['reason']) . "%'";
        }
        //echo $filter['amount1'];
        if ( $filter['amount1'] && $filter['amount1']<=$filter['amount2'])
        {
        	$filter['amount1']=$filter['amount1']*100;
        	$filter['amount2']=$filter['amount2']*100;
            $where .= " AND r.amount BETWEEN {$filter['amount1']} AND {$filter['amount2']} ";
        }

        if (-1!=$filter['start_time'] && $filter['start_time']<=$filter['end_time'])
        {
            $where .= " AND r.recharge_time BETWEEN {$filter['start_time']} AND {$filter['end_time']} ";
        }
       
		/* 分页大小 */
		$filter = array();
     
		$sql = "SELECT COUNT(*) 
		        FROM edo_cp_recharge AS r 
		        	LEFT JOIN edo_user AS u 
		        ON r.user_id=u.uid 
		        	LEFT JOIN edo_user AS a 
		        ON r.operator_id=a.uid ".
		        $where;

		$filter['record_count'] = $db -> getOne($sql);
        //echo $filter['record_count'];
		$filter = page_and_size($filter);
        
        //print_r($filter);echo "<br>";
		//购买时间,发送状态,发卡员,发送时间,城市,邮寄地址
		$sql = "SELECT u.uid , u.true_name AS userName , u.phone , r.id , r.amount , r.reason , r.recharge_time ,
		        	r.balance , a.true_name AS operator
		        FROM edo_cp_recharge AS r 
		        	LEFT JOIN edo_user AS u 
		        ON r.user_id=u.uid 
		        	LEFT JOIN edo_user AS a 
		        ON r.operator_id=a.uid ".
		        $where.
		        "ORDER BY r.recharge_time DESC"; 
		set_filter($filter, $sql);  //echo $sql;      
	} 

	else 
	{
		$sql = $result['sql'];
		$filter = $result['filter'];
	}
	$res = $db -> limitSelect($sql, $filter['start'], $filter['page_size']);
    //print_r($res);echo "dddd";
	$arr = array();
	foreach ($res as $rows)
	{
		$rows['recharge_time']=date('Y-m-d H:i:s',$rows['recharge_time']);
		//echo $rows['recharge_time'];
		//$rows['userName']=$rows['userName'];
		//$rows['phone']=$rows['phone'];
		//$rows['operator']=$rows['operator'];
		$rows['amount']=floatval($rows['amount']/100);
		//$rows['reason']=$rows['reason'];
		$rows['balance']=floatval($rows['balance']/100);
        $arr[]=$rows;
	} 
	return array('recharge' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}
