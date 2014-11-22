<?php
/**
 * 此为PHP-SDK 2.0 的一个使用Demo,用于流程和接口调用演示
 * 请根据自身需求和环境进相应的安全和兼容处理，勿直接用于生产环境
 */
error_reporting(0);
session_start();
require_once './Config.php';
require_once './Tencent.php';
$reqAgent = $_GET['req_agent'];
define('IN_ECS', true);
include_once(dirname(__FILE__) . '/../../cp/includes/init.php');
include_once "common/function.php";
require_once( ROOT_PATH.'/../openPlatForm/includes/OpenPlatformLogin.php' );
OAuth::init($client_id, $client_secret);
Tencent::$debug = $debug;
$reqAgent = $_GET['req_agent'];
$_SESSION['req_agent']=$reqAgent;

//未授权,跳转到api授权页
$url = OAuth::getAuthorizeURL($callback);
header('Location: ' . $url);

