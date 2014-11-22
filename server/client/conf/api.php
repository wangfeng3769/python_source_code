<?php
$initClass = array("classFile" => ROOT_PATH . "../client/classes/UserManager.php", "class" => "UserManager", "method" => "login");

// @formatter:off
$classNameArr = array(
	"CarManager"=>array(
		"classFile"=> ROOT_PATH . "../client/classes/CarManager.php" ,
		"methodArr" =>array( "getNearby" , "getPreferenceCarList" , "follow" )
	),

	"UserManager"=>array(
		"classFile" => ROOT_PATH . "../client/classes/UserManager.php",
		"methodArr" => array( "updateUserInvoiceInfo" , "getInvoiceInfo" , "login" , "captchaRegister" , 
			"modifyPassword" , "saveVerificationImgs" ,	"getMedal" , "untie" , 'getMybonus' , 'showOrder' ,
			'bindLocal' , 'getUserInfo' , "getViolateInfo" , 'getOpenPlatInfo' , 'captchaFindpassword' , 
			'GraphVerification' , 'findcreditcard' , 'delete_creditcard' , 'edouserdetail','removeFollowByUserId',
			'getMessageInfo','getCashInfo','getCashAccountInfo' )
	),
		
	"ModifyPhone"=>array(
		"classFile" => ROOT_PATH . "../client/classes/ModifyPhone.php",
		"methodArr" => array( "getCaptcha" , "check" )
	),
	
	
	"ClientManager"=>array(
		"classFile" => ROOT_PATH . "../client/classes/ClientManager.php",
		"methodArr" => array( "getLastVersion" )
	),

	"MsgProcessor"=>array(
		"classFile" => ROOT_PATH . "../client/classes/MsgProcessor.php",
		"methodArr" => array( "process" , "test" )
	),

	"CarAgent"=>array(
		"classFile" => ROOT_PATH . "../client/classes/CarAgent.php",
		"methodArr" => array( "ring" , "creditCardOpenDoor" , "creditCardCloseDoor" )
	),

	"SmsCaptcha"=>array(
		"classFile" => ROOT_PATH . "../client/classes/SmsCaptcha.php",
		"methodArr" => array( "get" )
	),

	"SmsReceiver"=>array(
		"classFile" => ROOT_PATH . "../client/classes/SmsReceiver.php",
		"methodArr" => array( "receive" )
	),

	"OrderManager"=>array(
		"classFile" => ROOT_PATH . "../client/classes/OrderManager.php",
		"methodArr" => array( "payFor" , "getOrderTimeInDay" , "create" , "balance" , "getMyOrderList" , 
			"getInvoiceInfo" , "askForInvoice" , "getShowOrderContent" , "getOrderPaymentInfo" , "getCancelOrderFee" , "cancel" )
	),
	
	"Payment"=>array(
		"classFile" => ROOT_PATH . "../client/classes/Payment.php",
		"methodArr" => array( "creditCardPay",)
	),
	
	"MedalContainer"=>array(
		"classFile" => ROOT_PATH . "../medal_container/MedalContainer.php",
		"methodArr" => array( "addMedal","delMedal","getMedal","getMyMedalArr","generateUserMedal",
		"gainMedal","useMedal","setMedalStatus","getMedalAndBonus","pickMedal")
	)
);
?>