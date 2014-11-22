<?php
//本程序只能从命令行运行
set_time_limit(0);

define('DONOT_ADD_SLASH', true);
define('IN_ECS', true);

function carServerShutdownLog() {
	$err = error_get_last();
	if (!empty($err)) {
		ob_start();
		print_r($err);

		$fp = fopen("/home/hoheart/car_server_shutdown.log", "a+");
		fwrite($fp, date('Y-m-d H:i:s'));
		fwrite($fp, ob_get_clean());
		fclose($fp);
	}
}

register_shutdown_function("carServerShutdownLog");

require_once (dirname(__FILE__) . '/../cp/includes/init.php');

ob_start();

//防止非命令行运行
if (basename(__FILE__) != $argv[0]) {
	return;
}

$rootUrl = $argv[1];
if (empty($rootUrl)) {
	return;
}

$webPort = $argv[2];
if ($webPort <= 0) {
	$webPort = 80;
}

require_once (ROOT_PATH . "../client/classes/UdpServer.php");

$c = new UdpServer();
echo $c -> start($rootUrl, $webPort);

$fp = fopen("/home/hoheart/car_server_shutdown.log", "a+");
fwrite($fp, date('Y-m-d H:i:s'));
fwrite($fp, ob_get_clean());
fclose($fp);
?>