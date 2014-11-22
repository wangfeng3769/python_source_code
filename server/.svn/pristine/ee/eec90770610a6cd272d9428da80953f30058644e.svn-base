<?php
set_time_limit(0);

define('IN_ECS', true);
require_once (dirname(__FILE__) . '/../../cp/includes/init.php');
require_once (ROOT_PATH . "../client/include/Utile.php");
require_once (ROOT_PATH . "../client/classes/UdpServer.php");
require_once (ROOT_PATH . "../client/classes/CarAgent.php");

function msgProcessShutdownLog(){
	$err = error_get_last();
	if( ! empty( $err ) ) {
		ob_start();
		print_r( $err );

		$fp = fopen( "/home/hoheart/msg_process.log" , "a+" );
		fwrite( $fp , ob_get_clean() );
		fclose( $fp );
	}
}
register_shutdown_function( "msgProcessShutdownLog" );

class MsgProcessor {

	/**
	 *
	 * @var cls_mysql
	 */
	protected $mDb = null;

	public function MsgProcessor() {
		global $db;
		$this -> mDb = $db;
	}

	public function process($argv) {
		$msg = $argv['msg'];
		$clientIp = $argv['client_ip'];
		$clientPort = $argv['client_port'];

		$msgArr = json_decode($msg, true);
		if (2 == count($msgArr)) {
			$bmsg = $msgArr[0];
			//basic msg
			$amsg = $msgArr[1];
			//addon msg
		} else {
			$bmsg = $msgArr;
		}

		$this -> processBasic($bmsg, $amsg);
		$this -> doAck($bmsg, $clientIp, $clientPort);

		//协议规定，下行的非查询消息，不ack，但立即上报定时的空闲消息。
		if ('rctrlack' == $bmsg['pt']) {//反向数据类报文,用于查询消息（查询车辆的LBS）
			//如果消息队列里有消息，说明是下行消息的回复。处理回复
			$this -> processCmdImmediateRet($msgArr['cid'], $msgArr['pn'], $msg);
			$fp = fopen( "/home/hoheart/rctrlack.txt" , "a+" );
			fwrite($fp, $msg);
			fclose($fp);

			//rctrlack不需要再次ack
			return;
		} else {//$bmsg['pt'] == 'ctrl'
			switch ($amsg['spt']) {
				case 'buc' :
					$fp = fopen("/home/hoheart/buc.txt", "a+");
					ob_start();
					print_r($amsg);
					fwrite($fp, ob_get_clean());
					$this -> beginUseCar($amsg);
					break;
				case 'euc' :
					$fp = fopen("/home/hoheart/euc.txt", "a+");
					ob_start();
					print_r($amsg);
					fwrite($fp, ob_get_clean());
					$this -> endUseCar($amsg);
					break;

				default :
					break;
			}
		}
	}

	protected function processBasic($msg, $amsg) {
		global $ecs;

		$jsAddon = json_encode($amsg);
		$sql = "INSERT INTO " . $ecs -> table("car_tracer") . " ( car_id , sys_state , soft_version , packet_number , ctrl_state , oil , distance_count , longitude , latitude , time , addon ) VALUES
			( {$msg['cid']} , {$msg['sysstatue']} , {$msg['svs']} , {$msg['pn']} , {$msg['cts']} , '{$msg['oil']}' , '{$msg['dic']}' , '{$msg['gps'][0]}' , '{$msg['gps'][1]}' , " . time() . " , '$jsAddon') ";
		$this -> mDb -> query($sql);

		if (0 == $msg['pn']) {
			$this -> processDeviceRestart($msg['cid']);
		} else {
			//如果cmd里有不需要回复的消息，要根据下一次心跳的PN是否加1来判断cmd是否成功
			$this -> processCmdRet($msg['cid'], $msg['pn']);
		}
	}

	protected function doAck($msg, $clientIp, $clientPort) {
		global $ecs;

		$carId = $msg['cid'];

		//找有没有cmd要下发。不能直接给车载设备下发消息，只能把消息放到ack里
		$sql = "SELECT id , addon FROM " . $ecs -> table("car_cmd_record") . " WHERE car_id = $carId AND state = " . CMDSTATE_NOT_SEND . " ORDER BY send_time ";
		$row = $this -> mDb -> getRow($sql);

		//收到消息就回复ack
		$server = new UdpServer();
		$addon = $row['addon'];
		if ($server -> ack($msg['cid'], $msg['pn'], $clientIp, $clientPort, $addon)) {
			if (!empty($row)) {
				$id = $row['id'];

				//记录此次的PN，当下一次心跳上来后，如果PN+1了，表示消息下发成功，否则不成功
				$sql = "UPDATE " . $ecs -> table("car_cmd_record") . " SET packet_number = {$msg['pn']} , state = " . CMDSTATE_SENT . " WHERE id = $id";
				//1表示已经下发
				$this -> mDb -> query($sql);
			}
		}
	}

