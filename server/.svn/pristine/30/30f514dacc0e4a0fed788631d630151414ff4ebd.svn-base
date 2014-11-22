<?php

/**
 * ECSHOP 前台公用文件
 * ============================================================================
 * 版权所有 2005-2010 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: init.php 17153 2010-05-05 09:39:12Z liuhui $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

error_reporting(0);

if (__FILE__ == '')
{
    die('Fatal error code: 0');
}

/* 取得当前ecshop所在的根目录 */
define('ROOT_PATH', str_replace('includes/init.php', '', str_replace('\\', '/', __FILE__)));

if (!file_exists(ROOT_PATH . 'data/install.lock') && !file_exists(ROOT_PATH . 'includes/install.lock')
    && !defined('NO_CHECK_INSTALL'))
{
    header("Location: ./install/index.php\n");

    exit;
}

/* 初始化设置 */
@ini_set('memory_limit',          '128M');
@ini_set('session.cache_expire',  180);
@ini_set('session.use_trans_sid', 0);
@ini_set('session.use_cookies',   1);
@ini_set('session.auto_start',    0);
@ini_set('display_errors',        1);

if (DIRECTORY_SEPARATOR == '\\')
{
    @ini_set('include_path', '.;' . ROOT_PATH);
}
else
{
    @ini_set('include_path', '.:' . ROOT_PATH);
}

require(ROOT_PATH . 'data/config.php');

if (defined('DEBUG_MODE') == false)
{
    define('DEBUG_MODE', 0);
}

if (PHP_VERSION >= '5.1' && !empty($timezone))
{
    date_default_timezone_set($timezone);
}

$php_self = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
if ('/' == substr($php_self, -1))
{
    $php_self .= 'index.php';
}
define('PHP_SELF', $php_self);


require(ROOT_PATH . 'includes/cls_ecshop.php');


require(ROOT_PATH . 'includes/lib_main.php');
require_once(ROOT_PATH . '/../hfrm/Frm.php');
require_once(ROOT_PATH . '/../openPlatForm/renren/class/RenrenRestApiService.class.php');
require_once (ROOT_PATH . '/../openPlatForm/renren/class/RenrenOAuthApiService.class.php');
/* 对用户传入的变量进行转义操作。*/

// if( ! defined('DONOT_ADD_SLASH') ){
// 	if (!get_magic_quotes_gpc())
// 	{
// 	    if (!empty($_GET))
// 	    {
// 	        $_GET  = addslashes_deep($_GET);

// 	    }

// 	    if (!empty($_POST))
// 	    {
// 	        $_POST = addslashes_deep($_POST);
// 	    }
	   
// 	    $_COOKIE   = addslashes_deep($_COOKIE);
// 	    $_REQUEST  = addslashes_deep($_REQUEST);
// 	}
// }


/* 创建 ECSHOP 对象 */
$ecs = new ECS($db_name, $prefix);

/* 初始化数据库类 */
require(ROOT_PATH . 'includes/cls_mysql.php');
$db = new cls_mysql($db_host, $db_user, $db_pass, $db_name);
$db->set_disable_cache_tables(array($ecs->table('sessions'), $ecs->table('sessions_data'), $ecs->table('cart')));
$db_host = $db_user = $db_pass = $db_name = NULL;

if (is_spider())
{
    /* 如果是蜘蛛的访问，那么默认为访客方式，并且不记录到日志中  */
    if (!defined('INIT_NO_USERS'))
    {
        define('INIT_NO_USERS', true);
        /* 整合UC后，如果是蜘蛛访问，初始化UC需要的常量 */
        if($_CFG['integrate_code'] == 'ucenter')
        {
             $user = & init_users();
        }
    }
    $_SESSION = array();
    $_SESSION['user_id']     = 0;
    $_SESSION['user_name']   = '';
    $_SESSION['email']       = '';
    $_SESSION['user_rank']   = 0;
    $_SESSION['discount']    = 1.00;
}
class sns
{
    function sns()
    {
    }
    function table($name)
    {
    return "edo_".$name;
    }
}
$sns = new sns();

?>