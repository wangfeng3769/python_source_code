<?php
session_start();
require_once './class/config.inc.php';
define('IN_ECS', true);
include_once (dirname(__FILE__) . '/../../cp/includes/init.php');
$reqAgent = $_GET['req_agent'];
$_SESSION['req_agent']=$reqAgent;
$sUrl="https://graph.renren.com/oauth/authorize?client_id=".$config->APPID."&response_type=code&scope=".
	$config->scope."&state=a%3d1%26b%3d2&redirect_uri=".$config->redirecturi."&x_renew=true&display=mobile";
// echo $sUrl;
if( 'mobile' == $reqAgent ){
	$sUrl .= "&display=mobile";
}	
header("Location: ".$sUrl);