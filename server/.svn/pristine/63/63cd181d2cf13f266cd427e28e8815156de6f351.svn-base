<?php
//初始化ecshop架构
define('IN_ECS', true);
require_once (dirname(__FILE__) . "/../../cp/includes/init.php");
require_once (ROOT_PATH . '/../openPlatForm/weibo/includes/config.php');
require_once (ROOT_PATH . '/../openPlatForm/weibo/includes/saetv2.ex.class.php');
require_once (ROOT_PATH . '/../openPlatForm/includes/OpenPlatformLogin.php');
require_once (ROOT_PATH . '/../client/classes/UserManager.php');
session_start();

$o = new SaeTOAuthV2(WB_AKEY, WB_SKEY);
if (isset($_REQUEST['code'])) {
	$keys = array();
	$keys['code'] = $_REQUEST['code'];
	$keys['redirect_uri'] = WB_CALLBACK_URL;
	try {
		$token = $o -> getAccessToken('code', $keys);

	} catch (OAuthException $e) {

	}
}

if ($token) {
	$_SESSION['w_token'] = $token;
	setcookie('weibojs_' . $o -> client_id, http_build_query($token));
	if (isset($token['uid'])) {
		$l = new OpenPlatformLogin();
		//$l->test();
		$is_login_sina = 1;
		$l -> sina_put_in_db($token);
		$l->loginFeedback($_REQUEST['req_agent']);		
	}
}
// if ('web' == $_GET['req_agent']) {
// 	header("Location: /index.php");
// } else {
	
// }
