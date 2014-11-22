
<?php

 /**
 * ECSHOP 商品管理程序
 * ============================================================================
 * 版权所有 2005-2010 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: wangleisvn $
 * $Id: goods.php 17114 2010-04-16 07:13:03Z wangleisvn $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

include_once(ROOT_PATH . '/includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);
$exc = new exchange($ecs->table('goods'), $db, 'goods_id', 'goods_name');

/*------------------------------------------------------ */
//-- 商品列表，商品回收站
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'list')
{
    /* 标签初始化 */


    //assign_query_info();
//    $flash_dir = ROOT_PATH . 'data/flashdata/';
//    $smarty->assign('current', 'sys');
//    $smarty->assign('group_list', $group_list);
//    $smarty->assign('group_selected', $_CFG['index_ad']);
//    $smarty->assign('action_link_special', array('text' => $_LANG['add_new'], 'href' => 'flashplay.php?act=add'));
//    $smarty->assign('flashtpls', get_flash_templates($flash_dir));
//    $smarty->assign('current_flashtpl', $_CFG['flash_theme']);
//    $smarty->assign('playerdb', $playerdb);
//    $smarty->display('flashplay_list.htm');
//    $group_list = array(
//    'sys' => array('text' => $_LANG['06_car_price_tmpl'], 'url' => ''),
//    'cus' => array('text' => $_LANG['custom_set'], 'url' => 'flashplay.php?act=custom_list')
//                       );
    $smarty->assign( 'ur_here',      $_LANG['06_car_price_tmpl']);
//    $smarty->assign( 'action_link',  array('text' => $_LANG['11_vip_card_add'], 'href' => 'vip_card.php?act=add'));
//    $smarty->assign( 'action_link2',  array('text' => $_LANG['11_vip_card_add_excel'], 'href' => 'vip_card.php?act=add_excel'));
    $smarty->assign( 'full_page',    1);
    $brand_list = @get_brandlist();
    $smarty->assign('brand_list',   $brand_list['brand']);
    $smarty->assign('filter', $brand_list['filter']);
    $smarty->assign('record_count', $brand_list['record_count']);
    $smarty->assign('page_count',   $brand_list['page_count']);
    assign_query_info();
    $smarty->display('car_price_tmpl_list.htm');
}

