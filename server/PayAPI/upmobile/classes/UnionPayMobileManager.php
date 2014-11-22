<?php

class UnionPayMobileManager {

	//正式帐号
	static public $TERMINAL_ID = "18989468";
	static public $MERCHANT_ID = "898110148994677";
	static protected $UNIONPAY_KEY_PATH = 'PayAPI/upmobile/cert/edoauto.pem';
	//测试帐号
	// static public $TERMINAL_ID = "00000001";
	// static public $MERCHANT_ID = "100011000110146";
	// static protected $UNIONPAY_KEY_PATH = 'PayAPI/upmobile/cert/edoauto.pem';

	static public $MERCHANT_NAME = "易多汽车共享";

	public function UnionPayMobileManager() {
	}

	public function callBackReq($xml) {
		require_once (Frm::$ROOT_PATH . 'hfrm/hfc/Dom.php');
		$dom = new Dom();
		$dom -> loadXml($xml);

		$node = $dom -> getNode("transaction");
		$type = $node -> getAttr("type");
		switch ($type) {
			case 'Purchase.PMReq' :
				$this -> purchase($dom);
				break;
			case 'Reversal.PMReq' :
				$this -> reversal($dom);
				break;

			default :
				break;
		}
	}

	/**
	 * 支付结果通知
	 */
	public function purchase($dom) {
		// @formatter:off
		$respFmt = '<?xml version="1.0" encoding="UTF-8"?>' . '<cupMobile application="%s" version="%s">' . '<transaction type="PurchaseAdvice.MPRsp">' . '<submitTime>%s</submitTime>' . '<order id="%s">' . '<generateTime>%s</generateTime>' . '</order>' . '<transAmount currency="156">%s</transAmount>' . '<terminal id="' . self::$TERMINAL_ID . '"/>' . '<merchant id="' . self::$MERCHANT_ID . '"/>' . '<accountNumber1>%s</accountNumber1>' . '<responseCode>%s</responseCode>' . '<transSerialNumber>%s</transSerialNumber>' . '<billAmount currency="156">%s</billAmount>' . '<settleDate>%s</settleDate>' . '</transaction>' . '</cupMobile>';
		//@formatter:on

		$application = $dom -> getAttr("application");
		$version = $dom -> getAttr("version");

		$nTransaction = $dom -> getNode("transaction");

		$submitTime = $nTransaction -> getValue("submitTime");

		$nOrder = $nTransaction -> getNode("order");

		$orderId = $nOrder -> getAttr("id");
		$generateTime = $nOrder -> getValue("generateTime");

		$transAmount = $nTransaction -> getValue("transAmount");
		$accountNumber1 = $nTransaction -> getValue("accountNumber1");
		$transSerialNumber = $nTransaction -> getValue("transSerialNumber");
		$billAmount = $nTransaction -> getValue("billAmount");
		$settleDate = $nTransaction -> getValue("settleDate");
		$transmitTime = $nTransaction -> getValue("transmitTime");

		//@formatter:off
		$id = $this -> log(array('trans_type' => 'Purchase', 'submit_time' => $submitTime, 'order_id' => $orderId, 'order_generate_time' => $generateTime, 'settle_date' => $settleDate, 'transmit_time' => $transmitTime, 'bill_amount' => $billAmount, 'account_number1' => $accountNumber1, 'trans_serial_number' => $transSerialNumber, 'trans_amount' => $transAmount));
		//@formatter:on

		require_once (Frm::$ROOT_PATH . 'PayAPI/upmobile/classes/Payment.php');
		$p = new PurchaseObj();
		$p -> id = $id;
		$p -> orderId = $orderId;
		$p -> amount = (int)$billAmount;
		$p -> time = strtotime($submitTime);

		$rspCode = '000';
		require_once (Frm::$ROOT_PATH . 'PayAPI/upmobile/conf/listener.php');
		foreach ($listener as $val) {
			require_once ($val['classPath']);
			$l = new $val['className'];
			if ($l -> onPurchase($p)) {
				//只要有一个监听器接受了该消息，就不再往下发
				$rspCode = '001';

				break;
			}
		}

		$resp = sprintf($respFmt, $application, $version, $submitTime, $orderId, $generateTime, $transAmount, $accountNumber1, $rspCode, $transSerialNumber, $billAmount, $settleDate);
		echo $resp;
		flush();
	}

