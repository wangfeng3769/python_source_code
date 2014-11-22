<?php
//session_start();
define('IN_ECS', true);
include_once (dirname(__FILE__) . '/../../cp/includes/init.php');
include_once (ROOT_PATH . '/../openPlatForm/weibo/includes/config.php');
include_once (ROOT_PATH . '/../openPlatForm/weibo/includes/saetv2.ex.class.php');
include_once (ROOT_PATH . '/../client/classes/UserManager.php');

$reqAgent = $_GET['req_agent'];
$callBackUrl = WB_CALLBACK_URL . "?req_agent=$reqAgent";

$accessToken = $argv['access_token'];
$o = new SaeTOAuthV2(WB_AKEY, WB_SKEY, $accessToken);
$code_url = $o -> getAuthorizeURL($callBackUrl);

if( 'mobile' == $reqAgent ){
	$code_url .= "&display=mobile";
}

if ('1' == $_GET['is_ajax']) {
	echo "\3$code_url";
} else {
	header("Location: $code_url");
}
exit(0);
?>

