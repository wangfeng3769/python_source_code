<?php
require_once './Config.php';
require_once './Tencent.php';
session_start();
define('IN_ECS', true);
include_once (dirname(__FILE__) . '/../../cp/includes/init.php');
$reqAgent=$_SESSION['req_agent'];
require_once (ROOT_PATH . '/../openPlatForm/includes/OpenPlatformLogin.php');
OAuth::init($client_id, $client_secret);
Tencent::$debug = $debug;

//未授权
if ($_GET['code']) {//已获得code
	$code = $_GET['code'];
	$openid = $_GET['openid'];
	$openkey = $_GET['openkey'];
	//获取授权token
	$url = OAuth::getAccessToken($code, $callback);
	$r = Http::request($url);
	parse_str($r, $out);
	//存储授权数据
	if ($out['access_token']) {
		$_SESSION['t_access_token'] = $out['access_token'];
		$_SESSION['t_refresh_token'] = $out['refresh_token'];
		$_SESSION['t_expire_in'] = $out['expire_in'];
		$_SESSION['t_code'] = $code;
		$_SESSION['t_openid'] = $openid;
		$_SESSION['t_openkey'] = $openkey;
		//验证授权
		$r = OAuth::checkOAuthValid();
		if ($r) {
			//授权通过
			$token = array('open_id' => $openid, 'access_token' => $out["access_token"], 'expires_in' => $out['expires_in']);
			$l = new OpenPlatformLogin();
			$token['platform'] = 'qq';
			$l -> qq_put_in_db($token);
			$l->loginFeedback($reqAgent);	
		} else {

			exit('<h3>授权失败,请重试</h3>');

		}
	} else {
		exit($r);
	}
} else {//获取授权code
	if ($_GET['openid'] && $_GET['openkey']) {//应用频道
		$_SESSION['t_openid'] = $_GET['openid'];
		$_SESSION['t_openkey'] = $_GET['openkey'];
		//验证授权
		$r = OAuth::checkOAuthValid();
		if ($r) {

			$l->loginFeedback($_REQUEST['req_agent']);	

		} else {
			exit('<h3>授权失败,请重试</h3>');
		}
	} else {
		// $url = OAuth::getAuthorizeURL($callback);
		// header('Location: ' . $url);
		die('参数错误!');
	}
}

// 	//初始化ecshop架构
// 	// define('IN_ECS', true);
// 	// require('../../cp/includes/init.php');
// 	//------------
// 	define('IN_ECS', true);
// 	require_once(dirname(__FILE__)."/../../cp/includes/init.php");

//     header("Content-type:text/html; charset=UTF-8;");
// 	if(!file_exists('./common/config.php')){
// 		header("Location:./install/index.php");
// 		exit;
// 	}
//     include_once("./common/function.php");

// 	require_once( ROOT_PATH.'/../openPlatForm/includes/OpenPlatformLogin.php' );
// 	require_once (ROOT_PATH . '/../client/classes/UserManager.php');

// $sUrl = "https://graph.qq.com/oauth2.0/token";
// $aGetParam = array(
// 	"grant_type"    =>    "authorization_code",
// 	"client_id"        =>    $aConfig["appid"],
// 	"client_secret"    =>    $aConfig["appkey"],
// 	"code"            =>    $_GET["code"],
// 	"state"            =>    $_GET["state"],
// 	"redirect_uri"    =>    $_SESSION["URI"]
// );
// unset($_SESSION["state"]);
// unset($_SESSION["URI"]);

// $sContent = get($sUrl,$aGetParam);
// if($sContent!==FALSE){
// 	$aTemp = explode("&", $sContent);
// 	$aParam = array();
// 	foreach($aTemp as $val){
// 		$aTemp2 = explode("=", $val);
// 		$aParam[$aTemp2[0]] = $aTemp2[1];
// 	}
// 	$_SESSION["access_token"] = $aParam["access_token"];
// 	$sUrl = "https://graph.qq.com/oauth2.0/me";
// 	$aGetParam = array(
// 		"access_token"    => $aParam["access_token"]
// 	);
// 	$sContent = get($sUrl, $aGetParam);
// 	if($sContent!==FALSE){
// 		$aTemp = array();
// 		preg_match('/callback\(\s+(.*?)\s+\)/i', $sContent,$aTemp);
// 		$aResult = json_decode($aTemp[1],true);
// 		$_SESSION["openid"] = $aResult["openid"];
// 		// if(intval($aConfig["debug"])==1){
// 		// 	echo "<tr><td class='narrow-label'></td><td><input type='button' onclick='location.href=\"../index.php\";' class='button' value='点此返回首页' /></td></tr>";
// 		// }else{
// 		// 	echo "<script>location.href='../index.php';</script>";
// 		// }
// 		/*未注册eduoauto:根据qq开放平台获取的openID是否存在数据库中,判断用户是否绑定qq授权登陆,*/
// 		if(empty($_SESSION['access_token'])||empty($_SESSION['openid']))
// 		{
// 		 	$_SESSION['is_login_qq'] = 0;
// 		}
// 		else
// 		{
// 			/*登陆成功,入库*/

// 			$token = array('open_id'=>$aResult["openid"],'access_token'=>$aParam["access_token"] , 'expires_in'=>$aParam['expires_in']);
// 			$l = new OpenPlatformLogin();
// 			$token['platform'] = 'qq';
// 			$l->qq_put_in_db($token);

// 			if( 'web' == $_GET['req_agent'] ){
// 				header("Location: /index.html");
// 			}else{
// 				echo '<script type="text/javascript">lapp.onOpenPlatLoginRet( 1 , "' . $aParam['access_token'] . '" , "qq");</script>';
// 			}
// 		}
// 		//header("location:"."./index.php");

// 	}

// }
?>