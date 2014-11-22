<?PHP
include_once ("../include/PubMarco.php");
require_once (ROOT_PATH . "/client/UdpServer.php");
require_once (ROOT_PATH . "/include/FileUtile.php");
require_once (ROOT_PATH . "/include/Logger.php");
require_once (ROOT_PATH . "/include/ConfigOpr.php");
require_once (ROOT_PATH . "/client/CarAgent.php");
require_once (ROOT_PATH . "../hfrm/Frm.php");
require_once (ROOT_PATH . "../order/classes/OrderManager.php");
require_once (ROOT_PATH . "../resource/classes/CarDeviceManager.php");

$action = $_REQUEST['action'] ? $_REQUEST['action'] : 'CDataAnalysis';
$method = $_REQUEST['method'] ? $_REQUEST['method'] : 'Analysis';

$clientip = $_REQUEST['client_ip'];
$clientport = $_REQUEST['client_port'];
$strMsg = $_REQUEST['msg'];

$g_strAddr = (string)$clientip;
$g_strPort = (string)$clientport;
$g_strMsg = (string)$strMsg;

function msgProcessShutdownLog() {
	$err = error_get_last();
	if (!empty($err)) {
		Logger($err['message'], ROOT_PATH . CAR_FOLDER . "/Error.log", true);
	}
}

register_shutdown_function("msgProcessShutdownLog");

$obj = new $action();

if (!method_exists($obj, $method)) {
	echo "method_exists failed.";
}

$obj -> {$method}();

class CDataAnalysis {
	protected $mDb = null;

	public function CDataAnalysis() {
		$db = Frm::getInstance() -> getDb();
		$this -> mDb = $db;
	}

	public function Analysis() {
		$arrMsg = json_decode($GLOBALS['g_strMsg'], true);

		if (2 == count($arrMsg)) {
			$bmsg = $arrMsg[0];
			//basic msg
			$amsg = $arrMsg[1];
			//addon msg
		} else {
			$bmsg = $arrMsg;
		}

		$strCarFolder = ROOT_PATH . CAR_FOLDER . "/" . $bmsg['cid'];
		//modify by yangbei 2013-04-12 修改上行日志添加字符串“in：”
		//Logger($GLOBALS['$g_strMsg'], $strCarFolder . "/" . date('Y-m-d') . ".log", true);
		Logger('in '.$GLOBALS['g_strMsg'], $strCarFolder . "/" . date('Y-m-d') . ".log", true);
		//modify by yangbei 2013-04-12
		
		$this -> DoAck($GLOBALS['g_strAddr'], $GLOBALS['g_strPort'], $bmsg['cid'], $bmsg['pn']);

		if ('rctrlack' == $bmsg['pt']) {//反向数据类报文,用于查询消息（查询车辆的LBS）
			//如果消息队列里有消息，说明是下行消息的回复。处理回复
			$this -> processCmdImmediateRet($msgArr['cid'], $msgArr['pn'], $msg);

			//rctrlack不需要再次ack
			return;
		} else {
			if ($bmsg['spt'] == "bcpk") {
				//$bmsg['gps'] = array(11618.2693, 4005.2302);
				// add by liming 2013-4-2 增加数组内键值是否存在的判断
				$strDicH = "";
				$strDicL = "";
				$strLon	= "";
				$strLat = "";
				$strOil = "";
				if (array_key_exists('dicH', $bmsg))
					$strDicH = $bmsg['dicH'];
				if (array_key_exists('dicL', $bmsg))
					$strDicL = $bmsg['dicL'];
				if (array_key_exists('oil', $bmsg))
					$strOil = $bmsg['oil'];
				if (array_key_exists('gps', $bmsg))
				{
					$strLon = $bmsg['gps'][0];
					$strLat = $bmsg['gps'][1];
				}
				// add by liming 2013-4-2 

				$this -> DoBasicRpt($GLOBALS['g_strAddr'], $GLOBALS['g_strPort'], $bmsg['cid'], $bmsg['sysstatue'], $bmsg['svs'], $bmsg['cts'], $strOil, $strDicH, $strDicL, $strLon, $strLat);
			}
		}

		if (2 == count($arrMsg)) {
			Logger("start call interface.", $strCarFolder . "/" . date('Y-m-d') . ".log", true);
			try {
				switch ($amsg['spt']) {
					case 'buc' :
						// 开始用车上报数据报文

						$ordMgr = new OrderManager();
						$ordMgr -> onBeginUseCar($this -> parseTime($amsg['stime']), $amsg['sdicH'], $amsg['sdicL'], $amsg['odn']);
						//					Logger("Begin Use car.", $strCarFolder. "/".date('Y-m-d').".log", true);
						break;
					case 'euc' :
						// 结束用车上报数据报文
						$ordMgr = new OrderManager();
						$ordMgr -> onEndUseCar($this -> parseTime($amsg['etime']), $amsg['edicH'], $amsg['edicL'], $amsg['odn']);
						break;
					case 'thpw' :
						// 3次密码错误上报数据报文
						break;
					case 'eucclodoor' :
						// 刷卡锁门
						$ordMgr = new OrderManager();
						// Logger("begin onCardCloseDoor.", $strCarFolder. "/".date('Y-m-d').".log", true);
						$ordMgr -> onCardCloseDoor($amsg['cn']);
						// Logger("end onCardCloseDoor.", $strCarFolder. "/".date('Y-m-d').".log", true);
						break;
				}

				Logger("end call interface.errc", $strCarFolder . "/" . date('Y-m-d') . ".log", true);
			} catch(Exception $e) {
				Logger("error call interface.errno:" . $e -> getCode(), $strCarFolder . "/" . date('Y-m-d') . ".log", true);
			}
		}
	}

