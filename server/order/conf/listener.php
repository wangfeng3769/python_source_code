<?php

require_once (dirname(__FILE__) . '/../../hfrm/Frm.php');

$listener = array(
	array(
		'classPath' => Frm::$ROOT_PATH . 'order_charge/classes/OrderCharge.php',
		'className' => 'OrderCharge'
	)
);
?>