	protected function processDeviceRestart($carId) {
		global $ecs;

		$sql = "SELECT id , packet_number , addon FROM " . $ecs -> table("car_cmd_record") . " WHERE car_id = $carId AND state = " . CMDSTATE_SENT . " AND need_resend = 1";
		$arr = $this -> mDb -> getAll($sql);

		//因为PN只能是+1递增，每次下发的消息也只能是一条，所以不会出现新的CMDSTATE_SENT消息。所以就直接update所有好了
		$sql = "UPDATE " . $ecs -> table("car_cmd_record") . " SET state = " . CMDSTATE_RECEIVE_FAILED . " WHERE car_id = $carId AND state = " . CMDSTATE_SENT;
		$this -> mDb -> query($sql);

		if (is_array($arr)) {
			foreach ($arr as &$row) {
				if (1 == $row['need_resend']) {
					//重发
					self::sendCmd($carId, $row['addon'], true);
				}
			}
		}
	}

	protected function processCmdRet($carId, $pn) {
		global $ecs;

		$lastPn = $pn - 1;
		if ($lastPn < 1) {
			$lastPn = 255;
			//PN是在1-255中间循环的
		}

		$sql = "SELECT id , packet_number , addon , need_resend FROM " . $ecs -> table("car_cmd_record") . " WHERE car_id = $carId AND state = " . CMDSTATE_SENT . " AND ( packet_number = $pn OR packet_number = $lastPn - 1 )";
		$arr = $this -> mDb -> getAll($sql);
		if (is_array($arr)) {
			foreach ($arr as &$row) {
				if ($pn == $row['packet_number']) {
					//有相同的PN，说明是没接收成功，要不PN就+1了，那么找到的应该是PN-1的那条
					$sql = "UPDATE " . $ecs -> table("car_cmd_record") . " SET state = " . CMDSTATE_RECEIVE_FAILED . " WHERE id = {$row['id']}";
					$this -> mDb -> query($sql);

					if (1 == $row['need_resend']) {
						//重发
						$carAgent = new CarAgent();
						$carAgent -> sendCmd($carId, $row['addon'], true);
					}
				} else {//sql语句里只选了两种情况
					$sql = "UPDATE " . $ecs -> table("car_cmd_record") . " SET state = " . CMDSTATE_RECEIVE_SUCC . " WHERE id = {$row['id']}";
					$this -> mDb -> query($sql);
				}
			}
		}
	}

	protected function processCmdImmediateRet($carId, $pn, $msg) {
		global $ecs;

		//还没有下发的消息里是没有packet_number
		$sql = "SELECT wait_port FROM " . $ecs -> table("car_cmd_record") . " WHERE car_id = $carId AND packet_number = $pn AND state = " . CMDSTATE_SENT;
		$port = $this -> mDb -> getOne($sql);
		if ($port > 0) {
			$socket = socket_create(AF_INET, SOCK_DGRAM, 0);
			socket_sendto($socket, $jsMsg, strlen($jsMsg), 0, '127.0.0.1', $port);
		}

		$sql = "UPDATE " . $ecs -> table("car_cmd_record") . " SET state = " . CMDSTATE_RECEIVE_SUCC . " WHERE car_id = $carId AND $pn = $pn AND state = " . CMDSTATE_SENT;
		$this -> mDb -> query($sql);
	}

	protected function parseInt($str) {
		$tmp = substr($str, 6, 2);
		$tmp .= substr($str, 4, 2);
		$tmp .= substr($str, 2, 2);
		$tmp .= substr($str, 0, 2);

		$arr = sscanf($tmp, "%08X");

		return $arr[0];
	}

	protected function parseTime($stime) {
		$t = $this -> parseInt($stime);
		return $t + strtotime("2009-01-01");
	}

	protected function beginUseCar($amsg) {
		$time = $this -> parseTime($amsg['stime']);
		$mileage = $amsg['sdicH'] . $amsg['sdicL'];
		$orderId = $amsg['odn'];

		global $ecs, $db;
		//2为执行中
		$sql = "UPDATE " . $ecs -> table("car_order") . " SET real_start_time = $time , start_mileage = $mileage , order_stat = 2 WHERE order_id = $orderId";
		$db -> query($sql);
	}

	protected function endUseCar($amsg) {
		$time = $this -> parseTime($amsg['etime']);
		$mileage = $amsg['edicH'] . $amsg['edicL'];
		$orderId = $amsg['odn'];

		global $ecs, $db;
		//3表示已完成
		$sql = "UPDATE " . $ecs -> table("car_order") . " SET real_end_time = $time , end_mileage = $mileage , order_stat = 3 WHERE order_id = $orderId";
		$db -> query($sql);

		//结算订单
		$orderManager = new OrderManager();
		$orderManager -> settlement($orderId);
	}

}
?>