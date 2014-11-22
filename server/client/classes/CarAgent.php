<?php
set_time_limit(0);

define('IN_ECS', true);
require_once (dirname(__FILE__) . '/../../cp/includes/init.php');
require_once (ROOT_PATH . "../client/classes/UdpServer.php");
require_once (ROOT_PATH . "../client/classes/UserManager.php");
require_once (ROOT_PATH . "../client/classes/OrderManager.php");

define("CMDSTATE_NOT_SEND", 0);
define("CMDSTATE_SENT", 1);
define("CMDSTATE_RECEIVE_SUCC", 2);
define("CMDSTATE_RECEIVE_FAILED", 3);

class CarAgent {

	/**
	 *
	 * @var UdpServer
	 */
	protected $mServer = null;
	protected $mDb = null;

	/**
	 * @var OrderManager
	 */
	protected $mOrderManager = null;

	public function CarAgent() {
		$this -> mServer = new UdpServer();

		global $db;
		$this -> mDb = $db;

		$this -> mOrderManager = new OrderManager();
	}

	public function ring($argv) {
		$arr = $this -> mOrderManager -> getMyRecentAvailableOrder();
		if (empty($arr)) {
			throw new Exception('', 4006);
		}

		$carId = $arr['car_id'];
		$this -> sendCmd($carId, '{"pt":"rctrl","spt":"setrt","rtn":5}');
	}

	public function updateOrder($argv) {
		$order = $argv['order'];
		$carId = $argv['car_id'];

		$jsOder = json_encode($order);
		return $this -> sendCmd($carId, '{"pt":"rctrl","spt":"reorder","order":' . $jsOder . '}', true);
	}

	public function queryLBS($argv) {
		$carId = $argv['car_id'];
		return $this -> sendCmd($carId, '{"pt":"rctrl","spt":"getLBS"}', false, true);
	}

	public function openDoor($argv) {
		$carId = $argv['car_id'];
		if (empty($carId)) {
			throw new Exception('', 4000);
		}

		return $this -> sendCmd($carId, '{"pt":"rctrl","spt":"opd"}');
	}

	protected function getCardId($userId) {
		global $ecs, $db;
		$card = $db -> getOne("select c.card_number from " . $ecs -> table("vip_card_issue") . " i
					left join " . $ecs -> table("vip_card") . " c on i.vip_card_id=c.id where i.user_id=" . $userId . " order by c.id desc limit 1 ");
		return $card;
	}

	public function closeDoor($argv) {
		$carId = $argv['car_id'];
		if (empty($carId)) {
			throw new Exception('', 4000);
		}

		return $this -> sendCmd($carId, '{"pt":"rctrl","spt":"cld"}');
	}

	/**
	 * 设置上报时间间隔
	 *
	 * @param unknown_type $argv
	 * @return unknown
	 */
	public function setReportTime($argv) {
		$carId = $argv['car_id'];
		$in = $argv['interval'];
		return $this -> sendCmd($carId, '{"pt":"rctrl","spt":"setret", "retn":' . $in . '}');
	}

	public function toff($argv) {
		$carId = $argv['car_id'];
		return $this -> sendCmd($carId, '{"pt":"rctrl","spt":"toff"}');
	}

	public function connectPower($argv) {
		$carId = $argv['car_id'];
		return $this -> sendCmd($carId, '{"pt":"rctrl","spt":"cpow"}');
	}

	/**
	 * 目前车载设备没有这个接口
	 *
	 * @param unknown_type $argv
	 * @return unknown
	 */
	public function setFactor($argv) {
		$carId = $argv['car_id'];
		$factor = $argv['factor'];
		return $this -> sendCmd($carId, '{"pt":"rctrl","spt":"setfac","fac":' . $factor . '}');
	}

	public function playVoice($argv) {
		$carId = $argv['car_id'];
		$index = $argv['index'];
		return $this -> sendCmd($carId, '{"pt":"rctrl","spt":"playV","voice":' . $index . '}');
	}

	public function reset($argv) {
		$carId = $argv['car_id'];
		return $this -> sendCmd($carId, '{"pt":"rctrl","spt":"reset"}');
	}

	public function setKeyId($argv) {
		$carId = $argv['car_id'];
		$keyId = $argv['key_id'];
		return $this -> sendCmd($carId, '{"pt":"rctrl","spt":"setkeyid","keyid":"' . $keyId . '"}');
	}

	public function getKeyId($argv) {
		$carId = $argv['car_id'];
		return $this -> sendCmd($carId, '{"pt":"rctrl","spt":"getKeyID"}', false, true);
	}

	public function creditCardOpenDoor($argv) {
		$arr = $this -> mOrderManager -> getMyRecentAvailableOrder();
		if (!$arr) {
			throw new Exception('', 4006);
		}

		$carId = $arr['car_id'];

		$cardId = $this -> getCardId(UserManager::getLoginUserId());
		if (empty($cardId)) {
			$cardId = UserManager::getLoginUserId();
		}
		$this -> sendCmd($carId, '{"pt":"rctrl","spt":"ccod","userid":' . (int)$cardId . '}');
	}

	public function creditCardCloseDoor($argv) {
		$arr = $this -> mOrderManager -> getMyRecentAvailableOrder();
		if (empty($arr)) {
			throw new Exception('', 4006);
		}

		$carId = $arr['car_id'];

		$userId = UserManager::getLoginUserId();
		$cardId = $this -> getCardId($userId);
		if (empty($cardId)) {
			$cardId = $userId;
		}
		$this -> sendCmd($carId, '{"pt":"rctrl","spt":"cccd","userid":' . (int)$cardId . '}');
	}

	/**
	 * 本函数只是把命令插入数据库，等待下次车载设备心跳时，带回响应给他，就是发送
	 */
	public function sendCmd($carId, $addon, $needResend = false, $needResult = false) {
		$port = 0;

		if ($needResult) {
			$socket = socket_create(AF_INET, SOCK_DGRAM, 0);
			socket_bind($socket, 0);
			socket_getsockname($socket, $ip, $port);
		}

		$sendTime = date("Y-m-d H:i:s");
		global $ecs;
		$sqlNeedResend = $needResend ? "1" : "0";
		$sql = "INSERT INTO " . $ecs -> table("car_cmd_record") . "( car_id , send_time , wait_port , addon , packet_number , state , need_resend) VALUES ( $carId , '$sendTime' , $port , '$addon' , -1 , 0 , $sqlNeedResend )";
		$this -> mDb -> query($sql);

		$id = $this -> mDb -> insert_id();

		if ($needResult) {
			socket_recvfrom($socket, $buf, 1024, 0, $n, $p);
		}

		return $buf;
	}

	protected function send($str) {
		$socket = socket_create(AF_INET, SOCK_DGRAM, 0);
		if (!is_resource($socket)) {
			throw new Exception("系统内部错误:建立服务失败", 5000);

			// echo "system error: can't create socket.";
			// return false;
		}

		$l = strlen($str);
		if ($l != socket_sendto($socket, $str, $l, 0, "127.0.0.1", $argv['port'])) {
			throw new Exception("系统内部错误:连接服务失败", 5000);
			return false;
		} else {
			return true;
		}
	}

	static public function getMileOrFactor($start, $end, $factorOrMile) {
		return ($end - $start) / $factorOrMile;
	}

}
?>