/*------------------------------------------------------ */
//-- 编辑vip卡
/*------------------------------------------------------ */
elseif($_REQUEST['act']=='edit')
{
    if($_REQUEST['type']== 1 )
    {
        $title = $_LANG['holiday'];
    }
    else
    {
        $title = $_LANG['workday'];
        $_REQUEST['type']== 0;
    }

     /* 权限判断 */
     
    admin_priv('brand_manage');
    /*判断此车此否存在计价模板*/
    
    $sql = "SELECT * ".
            "FROM " .$ecs->table('car_price_base'). 
            " WHERE car_id='$_REQUEST[id]'
              AND   type = $_REQUEST[type]";
    $base_info = $db->getRow($sql);
    $base_info = formatPrice($base_info,array('hour_price','top_price','price_km','price_time_out'),0);

   
    $sql =  "SELECT c.id as cid,cpd.* ".
            " FROM ".$ecs->table('car').' c '.
            " LEFT JOIN "."(SELECT * FROM ".$ecs->table('car_price_detail')." where type=$_REQUEST[type] )".' cpd '.
            " ON c.id=cpd.car_id ".
            " WHERE c.id='$_REQUEST[id]'ORDER BY time_from  ";
    $detail_list = $db->getAll($sql);
    $detail_list = (!empty($detail_list) ? $detail_list : '');
 
    $detail_list = formatPrice($detail_list,array('price'),1);


    // $sql = "SELECT * ".
    //         " FROM ".$ecs->table('car_price_top').
    //         " WHERE car_id='$_REQUEST[id]'
    //           AND   type = $_REQUEST[type]";

    $sql =  "SELECT c.id as cid,cpt.* ".
            " FROM ".$ecs->table('car').' c '.
            " LEFT JOIN "."(SELECT * FROM ".$ecs->table('car_price_top')." where type=$_REQUEST[type] )".' cpt '.
            " ON c.id=cpt.car_id ".
            " WHERE c.id='$_REQUEST[id]' ORDER BY  cpt.time_from, cpt.car_id, cpt.date_from,cpt.period_top";
    $top_list = $db->getAll($sql);       
    $top_list = (!empty($top_list) ? $top_list : '');
    
    $top_list = formatPrice($top_list,array('period_top'),1);


    $smarty->assign('car_id',$_REQUEST[id]);
    $smarty->assign('type', $_REQUEST[type]);
    $smarty->assign('ur_here',     $title);
    $smarty->assign('action_link', array('text' => $_LANG['06_car_price_tmpl'], 'href' => 'car_price_tmpl.php?act=list&' . list_link_postfix()));
    $smarty->assign('base_info',       $base_info);
    $smarty->assign('detail_list',       $detail_list);
    $smarty->assign('top_list',       $top_list);
    $smarty->assign('form_act', 'updata');

    assign_query_info();
   
    $smarty->display('car_price_detail_info.htm');
    }
    

    
    elseif ($_REQUEST['act'] == 'updata')
    {
        admin_priv('brand_manage');
        // print_r($_REQUEST);
        // die();
        // 
        /*update base info*/
        $sql =  " DELETE 
                FROM ".$ecs->table('car_price_base').
                " WHERE car_id = '$_REQUEST[car_id]' ".
                "  AND  `type` = $_REQUEST[type] ;";
        $db->query($sql); 
       
        
        $_REQUEST[base_hour_price] = $_REQUEST[base_hour_price]*100;
        $_REQUEST[base_top_price] = $_REQUEST[base_top_price]*100;
        $_REQUEST[base_price_km] = $_REQUEST[base_price_km]*100;
        $_REQUEST[base_price_time_out] = $_REQUEST[base_price_time_out]*100;
        $sql =  " INSERT INTO ".$ecs->table('car_price_base').
                " ( hour_price, hour_km, top_price, top_km, price_km, price_time_out, car_id, `type`) ".
                " VALUES ( $_REQUEST[base_hour_price], $_REQUEST[base_hour_km], $_REQUEST[base_top_price], $_REQUEST[base_top_km], $_REQUEST[base_price_km], $_REQUEST[base_price_time_out], $_REQUEST[car_id],$_REQUEST[type] );" ;      
     
       $a = $db->query($sql);  
        
    
        /*UPDATE detail info*/
		
        $strTmp = "";
        for ($i=1; $i < count($_REQUEST[detail_start_hour]); $i++) 
        { 
            if($i == count($_REQUEST[detail_start_hour]) -1)
            {
				if($_REQUEST[detail_contain_km][$i] != ""){
                $strTmp = $strTmp."  ({$_REQUEST[car_id]},
                                     {$_REQUEST[detail_start_hour][$i]}, 
                                     {$_REQUEST[detail_end_hour][$i]}, 
                                     {$_REQUEST[detail_hour_km][$i]}*100, 
                                     {$_REQUEST[detail_contain_km][$i]}, 
                                     {$_REQUEST[type]}
                                     ) ;";
				}
				
            }
            else
            {
            	if($_REQUEST[detail_contain_km][$i] != "")
            	{
                $strTmp = $strTmp."  ({$_REQUEST[car_id]}, 
                                     {$_REQUEST[detail_start_hour][$i]}, 
                                     {$_REQUEST[detail_end_hour][$i]}, 
                                     {$_REQUEST[detail_hour_km][$i]}*100, 
                                     '{$_REQUEST[detail_contain_km][$i]}', 
                                     {$_REQUEST[type]}
                                    ) ,";
            	}
				
            }
            
        }
       // print_r($_REQUEST[contain_km]);exit;
        $sql =  " DELETE 
                FROM ".$ecs->table('car_price_detail').
                " WHERE car_id = '$_REQUEST[car_id]' ".
                "  AND  type = $_REQUEST[type] ;";

        $db->query($sql);
        
		
      	if ( $strTmp != ""){
	    $sql =  " INSERT INTO ".$ecs->table('car_price_detail').
                " ( car_id, time_from, time_end, price, contain_km, type) ".
                " VALUES $strTmp "; 
	
        $db->query($sql); 
      	}





        /*update top info!*/
        $strTmp = "";
        for ($i=1; $i < count($_REQUEST[top_date_from]); $i++) 
        { 
            $_REQUEST[period_top][$i] = $_REQUEST[period_top][$i]*100;
            if($i == count($_REQUEST[top_date_from]) -1)
            {	
            	
            	if(!empty($_REQUEST[contain_km][1]))
            	{
                $strTmp = $strTmp."  ({$_REQUEST[car_id]},
                                     '{$_REQUEST[top_date_from][$i]}', 
                                     '{$_REQUEST[top_date_end][$i]}', 
                                     {$_REQUEST[top_time_from][$i]}, 
                                     {$_REQUEST[top_time_end][$i]},
                                     {$_REQUEST[period_top][$i]},
                                     {$_REQUEST[contain_km][$i]},
                                     {$_REQUEST[type]}
                                     ) ;";
            	}else {
            		                $strTmp = $strTmp."  ({$_REQUEST[car_id]},
                                     '{$_REQUEST[top_date_from][$i]}', 
                                     '{$_REQUEST[top_date_end][$i]}', 
                                     {$_REQUEST[top_time_from][$i]}, 
                                     {$_REQUEST[top_time_end][$i]},
                                     {$_REQUEST[period_top][$i]},
                                     {$_REQUEST[type]}
                                     ) ;";
            	}
								 
            }
            else
            {	
            	if(!empty($_REQUEST[contain_km][1]))
            	{
                $strTmp = $strTmp."  ({$_REQUEST[car_id]}, 
                                     '{$_REQUEST[top_date_from][$i]}', 
                                     '{$_REQUEST[top_date_end][$i]}', 
                                     {$_REQUEST[top_time_from][$i]}, 
                                     {$_REQUEST[top_time_end][$i]},
                                     {$_REQUEST[period_top][$i]}, 
                                     {$_REQUEST[contain_km][$i]},
                                     {$_REQUEST[type]}
                                    ) ,";
            	}else {
                $strTmp = $strTmp."  ({$_REQUEST[car_id]}, 
                                     '{$_REQUEST[top_date_from][$i]}', 
                                     '{$_REQUEST[top_date_end][$i]}', 
                                     {$_REQUEST[top_time_from][$i]}, 
                                     {$_REQUEST[top_time_end][$i]},
                                     {$_REQUEST[period_top][$i]}, 
                                     {$_REQUEST[type]}
                                    ) ,";            		
            	}
				
            }
            
        }

        $sql =  " DELETE 
                FROM ".$ecs->table('car_price_top').
                " WHERE car_id = '$_REQUEST[car_id]' ".
                "  AND  type = $_REQUEST[type] ;";
        $db->query($sql);
       
        if ( $strTmp != ""){
        	if (!empty($_REQUEST[contain_km][1])) {
             	 $sql =  " INSERT INTO ".$ecs->table('car_price_top').
               			 " ( car_id, date_from, date_end, time_from, time_end, period_top, contain_km, type) ".
                		 " VALUES $strTmp ";  	
        	}else {
		        $sql =  " INSERT INTO ".$ecs->table('car_price_top').
		                " ( car_id, date_from, date_end, time_from, time_end, period_top, type) ".
		                " VALUES $strTmp ";  	   		
        	}
   
        $db->query($sql);
        }
        
        admin_log($_POST['car_id'],'update','car_price_tmpl');

        /* 清除缓存 */
        clear_cache_files();

        $link[0]['text'] = $_LANG['back_list'] ;
        $link[0]['href'] = 'car_price_tmpl.php?act=list';

        sys_msg('修改成功', 0, $link);

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

    make_json_result($smarty->fetch('car_price_tmpl_list.htm'), '',
        array('filter' => $brand_list['filter'], 'page_count' => $brand_list['page_count']));
}

