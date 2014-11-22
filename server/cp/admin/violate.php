<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);

$exc = new exchange($ecs->table("vip_card"), $db, 'id', 'serial');
/*------------------------------------------------------ */
//-- 会员卡列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    //echo $_LANG['06_equite_list'];
    $smarty->assign( 'ur_here',      $_LANG['11_vip_card_list']);
    $smarty->assign( 'action_link',  array('text' => $_LANG['11_vip_card_add'], 'href' => 'vip_card.php?act=add'));
    $smarty->assign( 'action_link2',  array('text' => $_LANG['11_vip_card_add_excel'], 'href' => 'vip_card.php?act=add_excel'));
    $smarty->assign( 'full_page',    1);
    
    $brand_list = get_brandlist();
    //print_r($brand_list['brand']);
    $smarty->assign('brand_list',   $brand_list['brand']);
    $smarty->assign('filter', $brand_list['filter']);
   $smarty->assign('record_count', $brand_list['record_count']);
    $smarty->assign('page_count',   $brand_list['page_count']);
    assign_query_info();
    $smarty->display('vip_card_list.htm');
}

/*------------------------------------------------------ */
//-- 添加会员卡
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    /* 权限判断 */
    admin_priv('brand_manage');
    $smarty->assign('ur_here',     $_LANG['11_vip_card_add']);
    $smarty->assign('action_link', array('text' => $_LANG['11_vip_card_list'], 'href' => 'vip_card.php?act=list'));
    $smarty->assign('form_action', 'insert');
    assign_query_info();
    $smarty->assign('brand', array('sort_order'=>50, 'is_show'=>1));
    $smarty->display('vip_card_info.htm');
}
elseif ($_REQUEST['act'] == 'insert')
{
    /*检查会员卡卡名是否重复*/
    admin_priv('brand_manage');
//v.id,v.serial,v.group_number,v.card_number,v.card_type,v.using_status,v.allot_status
    $using_status = isset($_REQUEST['using_status']) ? intval($_REQUEST['using_status']) : 0;
    $allot_status = isset($_REQUEST['allot_status']) ? intval($_REQUEST['allot_status']) : 0;
    /*插入数据*/

    $sql = "INSERT INTO ".$ecs->table('vip_card')."(serial, group_number, card_number, card_type, using_status,allot_status,sort_order) ".
           "VALUES ('$_POST[serial]', '$_POST[group_number]',  '$_POST[card_number]',  '$_POST[card_type]', '$using_status','$allot_status','$_POST[sort_order]')";
    $db->query($sql);

    admin_log($_POST['serial'],'add','vip_card');

    /* 清除缓存 */
    clear_cache_files();

    $link[0]['text'] = $_LANG['continue_add'] ;
    $link[0]['href'] = 'vip_card.php?act=add' ;

    $link[1]['text'] = $_LANG['back_list'] ;
    $link[1]['href'] = 'vip_card.php?act=list';

    sys_msg($_LANG['brandadd_succed'], 0, $link);
}


elseif($_REQUEST['act'] == 'add_excel')
{
    //excel_add upload
    //assign_query_info();
    admin_priv('brand_manage');
    $smarty->assign( 'ur_here',      $_LANG['11_vip_card_list']);
    $smarty->assign('action_link', array('text' => $_LANG['11_vip_card_list'], 'href' => 'vip_card.php?act=list'));
    
    $smarty->assign('form_action', 'upload');
    $smarty->display('vip_excel_add.htm');
}

