<?php
session_start();
set_time_limit(0);

define('DONOT_ADD_SLASH', true);
define('IN_ECS', true);
require_once (dirname(__FILE__) . '/../cp/includes/init.php');

/*************************************************/
/*在这儿配置接口的属性
 /*************************************************/
require_once (ROOT_PATH . "../client/conf/api.php");
require_once (ROOT_PATH . "../client/classes/Output.php");
/*************************************************/
/*接口属性配置结束
 /*************************************************/

$o = new Output();
$o -> executeJson();