elseif ($_REQUEST['act']=='price_list')
{

    $id     = intval($_GET['id']);        
    $basePrice= basePrice($id);

    $detailPrice=detailPrice($id);

    $topPrice=topPrice($id);

    $smarty->assign('base_price',$basePrice);
    $smarty->assign('detail_price',$detailPrice);
    $smarty->assign('top_price',$topPrice);
    $smarty->assign('full_page',1);
    $smarty->display('car_price_detail_list.html');
}

function basePrice($carId)
{
    global $db;
    global $ecs;

    $sql =  " SELECT cpb.car_id,c.name,c.number,cpb.hour_price,cpb.price_km,cpb.type FROM ".$ecs->table('car_price_base').' cpb '.
            " LEFT JOIN ".$ecs->table('car').' c ON c.id=cpb.car_id '.
            " WHERE car_id=".$carId.
            " ORDER BY cpb.type ASC ";
    $ret=$db->getAll($sql);
    $ret=formatPrice($ret,array('hour_price','top_price','price_km','price_time_out'),1);

    return $ret;


}

function detailPrice($carId)
{
    global $db;
    global $ecs;

    $sql =  " SELECT cpd.car_id,cpd.time_from,cpd.time_end,cpd.price,cpd.contain_km,cpd.type,c.number,c.name FROM ".$ecs->table('car_price_detail').' cpd '.
            " LEFT JOIN ".$ecs->table('car').' c ON c.id=cpd.car_id '.
            " WHERE cpd.car_id=".$carId.
            " ORDER BY cpd.type,cpd.time_from ASC ";
    $ret=$db->getAll($sql);
    $ret=formatPrice($ret,array('price'),1);

    return $ret;
}

