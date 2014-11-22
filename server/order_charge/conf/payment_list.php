<?php

require_once (dirname(__FILE__) . '/../../hfrm/Frm.php');

require_once( Frm::$ROOT_PATH . 'order_charge/classes/OrderCharge.php' );

$paymentWay = array(
	// OrderCharge::$PAYMENT_TYPE_UPMOBILE => array(
	// 	'classPath' => Frm::$ROOT_PATH . 'PayAPI/upmobile/classes/UnionPayMobileManager.php',
	// 	'className' => 'UnionPayMobileManager'
	// ),
	OrderCharge::$PAYMENT_TYPE_UPMOBILE => array(
		'classPath' => Frm::$ROOT_PATH . 'PayAPI/upmp/classes/UnionMobilePayManager.php',
		'className' => 'UnionMobilePayManager'
	),

	OrderCharge::$PAYMENT_TYPE_CASH => array(
		'classPath' => Frm::$ROOT_PATH . 'PayAPI/cash_account/classes/CashAccountAgent.php',
		'className' => 'CashAccountAgent'
	),
	OrderCharge::$PAYMENT_TYPE_UPWEB => array(
		'classPath' => Frm::$ROOT_PATH . 'PayAPI/UPPop/classes/UnionWebPayManager.php',
		'className' => 'UnionWebPayManager'
	),
);
?>