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
 * $Id: medal.php 15013 2009-05-13 09:31:42Z wanglei $
 */

if(!defined('ROOT_PATH'))
{
	define('IN_ECS', true);
	require(dirname(__FILE__) . '/includes/init.php');
}
require_once( dirname(__FILE__) . "/../../medal_container/MedalContainer.php" );
$medalPath = "marketing/medal/classes/medals/";

$medalData=array(
				$medalPath."FirstExperienceMedal.php"=>"免费初体验",
				$medalPath."EdoBigGun.php"=>"易多达人"
				);

/*------------------------------------------------------ */
//-- 供货商列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
     /* 检查权限 */
     //admin_priv('id');

    /* 查询 */
    $result = medal_list();
    /* 模板赋值 */
    $smarty->assign('ur_here', $_LANG['medal_list']); // 当前导航
    $smarty->assign('action_link', array('href' => 'medal.php?act=add', 'text' => $_LANG['add_medal']));

    $smarty->assign('full_page',        1); // 翻页参数

    $smarty->assign('medal_list',    $result['result']);
    $smarty->assign('filter',       $result['filter']);
	$smarty->assign('record_count', $result['record_count']);
    $smarty->assign('page_count',   $result['page_count']);
  	
	
    /* 显示模板 */
    //assign_query_info();
    $smarty->display('medal_list.htm');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
   

    $result = medal_list();

    $smarty->assign('medal_list',    $result['result']);
    $smarty->assign('filter',       $result['filter']);
    $smarty->assign('record_count', $result['record_count']);
    $smarty->assign('page_count',   $result['page_count']);

    /* 排序标记 */
   

    make_json_result($smarty->fetch('medal_list.htm'), '',
        array('filter' => $result['filter'], 'page_count' => $result['page_count']));
}

/*------------------------------------------------------ */
//-- 列表页编辑名称
/*------------------------------------------------------ */


/*------------------------------------------------------ */
//-- 删除供货商
/*------------------------------------------------------ */
elseif (in_array($_REQUEST['act'], array('remove', 'batch')))
{
	if ($_REQUEST['act'] == 'remove')
	{
		
		
		$id = intval($_REQUEST['id']);
		$sql = "select icon from". $ecs->table('medal') ."where id = {$id}";
		$icon = $db->getOne($sql);
		$del = MedalContainer::getInstance();
		$row = $del -> delMedal($id);
		unlink($icon);


		$url = 'medal.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
		ecs_header("Location: $url\n");
	}
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
	        //admin_priv('id');
	
	        $id = $_POST['checkboxes'];
	 		
	
	        if (isset($_POST['remove']))
	        {
	        	for ($i=0;$i<count($id);$i++)
		        {
		        	$sql = "select icon from". $ecs->table('medal') ."where id = {$id[$i]}";
					$icon = $db->getOne($sql);
					
		           	$del = new MedalContainer; 
					$row = $del -> delMedal($id[$i]);
					
						unlink($icon);
		        }
	            if (empty($row))
	            {
	                sys_msg($_LANG['batch_drop_no']);
	            }
	            
	
	           
				/* 清除缓存 */
				clear_cache_files();
				$links[] = array('href' => 'medal.php?act=list', 'text' => $_LANG['back_medal_list']);
				sys_msg($_LANG['batch_drop_ok'],0,$links);
			}
		}
	}	
		exit;
	}




/*------------------------------------------------------ */
//-- 添加、编辑供货商
/*------------------------------------------------------ */
elseif (in_array($_REQUEST['act'], array('add', 'edit')))
{
	/* 检查权限 */
	//admin_priv('medal_id');

	if ($_REQUEST['act'] == 'add')
	{
		$medal = array();

		/* 取得所有管理员，*/
		/* 标注哪些是该供货商的('this')，哪些是空闲的('free')，哪些是别的供货商的('other') */
		/* 排除是办事处的管理员 */

		$smarty->assign("medalData",$medalData);

		$smarty->assign('ur_here', $_LANG['add_medal']);
		$smarty->assign('action_link', array('href' => 'medal.php?act=list', 'text' => $_LANG['medal_list']));

		$smarty->assign('form_action', 'insert');
		$smarty->assign('medal', $medal);

	

		$smarty->display('medal_info.htm');

	}
	

}

