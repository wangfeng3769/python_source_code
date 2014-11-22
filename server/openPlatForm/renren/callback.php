<?php
//初始化ecshop架构
error_reporting(1);
define('IN_ECS', true);
session_start();
require_once (dirname(__FILE__) . "/../../cp/includes/init.php");
require_once (ROOT_PATH . '/../openPlatForm/includes/OpenPlatformLogin.php');
require_once (ROOT_PATH . '/../openPlatForm/renren/class/config.inc.php');
require_once (ROOT_PATH . '/../openPlatForm/renren/class/RenrenRestApiService.class.php');
require_once (ROOT_PATH . '/../openPlatForm/renren/class/RenrenOAuthApiService.class.php');
require_once (ROOT_PATH . '/../client/classes/UserManager.php');
$code = $_GET["code"];
if ($code) {

	//获取accesstoken
	$oauthApi = new RenrenOAuthApiService;
	$post_params = array('client_id' => $config -> APIKey, 'client_secret' => $config -> SecretKey, 'redirect_uri' => $config -> redirecturi, 'grant_type' => 'authorization_code', 'code' => $code);
	$token_url = 'http://graph.renren.com/oauth/token';
	$access_info = $oauthApi -> rr_post_curl($token_url, $post_params);
	//使用code换取token
	//$access_info=$oauthApi->rr_post_fopen($token_url,$post_params);//如果你的环境无法支持curl函数，可以用基于fopen函数的该函数发送请求
	$access_token = $access_info["access_token"];
	$expires_in = $access_info["expires_in"];
	$refresh_token = $access_info["refresh_token"];
	//session_start();
	$_SESSION["access_token"] = $access_token;

	//获取用户信息RenrenRestApiService
	$restApi = new RenrenRestApiService;
	$params = array('fields' => 'uid,name,sex,birthday,mainurl,hometown_location,university_history,tinyurl,headurl', 'access_token' => $access_token);
	$res = $restApi -> rr_post_curl('users.getInfo', $params);
	//curl函数发送请求
	//$res = $restApi->rr_post_fopen('users.getInfo', $params);//如果你的环境无法支持curl函数，可以用基于fopen函数的该函数发送请求
	//print_r($res);//输出返回的结果

	$userId = $res[0]["uid"];
	// $username = $res[0]["name"];
	// $birthday = $res[0]["birthday"];
	// $tinyurl = $res[0]["tinyurl"];
	// $sex = $res[0]["sex"];
	// $headurl = $res[0]["headurl"];
	// $mainurl = $res[0]["mainurl"];
	//print_r($res);
	if ($userId > 0)//如果userId已经存在
	{
		//更新数据库的access_token、expires_in、refresh_token、获取token的时间 open_id
		//直接登录到网站
		$token = array('access_token' => $access_token, 'expires_in' => $expires_in, 'open_id' => $userId);
		$l = new OpenPlatformLogin();
		$token['platform'] = 'renren';
		$l -> rr_put_in_db($token);
		$l->loginFeedback($_SESSION['req_agent']);	


		// $l -> renrenShare($access_token);

	} else {
		//注册网站或者绑定已有账号
		//进行同步设置？
		//登录到网站
		// echo $userID . 'faile to get userId';
		// echo "<br/>";
		echo $res['error_msg'];
		// sleep(3);
	}

	//刷新代码：
	//用refreshtoken刷新accesstoken（在同步新鲜事的时候取出对应网站用户的人人accesstoken，如果用当前时间的秒数-expires_in>获取token的时间的秒数,则accesstoken过期，用refreshtoken刷新accesstoken）

	// $post_params = array('client_id'=>$config->APIKey,
	// 		'client_secret'=>$config->SecretKey,
	// 		'refresh_token'=>$refresh_token,
	// 		'grant_type'=>'refresh_token'
	// 		);
	// $access_info=$oauthApi->rr_post_curl($token_url,$post_params);//使用code换取token
	// $access_token=$access_info["access_token"];
	// $expires_in=$access_info["expires_in"];
	// $refresh_token=$access_info["refresh_token"];
	// echo '<script type="text/javascript">lapp.onOpenPlatLoginRet(' . 1 . ' , "' . UserManager::getSessionToken() . '" , "renren")</script>';

} else {
	//如果获取不到code，将错误信息打出来
	echo 'errorparameter code is null:<br>' . $_SERVER["QUERY_STRING"];
}
//下面是同步的代码，因为用到access_token，所以写在获取access_token的页面中，真正同步时应该从数据库中取的access_token（判断access_token是否过期，过期就用refreshtoken刷新，刷新代码在上面，并将刷新的access_token、expires_in、refresh_token替换掉数据库中的）
?>

