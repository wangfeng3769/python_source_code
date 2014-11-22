<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);

$exc = new exchange($ecs->table("vacation"), $db, 'id', 'holiday');
/*------------------------------------------------------ */
//-- 休息日列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    $str_json=get_holidays();
    $smarty->assign( 'ur_here',      $_LANG['01_calendar_setting']);
    $smarty->assign( 'str_json',    $str_json);
    $smarty->assign( 'full_page',    1);
    assign_query_info();
    $smarty->display('calendar_setting.htm');
}

/*------------------------------------------------------ */
//-- 删除休息日
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'unSlect')
{
    /* 权限判断 */
    admin_priv('brand_manage');
    print_r($_REQUEST);
                // $sql =  " INSERT INTO ".$ecs->table('vacation')." (holiday)".
                //         " VALUES($unSlectDate)"
    $unSlectDate = $_REQUEST['sdate'];
    $sql =  " SELECT COUNT(*) FROM ".$ecs->table('vacation') .
            " WHERE holiday = '$unSlectDate'";
    $count_col = $db->getOne($sql);
    if($count_col != 0)
    {
        $sql =  " DELETE FROM  ".$ecs->table('vacation').
                " WHERE holiday = '$unSlectDate'";
        $db->query($sql);
    }

}
//增加休息日
elseif ($_REQUEST['act'] == 'Slect')
{
    /* 权限判断 */
    admin_priv('brand_manage');
    print_r($_REQUEST);
                 
    $unSlectDate = $_REQUEST['sdate'];
    $sql =  " SELECT COUNT(*) FROM ".$ecs->table('vacation') .
            " WHERE holiday = '$unSlectDate'";
    $count_col = $db->getOne($sql);
    if($count_col == 0)
    {
        $sql =  " INSERT INTO ".$ecs->table('vacation')." (holiday)".
                " VALUES('$unSlectDate')";
        $db->query($sql);
    }

}

function get_holidays()
{
    $sql =  " SELECT holiday".
            " FROM ".$GLOBALS['ecs']->table('vacation');
    $arr = $GLOBALS['db']->getAll($sql);
    //{date:26, month:11},{date:25, month:11}
    $str_json = "";
    for ($i=0; $i < count($arr); $i++) 
    { 
        $res = getdate(strtotime($arr[$i]['holiday']));
        if ($i == count($arr)-1)
        {
            $str_json =$str_json. "{date:".$res[mday].", month:".($res[mon]-1).",year:".$res[year]."}";
        }
        else
        {
            $str_json =$str_json. "{date:".$res[mday].", month:".($res[mon]-1).",year:".$res[year]."},";
        }
        
    }
    return $str_json;
}
?>