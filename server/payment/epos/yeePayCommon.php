<?php
set_time_limit(0);
define('IN_ECS', true);
if (!defined('ROOT_PATH')) {
	require_once (dirname(__FILE__) . '/../../cp/includes/init.php');
}

/*
* @Title 易宝支付EPOS范例
* @Description 通用文件，包含签名机制，数据发送和日志写入
* @V3.0
* @Author  wenhua.cheng
*/
//---------------------------------------------------------------------------------------------
require_once ROOT_PATH . '/../payment/epos/merchantProperties.php';
require_once ROOT_PATH . '/../payment/epos/HttpClient.class.php';


// 支付请求函数
function getReqHmacString(Array $bizArray){
	$merchantKey = EPosConfig::$merchantKey;
	$str="";
	foreach($bizArray as $key=>$value){
		$str=$str.$value;
	}
	return HmacMd5($str,$merchantKey);
}


function eposSale(Array $bizArray) {

	$actionURL = EPosConfig::$actionURL;
	$merchantKey = EPosConfig::$merchantKey;

	// 调用签名函数生成签名串
	$ReqHmacString=getReqHmacString($bizArray);

	// 组成请求串
	$actionHttpString=HttpClient::buildQueryString($bizArray)."&pr_NeedResponse=1"."&hmac=".$ReqHmacString;
	echo $actionURL."?".$actionHttpString;

	// 记录发起支付请求的参数
	logurl("发起请求",$actionURL."?".$actionHttpString);
	// 发起支付请求
	$pageContents = HttpClient::quickPost($actionURL,$actionHttpString);

	echo $pageContents;

	// 记录收到的提交结果
	logurl("请求回写",$pageContents);
	$result=explode("\n",$pageContents);
	for($index=0;$index<count($result);$index++){
		$result[$index] = trim($result[$index]);
		if (strlen($result[$index]) == 0) {
			continue;
		}
		$aryReturn= explode("=",$result[$index]);
		$sKey= $aryReturn[0];
		$sValue	= $aryReturn[1];
		if($sKey=="r0_Cmd"){
			$r0_Cmd	= $sValue;
		}elseif($sKey=="r1_Code"){
			$r1_Code= $sValue;
		}elseif($sKey=="r2_TrxId"){
			$r2_TrxId=$sValue;
		}elseif($sKey=="r6_Order"){
			$r6_Order=$sValue;
		}elseif($sKey =="errorMsg"){
			$errorMsg=$sValue;
		}elseif($sKey == "hmac"){
			$hmac = $sValue;

		} else{
			return $result[$index];
		}
	}


	// 进行校验码检查 取得加密前的字符串
	$sbOld="";
	// 加入业务类型
	$sbOld = $sbOld.$r0_Cmd;
	// 加入支付结果
	$sbOld = $sbOld.$r1_Code;
	// 加入易宝支付交易流水号
	$sbOld = $sbOld.$r2_TrxId;
	// 加入商户订单号
	$sbOld = $sbOld.$r6_Order;

	$sNewString = HmacMd5($sbOld,$merchantKey);

	logurl("订单号:".$r6_Order,"本地生成HMAC:".$sNewString."返回HMAC:".$hmac);

	// 校验码正确
	if($sNewString==$hmac) {
		if($r1_Code=="1"){
			logurl("请求成功","本地生成HMAC:".$sNewString."返回HMAC:".$hmac);
			echo "<br>提交成功!";
			echo "<br>商户订单号:".$r6_Order."<br>";
			return;
		} elseif($r1_Code=="66"){
			echo "<br>提交失败";
			echo "<br>订单金额过小!";
			return;
		} elseif($r1_Code=="30001"){
			echo "<br>提交失败";
			echo "<br>支付卡密无效!";
			return;
		} elseif($r1_Code=="3002"){
			echo "<br>提交失败";
			echo "<br>创建订单异常!";
			return;
		} elseif($r1_Code=="3003"){
			echo "<br>提交失败";
			echo "<br>创建交易异常!";
			return;


		} elseif($r1_Code=="3006"){
			echo "<br>提交失败";
			echo "<br>银行返回失败信息，参考返回参数errorMsg!";
			return;

		} elseif($r1_Code=="3008"){
			echo "<br>提交失败";
			echo "<br>卡号规则不符合!";
			return;
		} elseif($r1_Code=="-100"){
			echo "<br>未知错误!";
			return;

		} else{
			echo "<br>提交失败";
			echo "<br>请检查后重新测试支付";
			return;
		}
	} else{
		return false;
	}

	return true;
}


// 校验支付结果

function verifyCallback(Array $bizArray,$callBackHmac){
	$merchantKey = EPosConfig::$merchantKey;
	$callBackString="";
	$callBackStringLog="";
	foreach($bizArray as $key=>$value){
		$callBackString.=$value;
		$callBackStringLog.=$key."=".$value."&";

	}
	$newLocalHmac=HmacMd5( $callBackString,$merchantKey);
	if ($newLocalHmac==$callBackHmac){
		logurl("callBack页面回调成功，交易信息正常!","回调参数串:".$callBackStringLog."LocalHmac(".$$newLocalHmac.") == ResponseHmac(".$callBackHmac.")!");
		return true;

	}
	else{
		echo "交易信息被篡改！</br>newLocalHmac=".$newLocalHmac."</br>callBackHmac=".$callBackHmac;
		logurl("callBack页面回调成功，但交易信息被篡改!","回调参数串:".$callBackStringLog."LocalHmac(".$newLocalHmac.") != ResponseHmac(".$callBackHmac.")!");
		return false;
	}

}


// 生成hmac的函数
function HmacMd5($data,$key)
{
	//logurl("iconv Q logdata",$data);
	$logdata=$data;
	$logkey=$key;

	// RFC 2104 HMAC implementation for php.
	// Creates an md5 HMAC.
	// Eliminates the need to install mhash to compute a HMAC
	// Hacked by Lance Rushing(NOTE: Hacked means written)

	// 需要配置环境支持iconv，否则中文参数不能正常处理
	$ke=iconv("GB2312","UTF-8",$key);
	$data=iconv("GB2312","UTF-8",$data);
	$b=64; // byte length for md5
	if (strlen($key) > $b) {
		$key = pack("H*",md5($key));
	}
	$key=str_pad($key, $b, chr(0x00));
	$ipad=str_pad('', $b, chr(0x36));
	$opad=str_pad('', $b, chr(0x5c));
	$k_ipad=$key ^ $ipad ;
	$k_opad=$key ^ $opad;

	$log_hmac=md5($k_opad . pack("H*",md5($k_ipad . $data)));
	loghmac($logdata,$logkey,$log_hmac);
	return md5($k_opad . pack("H*",md5($k_ipad . $data)));
}

// 记录请求URL到日志
function logurl($title,$content)
{
	$logName = EPosConfig::$logName;
	$james=fopen($logName,"a+");
	date_default_timezone_set(PRC);
	fwrite($james,"\r\n".date("Y-m-d H:i:s,A")." [".$title."]   ".$content."\n");
	fclose($james);
}

// 记录生成hmac时的日志信息
function loghmac($str,$merchantKey,$hmac)
{
	$logName = EPosConfig::$logName;
	$merchantKey = EPosConfig::$merchantKey;

	$james=fopen($logName,"a+");
	date_default_timezone_set(PRC);
	fwrite($james,"\r\n".date("Y-m-d H:i:s,A")."  [构成签名的参数:]".$str."  [商户密钥:]".$merchantKey."   [本地HMAC:]".$hmac);
	fclose($james);
}
?>