elseif($_REQUEST['act'] == 'upload')
{
    if ($_FILES["file"]["error"] > 0)
    {
        echo "Error: " . $_FILES["file"]["error"] . "<br />";
        $link[0]['text'] = $_LANG['upload_fail'] ;
        $link[0]['href'] = 'vip_card.php?act=add_excel';
        sys_msg($_LANG['upload_fail'], 0, $link);
    }
    else
    {
//        echo "Upload: " . $_FILES["file"]["name"] . "<br />";
//        echo "Type: " . $_FILES["file"]["type"] . "<br />";
//        echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
//        echo "Stored in: " . $_FILES["file"]["tmp_name"]."</br>";
        require_once dirname(__FILE__) .'/includes/Excel/reader.php';
        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('utf-8');
        $data->read($_FILES["file"]["tmp_name"]);
        error_reporting(E_ALL ^ E_NOTICE);
        //echo $data->sheets[0]['numCols'];
        //die();
        //INSERT INTO  (`serial`, `group_number`, `card_number`, `card_type`, `using_status`, `allot_status`, `sort_order`)
        // VALUES ('fe1323fac312', '123456789012', '3453454614243', '0', '0', '0', '50');
        
        $values = "";
        $all_error_messages = array();
        for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) 
        {         $error_bool = FALSE ;
                  $strTmp = '( ';
                  $messages = "";
	for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) 
                    {
                            $a = trim($data->sheets[0]['cells'][$i][$j]);
                           
                            switch($j)
                            {
                                case 1:
                                    //验证serial格式 16进制，8位
                                    if(!eregi("^[0-9a-f]{8}$", $a) || strlen($a) != 8 )
                                    {
                                        $link[0]['text'] = $_LANG['exceladd_fail'] ;
                                        $link[0]['href'] = 'vip_card.php?act=add_excel';
                                        $messages = $messages .'serial格式错误,';
                                        
                                        $error_bool = true;
                                        
                                    }
                                    else
                                    {                                                                               
                                        $sql = " SELECT COUNT(*) FROM ".$ecs->table('vip_card') .
                                                    " WHERE serial = '$a '";
                                        $count_col = $db->getOne($sql);
                                        if($count_col == 0)
                                        {
                                            $strTmp = $strTmp." '".$a."', ";
                                        }
                                        else
                                        {
                                            $messages = $messages.'serial已存在,';
                                            $error_bool = true;
                                        }
                                    }
                                    $error_messages['serial'] = $a ;
                                    $error_messages['code'] = $messages;
                                    break;
                                case 2:
                                    //验证group_number格式 用户编号12位，直接是顺序编号
                                    if(strlen($a) != 12 || !Is_Numeric($a))
                                    {
                                        $link[0]['text'] = $_LANG['exceladd_fail'] ;
                                        $link[0]['href'] = 'vip_card.php?act=add_excel';
                                        $error_bool = true;
                                    }
                                    else
                                    {
                                        $sql = " SELECT COUNT(*) FROM ".$ecs->table('vip_card') .
                                                    " WHERE group_number = '$a' ";
                                        $count_col = $db->getOne($sql);
                                        if($count_col == 0)
                                        {
                                            $strTmp = $strTmp." '".$a."', ";
                                        }
                                        else
                                        {
                                            $messages = $messages.'用户编号已存在,';
                                            $error_bool = true;
                                        }
                                    }
                                    $error_messages['group_number'] = $a ;
                                    $error_messages['code'] = $messages;
                                    
                                    
                                    break;
                                case 3:
                                    //验证card_number格式 卡号采用12位，头4为是区号，后面8位是顺序编号
                                    if(strlen($a) != 12 || !Is_Numeric($a))
                                    {
                                        $link[0]['text'] = $_LANG['exceladd_fail'] ;
                                        $link[0]['href'] = 'vip_card.php?act=add_excel';
                                        $error_bool = true;
                                        
                                        
                                    }
                                    else
                                    {
                                        $sql = " SELECT COUNT(*) FROM ".$ecs->table('vip_card') .
                                                    " WHERE card_number = '$a' ";
                                        $count_col = $db->getOne($sql);
                                        if($count_col == 0)
                                        {
                                            $strTmp = $strTmp." '".$a."', ";
                                        }
                                        else
                                        {
                                            $messages = $messages.'卡号已存在,';
                                            $error_bool = true;
                                        }
                                    }
                                    $error_messages['card_number'] = $a ;
                                    $error_messages['code'] = $messages;
                                    
                                    break;
                                case 4:
                                    //验证card_type
                                    if(strlen($a) != 1 || !Is_Numeric($a))
                                    {
                                        $link[0]['text'] = $_LANG['exceladd_fail'] ;
                                        $link[0]['href'] = 'vip_card.php?act=add_excel';
                                        $error_bool = true;
                                    }
                                    else
                                    {
                                        $strTmp =$strTmp . " '".$a."', ";
                                    }
                                    $error_messages['card_type'] = $a ;
                                    $error_messages['code'] = $messages;
                                    break;
                                    
                            }

                }
                
                if ($error_bool)
                {
                    //sys_msg($_LANG['brandadd_fail'], 1, $link,FALSE);
                    array_push($all_error_messages,$error_messages);
                    
                }
                else
                {
                    $values = $strTmp."  '0', '0', '50') ";
                    $sql = "INSERT INTO ".  $ecs->table('vip_card') ."  (serial, group_number, card_number, card_type, using_status, allot_status, sort_order) VALUES " .$values;
                    admin_log($_POST['serial'],'add','vip_card');
                    /*验证卡号序列号等是否有重复*/
                    $db->query($sql);
                }
                
        }
        $smarty->assign( 'full_page',    1);
        if(!empty($all_error_messages))
        {
            $smarty->assign('ur_here',     $_LANG['brand_edit']);
            $smarty->assign('action_link', array('text' => $_LANG['11_vip_card_list'], 'href' => 'vip_card.php?act=list&' . list_link_postfix()));
            $smarty->assign('brand_list',       $all_error_messages);
            assign_query_info();

            $smarty->display('vip_error_msg.htm');
        }
        else
        {
                /* 清除缓存 */
            clear_cache_files();
            $link[0]['text'] = $_LANG['continue_add'] ;
            $link[0]['href'] = 'vip_card.php?act=add_excel' ;

            $link[1]['text'] = $_LANG['back_list'] ;
            $link[1]['href'] = 'vip_card.php?act=list';

            sys_msg($_LANG['brandadd_succed'], 0, $link);
        }

    }
    




}
/*------------------------------------------------------ */
//-- 编辑vip卡
/*------------------------------------------------------ */
elseif($_REQUEST['act']=='edit')
{
     /* 权限判断 */
     
    admin_priv('brand_manage');
    $sql = "SELECT * ".
            "FROM " .$ecs->table('vip_card'). " WHERE id='$_REQUEST[id]'";
    $brand = $db->GetRow($sql);
    //print_r($sql);
    $smarty->assign('ur_here',     $_LANG['brand_edit']);
    $smarty->assign('action_link', array('text' => $_LANG['11_vip_card_list'], 'href' => 'vip_card.php?act=list&' . list_link_postfix()));
    $smarty->assign('brand',       $brand);
    $smarty->assign('form_action', 'updata');

    assign_query_info();
   
    $smarty->display('vip_card_info.htm');
}