function topPrice($carId)
{
    global $db;
    global $ecs;

    $sql =  " SELECT cpt.*,c.name,c.number FROM ".$ecs->table('car_price_top').' cpt '.
            " LEFT JOIN ".$ecs->table('car').' c ON c.id=cpt.car_id '.
            " WHERE car_id=".$carId.
            " ORDER BY cpt.type,cpt.time_from ASC ";
    $ret=$db->getAll($sql);
    $ret=formatPrice($ret,array('period_top'),1);

    return $ret;
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
            //modify by yangbei 2013-06-28 增加下线车辆判断
            // $sql = " SELECT c.id,c.name,c.number,p.hour_price ,p.price_km
            //         FROM ".$GLOBALS['ecs']->table('car'). " c  ".
            //         " LEFT JOIN ".
            //             " (SELECT car_id, hour_price, price_km ".
            //             " FROM ".$GLOBALS['ecs']->table('car_price_base').
            //             " WHERE type = 0) p ".
            //         " ON  p.car_id = c.id ".
            //         " WHERE c.name like '%$keyword%' ".
            //         " ORDER BY c.id ASC ";
            $sql = " SELECT c.id,c.name,c.number,p.hour_price ,p.price_km
                    FROM ".$GLOBALS['ecs']->table('car'). " c  ".
                    " LEFT JOIN ".
                        " (SELECT car_id, hour_price, price_km ".
                        " FROM ".$GLOBALS['ecs']->table('car_price_base').
                        " WHERE type = 0) p ".
                    " ON  p.car_id = c.id ".
                    " WHERE c.name like '%$keyword%' "." WHERE delete_stat=0 ".
                    " ORDER BY c.id ASC ";
            //modify by yangbei 2013-06-28 增加下线车辆判断        
            //$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('oil_card')." WHERE card_id like '%{$keyword}%' or password like '%{$keyword}%' or provider like '%{$keyword}%' ORDER BY sort_order ASC";
        }
        else
        {//车辆 ID 车辆昵称 车牌号 时租价格 (/小时) 里程价格 (/公里)
            //modify by yangbei 2013-06-28 增加下线车辆判断
            // $sql = " SELECT c.id,c.name,c.number,p.hour_price ,p.price_km
            //         FROM ".$GLOBALS['ecs']->table('car'). " c  ".
            //         " LEFT JOIN ".
            //             " (SELECT car_id, hour_price, price_km ".
            //             " FROM ".$GLOBALS['ecs']->table('car_price_base').
            //             " WHERE type = 0) p ".
            //         " ON  p.car_id = c.id ".
            //         " ORDER BY c.id ASC ";
            $sql = " SELECT c.id,c.name,c.number,p.hour_price ,p.price_km
                    FROM ".$GLOBALS['ecs']->table('car'). " c  ".
                    " LEFT JOIN ".
                        " (SELECT car_id, hour_price, price_km ".
                        " FROM ".$GLOBALS['ecs']->table('car_price_base').
                        " WHERE type = 0) p ".
                    " ON  p.car_id = c.id "." WHERE delete_stat=0 ".
                    " ORDER BY c.id ASC ";
            //modify by yangbei 2013-06-28 增加下线车辆判断
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
        $rows['hour_price'] = $rows['hour_price']/100;
        $rows['price_km'] = $rows['price_km']/100;
        
        $arr[] = $rows;
    }

    return array('brand' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}


function formatPrice($arr,$keys,$isArrays=0)
{
    $ret = array();
    if(empty($arr))
    {
        return array();
    }
    if ($isArrays)
    {   

        foreach($arr as $k => $v)
        {

            $ret[]=cellFomatPrice($v,$keys);
        }
    }
    else
    {

        $ret = cellFomatPrice($arr,$keys);
    }

    return $ret;

}

function cellFomatPrice($arr,$keys)
{

    foreach ($arr as $k => $v) 
    {
        if(in_array($k, $keys))
        {
            $arr[$k] = $v/100;
        }
    }
    return $arr;
}

?>