	protected function log($arr) {
		$sql = "INSERT INTO upp_payment_log( trans_type , submit_time , order_id , order_generate_time  ,
			settle_date , bill_amount , account_number1 , trans_serial_number , trans_amount ) 
			VALUES 
			( '{$arr['trans_type']}' , '{$arr['submit_time']}' , '{$arr['order_id']}' , '{$arr['order_generate_time']}' ,
			'{$arr['settle_date']}' , '{$arr['bill_amount']}' , '{$arr['account_number1']}' , 
			'{$arr['trans_serial_number']}' , '{$arr['trans_amount']}' )";
		$db = Frm::getInstance() -> getDb();
		$trans = $db -> startTransaction();
		$db -> execSql($sql);
		$id = $trans -> lastId();
		$trans -> commit();

		return $id;
	}

	/**
	 * 冲正交易
	 */
	public function reversal($dom) {
		$nCupMobile = $dom -> getNode("cupMobile");

		$application = $nCupMobile -> getAttr("application");
		$version = $nCupMobile -> getAttr("version");

		$nTransaction = $nCupMobile -> getNode("transaction");

		$submitTime = $nTransaction -> getValue("origSubmitTime");

		$nOrder = $nTransaction -> getNode("order");

		$orderId = $nOrder -> getAttr("id");
		$generateTime = $nOrder -> getValue("generateTime");

		$transAmount = $nTransaction -> getValue("transAmount");
		$accountNumber1 = $nTransaction -> getValue("accountNumber1");
		$transSerialNumber = $nTransaction -> getValue("transSerialNumber");
		$settleDate = $nTransaction -> getValue("settleDate");

		// @formatter:off
		$rsp = '<?xml version="1.0" encoding="UTF-8"?>' . '<cupMobile application="' . $application . '" version="' . $version . '">' . '<transaction type="Reversal.MPRsp">' . '<submitTime>' . $submitTime . '</submitTime>' . '<order id="' . $orderId . '">' . '<generateTime>' . $generateTime . '</generateTime>' . '</order>' . '<transAmount currency="156">' . $transAmount . '</transAmount>' . '<merchant id="' . self::$MERCHANT_ID . '"/>' . '<accountNumber1>' . $accountNumber1 . '</accountNumber1>' . '<responseCode>000</responseCode>' . '<transSerialNumber>' . $transSerialNumber . '</transSerialNumber>' . '<settleDate>' . $settleDate . '</settleDate>' . '</transaction>' . '</cupMobile>';
		//@formatter:on
		echo $rsp;
		flush();

		require_once (Frm::$ROOT_PATH . 'PayAPI/upmobile/classes/Payment.php');
		$p = new ReversalObj();
		$p -> orderId = $orderId;
		$p -> time =

		require_once (Frm::$ROOT_PATH . 'PayAPI/upmobile/conf/listener.php');
		foreach ($listener as $val) {
			require_once ($val['classPath']);
			$l = new $val['className'];
			if ($l -> onPurchase($m)) {
				//只要有一个监听器接受了该消息，就不再往下发
				break;
			}
		}
	}

	public function refund($orderId, $amount, $origAccountNumber1, $origSubmitTime, $origTransactionNumber, $tSettleDate) {
		$now = time();
		//@formatter:off
		$logId = $this -> log(array('trans_type' => 'Refund', 'submit_time' => $now, 'order_id' => $orderId, 'settle_date' => $tSettleDate, 'account_number1' => $origAccountNumber1, 'trans_serial_number' => $origTransactionNumber, 'trans_amount' => $amount));
		//@formatter:on

		$strAmount = sprintf("%012d", $amount);
		$strOrigSubmitTime = date('YmdHis', $origSubmitTime);
		$settleDate = date('Ymd', $tSettleDate);

		// @formatter:off
		$sNow = date('YmdHis');
		$content = '<?xml version="1.0" encoding="UTF-8"?>' . '<cupMobile application="UPNoCards" version="1.01">' . '<transaction type="Refund.MPReq">' . '<submitTime>' . $sNow . '</submitTime>' . '<order id="' . $orderId . '">' . '<generateTime>' . $sNow . '</generateTime>' . '</order>' . '<transAmount currency="156">' . $strAmount . '</transAmount>' . '<terminal id="' . self::$TERMINAL_ID . '"/>' . '<merchant id="' . self::$MERCHANT_ID . '" name="' . self::$MERCHANT_NAME . '"/>' . '<accountNumber1>' . $origAccountNumber1 . '</accountNumber1>' . '<origSubmitTime>' . $strOrigSubmitTime . '</origSubmitTime>' . '<origTransSerNo>' . $origTransactionNumber . '</origTransSerNo>' . '<settleDate>' . $settleDate . '</settleDate>' . '</transaction>' . '</cupMobile>';
		//@formatter:on

		$xml = $this -> httpsPost($content);
		$pos = strpos($xml, 'responseCode');
		if ($pos > 0) {
			$rspCode = substr($xml, $pos + 13, 3);

			$sql = "UPDATE upp_payment_log SET errcode = '$rspCode' WHERE id = $logId";
			$db = Frm::getInstance() -> getDb();
			$db -> execSql($sql);
		}

		return array('logId' => $logId, 'rspCode' => $rspCode);
	}

	public function httpsPost($content) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://202.96.255.146:8443/easypay/merchant');

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		// 客户端证书
		curl_setopt($ch, CURLOPT_SSLCERT, Frm::$ROOT_PATH . 'UPPay/edoauto.pem');
		curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');

		// 验证服务器证书
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
		curl_setopt($ch, CURLOPT_CAINFO, Frm::$ROOT_PATH . 'UPPay/trust.crt');

		//数据
		curl_setopt($ch, CURLOPT_POSTFIELDS, $content);

		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}

	public function getUPPayOrderInfo($orderId, $orderAmount) {
		$strOrderAmount = sprintf("%012d", $orderAmount);
		// @formatter:off
		$orderInfoFmt = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>" . "<cupMobile version=\"1.01\" application=\"UPNoCard\">" . "<transaction type=\"Purchase.MARsp\">" . "<submitTime>%s</submitTime>" . "<order id=\"%s\">EduoAuto</order>" . "<transAmount currency=\"156\">%s</transAmount>" . "<terminal id=\"" . self::$TERMINAL_ID . "\"/>" . "<merchant name=\"" . self::$MERCHANT_NAME . "\" id=\"" . self::$MERCHANT_ID . "\"/></transaction><senderSignature>%s</senderSignature></cupMobile>";

		$orderTime = date('YmdHis');
		$sign = $this -> getOrderSign($orderTime, $orderId, $strOrderAmount);

		$orderInfo = sprintf($orderInfoFmt, $orderTime, $orderId, $strOrderAmount, $sign);
		//echo $orderInfo;exit;
		return $orderInfo;
	}

	protected function getOrderSign($orderTime, $orderId, $orderAmount) {
		$data = $orderTime . $orderId . $orderAmount . "156" . self::$TERMINAL_ID . self::$MERCHANT_ID . self::$MERCHANT_NAME;
		//echo $data;exit;
		//$data = "201201091346271234773300000003023115601042900303290047228001test";
		$fp = fopen(Frm::$ROOT_PATH . self::$UNIONPAY_KEY_PATH, "r");
		$priv_key = fread($fp, 8192);
		fclose($fp);
		$pkeyid = openssl_get_privatekey($priv_key);
		openssl_sign($data, $signature, $pkeyid);
		openssl_free_key($pkeyid);
		$len = strlen($signature);
		$str = $signature;
		$result = "";
		$hexArray = "0123456789abcdef";
		for ($i = 0; $i < $len; $i++) {
			if (ord($str[$i]) >= 128) {
				$byte = ord($str[$i]) - 256;
			} else {
				$byte = ord($str[$i]);
			}
			$bytes[] = $byte;
			$byte = $byte & 0xff;
			$result .= $hexArray[$byte>>4];
			$result .= $hexArray[$byte & 0xf];
		}

		return $result;
	}

	public function onRefund($id, $amount) {
		$sql = "SELECT order_id , account_number1 , submit_time , trans_serial_number , settle_date FROM upp_payment_log WHERE id = $id ";
		$db = Frm::getInstance() -> getDb();
		$row = $db -> getRow($sql);

		return $this -> refund($row['order_id'], $amount, $row['account_number1'], strtotime($row['submit_time']), $row['trans_serial_number'], strtotime($row['settle_date']));
	}

}
?>