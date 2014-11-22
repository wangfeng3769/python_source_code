<?php

require_once (dirname(__FILE__) . '/../../hfrm/Frm.php');

require_once (Frm::$ROOT_PATH . 'client/classes/JsonOutput.php');

require_once( Frm::$ROOT_PATH . 'client/classes/MemberClient.php');
$className = 'MemberClient';
$methodName = $_REQUEST['item'];

$jsonOutput = new JsonOutput();
$jsonOutput -> output($className, $methodName);
?>