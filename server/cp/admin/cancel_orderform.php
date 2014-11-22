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
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');


if($_REQUEST['act'] == 'list'){
	global $smarty , $_LANG; 
	showlist();
}
else if($_REQUEST['act'] == 'edit'){
	global $smarty , $_LANG;
	$result = eidtpage();
	sys_msg('修改成功');
	/* 模板赋值 */
    $smarty->assign('form_action', 'edit');
    $smarty->assign('list',    $result);
    /* 显示模板 */
    $smarty->display('cancel_orderform.htm'); 
}

function showlist(){
	global $smarty , $_LANG;
	
	$result = getlist();
	
    /* 模板赋值 */
    $smarty->assign('form_action', 'edit');
    $smarty->assign('list',    $result);

    /* 显示模板 */
    $smarty->display('cancel_orderform.htm');
}

function getlist()
{		
	global $ecs;
	global $db;

	
    $sql = "SELECT * FROM " .  $ecs->table('sys_config');
    $list = $db->getAll($sql);
    return $list;
}



function eidtpage() {
	global $ecs;
	global $db;
	$str_a=trim($_POST['time']);
    $str_b=trim($_POST['value']);			   
    $list = array();
   	$list = getlist();
   	if ($list) {
   	deletepage();
   	}
   	$str = array($str_a,$str_b);
	$res = array();
    $res = insertpage($str);
  	return $res;
	
}


function insertpage($str) {
	global $ecs;
	global $db;
	$sql_a = "INSERT ". $ecs->table("sys_config")."(type,param,value)VALUES('cancel_order','time',{$str[0]})";	
	$sql_b = "INSERT ". $ecs->table("sys_config")."(type,param,value)VALUES('cancel_order','percent',{$str[1]})";
	if ($str[0]) {
		$result = $db->query($sql_a);		
	}
	if ($str[1]) {
		$result2 = $db->query($sql_b);
	}
	$res = getlist();
	return $res;

}

function deletepage() {
	global $ecs;
	global $db;
	global $smarty;
	$sql = "DELETE FROM ".$ecs->table("sys_config");

	$db->query($sql);			   
}

