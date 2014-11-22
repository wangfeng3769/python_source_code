<?php
define('IN_ECS','1');
error_reporting ( E_ERROR  );
ini_set('display_errors',        1);
session_start();
require_once "cp/includes/init.php";

require_once "cp/includes/cls_json.php";
include_once( 'sdk/config.php' );
include_once( 'sdk/saetv2.ex.class.php' );

if($_GET['act'] == "code")
{
	print_r($_REQUEST);
}
elseif($_GET['act'] == "send")
{
	//$_SESSION['token']['access_token'] = $_GET['access_token'];
	//include_once( 'weibolist.php' ) ;
	/*
	//发送分享微博
	include_once( 'config.php' ) ;
	include_once( 'saetv2.ex.class.php' ) ;
	echo WB_AKEY. ":". WB_SKEY. ":" .$_GET['access_token']."<BR>" ;
	$c = new SaeTClientV2( WB_AKEY , WB_SKEY , $_GET['access_token'] ) ;
	$c->update( "saaadf" ) ;*/
	
	$c = new SaeTClientV2( WB_AKEY , WB_SKEY , $_SESSION['token']['access_token'] );
	$ret = $c->update( "我已加入**网，快来一起加入吧" );	//发送微博
	if ( isset($ret['error_code']) && $ret['error_code'] > 0 ) {
		echo "<p>发送失败，错误：{$ret['error_code']}:{$ret['error']}</p>";
	} else {
		//echo "<p>发送成功</p>";
	}
	die();
}
else
{
	$o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );

	if (isset($_REQUEST['code'])) {
		$keys = array();
		$keys['code'] = $_REQUEST['code'];
		$keys['redirect_uri'] = WB_CALLBACK_URL;
		try {
			$token = $o->getAccessToken( 'code', $keys ) ;
		} catch (OAuthException $e) {
		}
	}

	if ($token) {
		$_SESSION['token'] = $token;
		setcookie( 'weibojs_'.$o->client_id, http_build_query($token) );
		$sql = "insert into ".$sns->table("open_login")." set user_id='".$_SESSION['user_id']."', open_id='".$_SESSION['token']['uid']."', access_token='".$_SESSION['token']['access_token']."',login_from=1, last_login_time='".date("Y-m-d H:i:s")."', timout='".$_SESSION['token']['expires_in']."' ";
		$db->query($sql);

		header("location:attachWeibo.php?act=send");
	}

	/*
	$url = urlencode( "http://www.eduoauto.com/attachWeibo.php?act=getToken" ) ;
	$ch = curl_init();//初始化curl  
	$data = "client_id=1774355824&client_secret=2c96916d1d15c71aac537a5615f3b00b&grant_type=authorization_code&code=".$_REQUEST['code']."&redirect_uri=".$url ;
	curl_setopt($ch,CURLOPT_URL,'https://api.weibo.com/oauth2/access_token');//抓取指定网页  

	curl_setopt($ch, CURLOPT_HEADER, 0);//设置header  

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上  

	curl_setopt($ch, CURLOPT_POST, 1);//post提交方式  

	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  

	$data = curl_exec($ch);//运行curl  

	curl_close($ch); 

	
	$json = new JSON();
	$data = $json->decode($data);

	//$uid = UserManager::getLoginUserId() ;
	$sql = "insert into ".$sns->table("open_login")." set user_id='".$_SESSION['user_id']."', open_id='".$data->uid."', access_token='".$data->access_token."',login_from=1, last_login_time='".date("Y-m-d H:i:s")."', timout='".$data->expires_in."' ";
	$db->query($sql);

	header("location:attachWeibo.php?act=send&access_token=".$data->access_token);
	
	/*
	$ci = curl_init();//初始化curl  
	//$msg = "我成为易多汽车的会员了，有车开啦！" ;
	$msg = "aaaa" ;
	$msg = htmlspecialchars( $msg );
	//$data2 = "source=2465585024&access_token=".$data->access_token."&status=".$msg ;
	$data2 = "status=".$msg ;
	$headers[] = "Authorization: OAuth2 ".$data->access_token;
	$headers[] = "API-RemoteIP: " . $_SERVER['REMOTE_ADDR'];

	curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0) ;
	curl_setopt($ci, CURLOPT_USERAGENT, "Sae T OAuth2 v0.1");
	curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, '30');
	curl_setopt($ci, CURLOPT_TIMEOUT, '30');
	curl_setopt($ci, CURLOPT_URL, "https://api.weibo.com/2/statuses/update.json" ); //抓取指定网页  
	curl_setopt($ci, CURLOPT_HEADERFUNCTION, 'getHeader');
	curl_setopt($ci, CURLOPT_HEADER, FALSE);//设置header  
	curl_setopt($ci, CURLOPT_RETURNTRANSFER, true); //要求结果为字符串且输出到屏幕上  
	curl_setopt($ci, CURLOPT_ENCODING, "");
	curl_setopt($ci, CURLOPT_POST, true); //post提交方式  
	curl_setopt($ci, CURLOPT_POSTFIELDS, $data2 ) ;  
	curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE );
	curl_setopt($ci, CURLOPT_HTTPHEADER, $headers );
	curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false);
	
	$datas = curl_exec($ci); //运行curl  
	echo $datas;
	curl_close($ci);*/
	/**/
	/*
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' ;
	echo '<script src="cp/js/utils.js"></script><script src="cp/js/transport.js"></script>';
	*/
	echo '<script>
	Ajax.call("https://api.weibo.com/2/statuses/update.json", "'.$data2.'", null, "POST");
	//lapp.onOpenPlatLoginRet(1 , "' . $data->access_token . '" , "sinaweibo");
	</script>' ;
}


?>