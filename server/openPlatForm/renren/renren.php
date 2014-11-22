<?php session_start();?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once "HttpClient.class.php";
require_once './class/config.inc.php';

$APIKey = $config->APIKey;
$SecretKey = $config->SecretKey;
$redirecturi = 'http://www.eduoauto.com/renren/renren.php';
$scope ='publish_feed,photo_upload';
 
// 生成API签名sig，sig人人API的一个参数
function gensig($params,$secret_key){
    ksort($params);
    reset($params);
    $str = "";
    foreach($params as $key=>$value){
        $str .= "$key=$value";
    }
    return md5($str.$secret_key);;
}
 
// 页面状态设置，用于页面路由
// 默认是‘1’
// 获得request token (code) 后是 ‘2’
// 获得access token 后是 ‘magic’ 的值
 
$state = '1';
if(isset($_REQUEST['code'])){
    if(empty($_SESSION['atoken']))
        $state = '2';
}
if(isset($_REQUEST['magic'])){
    $state = $_REQUEST['magic'];
}
 
// 根据state生成响应的页面
switch($state){
    case '1': // 给出进入“登录验证和应用许可”页面的连接
         
        $_SESSION['atoken'] = '';
        $url = "http://graph.renren.com/oauth/authorize?client_id=$APIKey".
            "&response_type=code&scope=$scope&redirect_uri=$redirecturi";
        echo "<a href=\"$url\">使用人人帐号登录</a><br/>";
        break;
    case '2': // 获取 access token，给出api调用的连接
         
        // 获取 request token，即code
        $code = $_REQUEST['code'];
         
        // 发起获取 access token请求
        $url = "http://graph.renren.com/oauth/token?client_id=$APIKey&code=$code".
            "&grant_type=authorization_code&client_secret=$SecretKey&redirect_uri=$redirecturi";
        $json = HttpClient::quickGet($url);
         
        // 解析返回 json
        $jsond = json_decode($json);
        $access_token = $jsond->access_token;
         
        // 生成页面
        if(!empty($access_token)){
            $_SESSION['atoken'] = $access_token;
            $url = $redirecturi."?magic=3";
            echo "access token: $access_token<br/>";
            echo "<a href=\"$url\">调用API: users.getInfo</a>";
        }else{
            echo "Wrong!<br/>";
        }
        break;
    case '3': // 调用 api user.getInfo，显示用户的姓名、uid和头像
         
        // 发起API调用请求
        $access_token = $_SESSION['atoken'];
        $params = array("method"=>"users.getInfo","v"=>"1.0",
            "access_token"=>$access_token,"format"=>"json");
        $params['sig'] = gensig($params,$SecretKey);
        $url = "http://api.renren.com/restserver.do";
        $json = HttpClient::quickPost($url,$params);
         
        // 解析返回json
        $jsond = json_decode($json);
        $uid = $jsond['0']->uid;
        $tinyurl = $jsond['0']->tinyurl;
        $name = $jsond['0']->name;
 
        // 生成页面
        echo "你好$name, 你的UID是$uid<br/>";
        echo "<img src=\"$tinyurl\">";
        break;
    default:
        break;
}
?>