	protected function processCmdImmediateRet($carId, $msg) {
		global $ecs;

		//还没有下发的消息里是没有packet_number
		$sql = "SELECT wait_port FROM " . $ecs -> table("car_cmd_record") . " WHERE car_id = $carId AND state = " . CMDSTATE_SENT;
		$port = $this -> mDb -> getOne($sql);
		if ($port > 0) {
			$socket = socket_create(AF_INET, SOCK_DGRAM, 0);
			socket_sendto($socket, $jsMsg, strlen($jsMsg), 0, '127.0.0.1', $port);
		}

		$sql = "UPDATE " . $ecs -> table("car_cmd_record") . " SET state = " . CMDSTATE_RECEIVE_SUCC . " WHERE car_id = $carId AND state = " . CMDSTATE_SENT;
		$this -> mDb -> query($sql);
	}

	protected function GetNewOrder($strCid, &$nId) {
		$sql = "SELECT id , addon FROM edo_cp_car_cmd_record WHERE car_id = '$strCid' AND 
			( state = " . CMDSTATE_NOT_SEND . " OR state = " . CMDSTATE_RECEIVE_FAILED . " ) ORDER BY send_time ";
		$row = $this -> mDb -> getRow($sql);
		$nId = $row['id'];

		return $row['addon'];
	}

	protected function UpdateCarOrder($nId, $strCid, $nSendStatus) {
		$sql = "UPDATE edo_cp_car_cmd_record SET state = " . $nSendStatus . " WHERE id = " . $nId;
		$this -> mDb -> execSql($sql);
	}

	protected function DoAck($strAddr, $strPort, $strCid, $nPn, $strAddon = "") {
		//收到消息就回复ack
		$nId = 0;
		$server = new UdpServer();
		$strPnFile = ROOT_PATH . CAR_FOLDER . "/" . $strCid . "/pn.cfg";
		$strLogFile = ROOT_PATH . CAR_FOLDER . "/" . $strCid . "/" . date('Y-m-d') . ".log";
		$nStatus = CMDSTATE_RECEIVE_FAILED;

		if ($nPn == 0) {
			// 车载设备重启，相关信息全部置空
			WritePn($strCid, 0, $strPnFile);
			Logger("Device has been restart." . $strLogFile, true);
		}

		$arrCfg = parse_ini_file($strPnFile);
		$nLastPn = $arrCfg['Pn'];
		if ($nLastPn == "") {
			$nLastPn = (int)0;
		}

		if (0 <= $nLastPn) {
			$nLastPn++;
			if ($nLastPn == 256) {
				$nLastPn = 1;
			}

			if ($nLastPn == $nPn && $nPn > 0) {
				// 成功发送
				$nStatus = CMDSTATE_RECEIVE_SUCC;
			}

			$nId = $arrCfg['Id'];
			if (empty($nId)) {
				$nId = 0;
			}
			$this -> UpdateCarOrder($nId, $strCid, $nStatus);
		}

		$strAddon = $this -> GetNewOrder($strCid, $nId);

		if (0 < strlen($strAddon)) {
			// 发现订单后，记录PN值（PN，判断包是否下发成功）
			WritePn($nId, $nPn, $strPnFile);
		} else {
			WritePn($strCid, -1, $strPnFile);
		}

		if (!$server -> ack($strCid, $nPn, $strAddr, $strPort, $strAddon)) {
			$strCarFolder = ROOT_PATH . CAR_FOLDER . "/" . $strCid;
			Logger("Do ack packet failed. pn=" . $nPn, ROOT_PATH . CAR_FOLDER . "/" . $strCid . "/" . date('Y-m-d') . ".log", true);
		}
	}