elseif ($_REQUEST['act'] == 'updata')
{
    admin_priv('brand_manage');

    /*对描述处理*/
    if (!empty($_POST['brand_desc']))
    {
        $_POST['brand_desc'] = $_POST['brand_desc'];
    }

    $using_status = isset($_REQUEST['using_status']) ? intval($_REQUEST['using_status']) : 0;
    $allot_status = isset($_REQUEST['allot_status']) ? intval($_REQUEST['allot_status']) : 0;

    /* 处理图片 */
    $img_name = basename($image->upload_image($_FILES['brand_logo'],'brandlogo'));
    //v.id,v.serial,v.group_number,v.card_number,v.card_type,v.using_status,v.allot_status
    $param = "id = '$_POST[id]',serial = '$_POST[serial]',  group_number='$_POST[group_number]', card_number='$_POST[card_number]',  card_type='$_POST[card_type]', using_status='$_POST[using_status]', allot_status = '$_POST[allot_status]' ,sort_order='$_POST[sort_order]' ";
 

    if ($exc->edit($param,  $_POST['id']))
    {
        /* 清除缓存 */
        clear_cache_files();

        admin_log($_POST['brand_name'], 'edit', 'brand');
        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'vip_card.php?act=list&' . list_link_postfix();
        $note = vsprintf($_LANG['brandedit_succed'], $_POST['serial']);
        sys_msg($note, 0, $link);
    }
    else
    {
        die($db->error());
    }
}

/*------------------------------------------------------ */
//-- 删除vip卡
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('brand_manage');

    $id = intval($_GET['id']);


    $exc->drop($id);


    $url = 'vip_card.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
    
    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 切换是否显示
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_show_using_status')
{
    check_authz_json('brand_manage');

    $id     = intval($_POST['id']);
    $val    = intval($_POST['val']);

    $exc->edit("using_status='$val'", $id);

    make_json_result($val);
}
/*------------------------------------------------------ */
//-- 切换是否显示
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_show_allot_status')
{
    check_authz_json('brand_manage');

    $id     = intval($_POST['id']);
    $val    = intval($_POST['val']);

    $exc->edit("allot_status='$val'", $id);

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

    make_json_result($smarty->fetch('vip_card_list.htm'), '',
        array('filter' => $brand_list['filter'], 'page_count' => $brand_list['page_count']));
}



/**
 * 获取vip卡列表列表
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

       
            $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('vip_card');
        

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
            $sql = " SELECT v.id,v.serial,v.group_number,v.card_number,v.card_type,v.using_status,v.allot_status,u.uname
                        FROM ".$GLOBALS['ecs']->table('vip_card'). " v ".
                        " LEFT JOIN ".$GLOBALS['sns']->table('user')." u ".
                        " ON v.id = u.vip_card_id ".
                        //LEFT JOIN". $GLOBALS['ecs']->table('equipment'). " e on e.sim = s.id ".  
                       // "LEFT JOIN".$GLOBALS['ecs']->table('car')." c on c.equipment = e.id ".
                    //`allot_status``using_status``card_type``card_number``group_number``serial
                        "WHERE v.serial like '%{$keyword}%' or v.group_number like '%{$keyword}%' or v.card_number like '%{$keyword}%' or v.card_type like '%{$keyword}%'  or v.using_status like '%{$keyword}%' or v.allot_status like '%{$keyword}%' or u.uname like '%{$keyword}%'".
                        "ORDER BY v.sort_order ASC";

        
            //$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('oil_card')." WHERE card_id like '%{$keyword}%' or password like '%{$keyword}%' or provider like '%{$keyword}%' ORDER BY sort_order ASC";
        }
        else
        {//手机号码,供应商,状态,服务密码,供应商网络登录 密码,归属地
            $sql = " SELECT v.id,v.serial,v.group_number,v.card_number,v.card_type,v.using_status,v.allot_status,u.uname
                        FROM ".$GLOBALS['ecs']->table('vip_card'). " v ".
                        " LEFT JOIN ".$GLOBALS['sns']->table('user')." u ".
                        " ON v.id = u.vip_card_id ".
                        " ORDER BY v.sort_order ASC";
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

?>