/*------------------------------------------------------ */
//-- 提交添加、编辑供货商
/*------------------------------------------------------ */
elseif (in_array($_REQUEST['act'], array('insert', '')))
{
    /* 检查权限 */
   	


    if ($_REQUEST['act'] == 'insert')
    {
        /* 提交值 */
    	$id=$_POST['id'];
    	
    	$name=$_POST['name'];
    	
    	$file=$_POST['file'];
    	//处理生成类名
		preg_match( '#medals/.*.php$#', $file, $outStr );
		$repStr = array("medals/",".php");
		$classname=str_replace($repStr,"",$outStr[0]);
		
    	$icon=$_FILES['icon'];
    	$medaldesc=$_POST['medaldesc'];	
		if( empty($name) ){
			sys_msg($_LANG['medal_name']);
			exit();
		}
		else
		{
			//判断上传是否失败
			if(empty($_FILES))
			{
				sys_msg($_LANG['medal_noadd']);
				exit();
			}
			//判断error判断size
			if($icon['error']=0 && $icon['size']>0)
			{
				sys_msg($_LANG['medal_noadd']);
				exit();
			}
			//文件类型是否非法类型.只能上传图片
			//echo $_FILES['my_file']['type'];//app.jpg
			//所有图片类型
			//1.jpg
			$allowtype = array("jpg","jpeg","pjpeg","gif","png","bmp");
			$array = explode(".",$_FILES['icon']['name']);
			//png
			$ext = $array[count($array)-1];
			//php内置函数   把png 去数组 比较 true false
			if(!in_array($ext,$allowtype))
			{
				echo "非法类型";
				exit();//仃止php程序执行
			}
			//将临时文件..移动指定目录;
			//c:/windows/temp/
			$src = $_FILES['icon']['tmp_name'];
			$iconName=uniqid();
			$des = "../data/medal/".$iconName.".".$ext;
			$storePath = "/cp/data/medal/".$iconName.".".$ext;
			if(move_uploaded_file($src,$des))
			{
				echo "上传成功！！！";
			}else{
				sys_msg($_LANG['medal_noadd']);
				exit();
			}



			$add = MedalContainer::getInstance(); 
			$row = $add -> addMedal($name , $file , $classname , $storePath , $medaldesc );
			// echo $row;
			if($row<=0){
				sys_msg($_LANG['medal_noadd']);
			}else{
				sys_msg($_LANG['medal_add']);
				
			}
		}
		
		 
       
		
      
        $db->autoExecute($ecs->table('medal'), $medal, 'INSERT');
        $medal['id'] = $db->insert_id();

		}


        /* 记日志 */
        admin_log($medal['medal_id'], 'add', 'medal');

        /* 清除缓存 */
        clear_cache_files();

        /* 提示信息 */
        $links = array(array('href' => 'medal.php?act=add',  'text' => $_LANG['continue_add_medal']),
                       array('href' => 'medal.php?act=list', 'text' => $_LANG['back_medal_list'])
                       );
        sys_msg($_LANG['add_medal_ok'], 0, $links);

    }
    



/**
 *  获取供应商列表信息
 *
 * @access  public
 * @param
 *
 * @return void
 */
function medal_list()
{
    $result = get_filter();
    if ($result === false)
    {
        $aiax = isset($_GET['is_ajax']) ? $_GET['is_ajax'] : 0;

        /* 过滤信息 */
        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'id' : trim($_REQUEST['sort_by']);
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
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('medal') . $where;
        $filter['record_count']   = $GLOBALS['db']->getOne($sql);
        $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;

        /* 查询 */
        $sql = "SELECT id,name,file, class_name, icon, medaldesc
                FROM " . $GLOBALS['ecs']->table("medal") . "
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