	protected function DoBasicRpt($strAddr, $strPort, $strCid, $strSysstatue, $strSvs, $strCts, $strOil, $strDicH, $strDicL, $strLon, $strLat) {
		$strIni = ROOT_PATH . CAR_FOLDER . "/" . $strCid . "/" . CAR_CONFIG_FILE;
		$bNotify = false;

		$newData = array('ClientAddr' => $strAddr, 'ClientPort' => $strPort, 'sysstatue' => $strSysstatue, 'svs' => $strSvs, 'cts' => $strCts, 'oil' => $strOil, 'dicH' => $strDicH, 'dicL' => $strDicL, 'VssRecTime' => '', 'ClientLon' => $strLon, 'ClientLat' => $strLat);
		$arrItem = array();

		if (file_exists($strIni)) {
			// 信息文件存在，则读取文件内容，判断状态是否有变化，如果有，通知更新
			$arrItem = parse_ini_file($strIni);

			if (!empty($strDicL)) {
				$newData['VssRecTime'] = date('Y-m-d H:i:s');
			}

			foreach ($newData as $key => $value) {
				// Modify by liming 2013-4-7 修改了VSS不上报的问题
				//if (empty($value)) {//如果这次没有上报数据，说明没有变动。
				// Modify by liming 2013-4-7
				if (empty($newData[$key])) {//如果这次没有上报数据，说明没有变动。
					$newData[$key] = $arrItem[$key];
				}
			}

			if ($strSysstatue != $arrItem['sysstatue'] || $strSvs != $arrItem['svs'] || $strCts != $arrItem['cts']) {
				$bNotify = true;
			}

			$fLon = floatval($strLon);
			$fLat = floatval($strLat);
			$fLonRec = floatval($arrItem['ClientLon']);
			$fLatRec = floatval($arrItem['ClientLat']);
			if (abs($fLon - $fLonRec) > 0.01 || abs($fLat - $fLatRec) > 0.01) {
				// 车载设备位移变化超过0.1分，通知服务器进行更新最新位置
				// 同时更新信息文件内容
				$bNotify = true;
			}
		} else {
			$bNotify = true;
		}

		//要把最后一次通信时间写进去
		Write($strIni, $newData);

		if ($bNotify) {
			$param = array();
			if (!empty($strSysstatue) && $strSysstatue != $arrItem['sysstatue']) {
				$param['sys_state'] = $strSysstatue;
			}
			if (!empty($strSvs) && $strSvs != $arrItem['svs']) {
				$param['software_version'] = $strSvs;
			}
			if (!empty($strCts) && $strCts != $arrItem['cts']) {
				$param['cts'] = $strCts;
			}
			if (!empty($strLon) && ($strLon != $arrItem['ClientLon'] || $strLat != $arrItem['ClientLat'])) {
				$param['longitude'] = $strLon;
				$param['latitude'] = $strLat;
			}

			$CarDevMgr = new CarDeviceManager();
			$CarDevMgr -> onDeviceStatusChange($strCid, $param);
		}
	}

	protected function parseTime($stime) {
		$t = $this -> parseInt($stime);
		return $t + strtotime("2009-01-01");
	}

	protected function parseInt($str) {
		$tmp = substr($str, 6, 2);
		$tmp .= substr($str, 4, 2);
		$tmp .= substr($str, 2, 2);
		$tmp .= substr($str, 0, 2);

		$arr = sscanf($tmp, "%08X");

		return $arr[0];
	}

}
?>