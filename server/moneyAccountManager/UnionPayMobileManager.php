<?php
set_time_limit(0);
if (!defined('ROOT_PATH')) {
	define('IN_ECS', true);
	require_once (dirname(__FILE__) . '/../cp/includes/init.php');
}
require_once (ROOT_PATH . "../hfrm/hfc/Dom.php");
require_once (ROOT_PATH . "../client/classes/OrderManager.php");
require_once (ROOT_PATH . '../moneyAccountManager/AccountManager.php');
require_once (ROOT_PATH . '../moneyAccountManager/AccountManagerContainer.php');

class UnionPayMobileManager extends AccountManager {

	/**
	 * @var cls_mysql
	 */
	private $mDbAgent = null;
	private $ecs = null;
	private $sns = null;

	//正式帐号
	static public $TERMINAL_ID = "18989468";
	static public $MERCHANT_ID = "898110148994677";
	static protected $UNIONPAY_KEY_PATH = "../UPPay/edoauto.pem";
	//测试帐号
	// static public $TERMINAL_ID = "00000001";
	// static public $MERCHANT_ID = "100011000110146";
	// static protected $UNIONPAY_KEY_PATH = "../yinlian/edoauto.pem";

	static public $MERCHANT_NAME = "易多汽车共享";

	public function UnionPayMobileManager() {
		global $db, $ecs, $sns;
		$this -> mDbAgent = $db;
		$this -> ecs = $ecs;
		$this -> sns = $sns;

		$this -> account_type = "unionPayMobile";
	}

	public function callBackReq($xml) {
		$dom = new Dom();
		$dom -> loadXml($xml);

		$node = $dom -> getNode("cupMobile::transaction");
		$type = $node -> getAttr("type");
		$ret = "";
		switch ($type) {
			case 'Purchase.PMReq' :
				$ret = $this -> purchase($dom);
				break;
			case 'Reversal.PMReq' :
				$ret = $this -> reversal($dom);
				break;

			default :
				break;
		}

		return $ret;
	}

	/**
	 * 支付结果通知
	 */
	public function purchase($dom) {
		// @formatter:off
		$respFmt = '<?xml version="1.0" encoding="UTF-8"?>' .
			'<cupMobile application="%s" version="%s">' .
			'<transaction type="PurchaseAdvice.MPRsp">' .
			'<submitTime>%s</submitTime>' .
			'<order id="%s">' .
			'<generateTime>%s</generateTime>' .
			'</order>' .
			'<transAmount currency="156">%s</transAmount>' .
			'<terminal id="' . self::$TERMINAL_ID . '"/>' .
			'<merchant id="' . self::$MERCHANT_ID . '"/>' .
			'<accountNumber1>%s</accountNumber1>' .
			'<responseCode>000</responseCode>' .
			'<transSerialNumber>%s</transSerialNumber>' .
			'<billAmount currency="156">%s</billAmount>' .
			'<settleDate>%s</settleDate>' .
			'</transaction>' .
			'</cupMobile>';
		//@formatter:on

		$nCupMobile = $dom -> getNode("cupMobile");

		$application = $nCupMobile -> getAttr("application");
		$version = $nCupMobile -> getAttr("version");

		$nTransaction = $nCupMobile -> getNode("transaction");

		$submitTime = $nTransaction -> getValue("submitTime");

		$nOrder = $nTransaction -> getNode("order");

		$orderId = $nOrder -> getAttr("id");
		$generateTime = $nOrder -> getValue("generateTime");

		$transAmount = $nTransaction -> getValue("transAmount");
		$accountNumber1 = $nTransaction -> getValue("accountNumber1");
		$transSerialNumber = $nTransaction -> getValue("transSerialNumber");
		$billAmount = $nTransaction -> getValue("billAmount");
		$settleDate = $nTransaction -> getValue("settleDate");

		$resp = sprintf($respFmt, $application, $version, $submitTime, $orderId, $generateTime, $transAmount, $accountNumber1, $transSerialNumber, $billAmount, $settleDate);
		echo $resp;
		flush();

		$this -> moneyOperate('consume', $accountNumber1, $transactionNumber, $transAmount, "unionpay mobile pay Purchase.", array());

		$orderManager = new OrderManager();
		$userId = $orderManager -> getCreator($orderId);
		$account = AccountManagerContainer::getInstance() -> getAccountManager("eduo");
		$accountId = $account -> getAccountId($userId);
		$account -> moneyOperate('charge', $accountId, $transactionNumber1, $billAmount, "UnionPay Mobile PurchaseAdvice.PMReq: ", array());

		$orderManager -> payFor(array('useType' => 'eduo', 'order_id' => $orderId));

		return $resp;
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
		$rsp = '<?xml version="1.0" encoding="UTF-8"?>' .
			'<cupMobile application="'.$application.'" version="'.$version.'">' .
			'<transaction type="Reversal.MPRsp">' .
			'<submitTime>'.$submitTime.'</submitTime>' .
			'<order id="'.$orderId.'">' .
			'<generateTime>'.$generateTime.'</generateTime>' .
			'</order>' .
			'<transAmount currency="156">'.$transAmount.'</transAmount>' .
			'<merchant id="' . self::$MERCHANT_ID . '"/>' .
			'<accountNumber1>'.$accountNumber1.'</accountNumber1>' .
			'<responseCode>000</responseCode>' .
			'<transSerialNumber>'.$transSerialNumber.'</transSerialNumber>' .
			'<settleDate>'.$settleDate.'</settleDate>' .
			'</transaction>' .
			'</cupMobile>';
		//@formatter:on
		echo $rsp;
		flush();

		$iAmount = (int)$transAmount;
		$this -> moneyOperate('consume', $accountNumber1, $transactionNumber, -$iAmount, "unionpay mobile pay Reversal.", array());

		$orderManager = new OrderManager();
		//设回原来的未支付状态。
		$orderManager -> setOrderStatus($orderId, 0);

		return $rsp;
	}

	public function refund($amount, $origAccountNumber1, $origSubmitTime, $origTransactionNumber, $settleDate) {
		$strAmount = sprintf("%012d", $amount);
		$strOrigOrderAmount = sprintf("%012d", $origAmount);
		$strOrigSubmitTime = date('YmdHis', $origSubmitTime);

		$orderId = uniqid();
		// @formatter:off
		$sNow = date( 'YmdHis' );
		$content = '<?xml version="1.0" encoding="UTF-8"?>' .
			'<cupMobile application="UPNoCards" version="1.01">' .
			'<transaction type="Refund.MPReq">' .
			'<submitTime>'.$sNow.'</submitTime>' .
			'<order id="'.$orderId.'">' .
				'<generateTime>'.$sNow.'</generateTime>' .
			'</order>' .
			'<transAmount currency="156">'.$strAmount.'</transAmount>' .
			'<terminal id="' . self::$TERMINAL_ID . '"/>' .
			'<merchant id="' . self::$MERCHANT_ID . '" name="'.self::$MERCHANT_NAME.'"/>' .
			'<accountNumber1>'.$origAccountNumber1.'</accountNumber1>' .
			'<origSubmitTime>'.$strOrigSubmitTime.'</origSubmitTime>' .
			'<origTransSerNo>'.$origTransactionNumber.'</origTransSerNo>' .
			'<settleDate>'.$settleDate.'</settleDate>' .
			'</transaction>' .
			'</cupMobile>';
		//@formatter:on

		$this -> httpsPost($content);
	}

	public function httpsPost($content) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://202.96.255.146:8443/easypay/merchant');

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		// 客户端证书
		curl_setopt($ch, CURLOPT_SSLCERT, ROOT_PATH . '../UPPay/edoauto.pem');
		curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');

		// 验证服务器证书
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
		curl_setopt($ch, CURLOPT_CAINFO, ROOT_PATH . '../UPPay/trust.crt');

		//数据
		curl_setopt($ch, CURLOPT_POSTFIELDS, $content);

		$result = curl_exec($ch);
		echo $result;
		curl_close($ch);

	}

	public function getUPPayOrderInfo($orderTime, $orderId, $orderAmount) {
		$strOrderAmount = sprintf("%012d", $orderAmount);
		// @formatter:off
		$orderInfoFmt = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>"
		. "<cupMobile version=\"1.01\" application=\"UPNoCard\">"
		. "<transaction type=\"Purchase.MARsp\">"
		. "<submitTime>%s</submitTime>"
		. "<order id=\"%s\">EduoAuto</order>"
		. "<transAmount currency=\"156\">%s</transAmount>"
		. "<terminal id=\""
		. self::$TERMINAL_ID
		. "\"/>"
		. "<merchant name=\""
		. self::$MERCHANT_NAME
		. "\" id=\""
		. self::$MERCHANT_ID
		. "\"/></transaction><senderSignature>%s</senderSignature></cupMobile>";

		$orderTime = date( 'YmdHis' );
		$sign = $this->getOrderSign( $orderTime , $orderId , $strOrderAmount );

		$orderInfo = sprintf( $orderInfoFmt , $orderTime , $orderId , $strOrderAmount , $sign );
		//echo $orderInfo;exit;
		return $orderInfo;
	}

	public function getOrderSign($orderTime, $orderId, $orderAmount) {
		$data = $orderTime . $orderId . $orderAmount . "156" . self::$TERMINAL_ID . self::$MERCHANT_ID .
		self::$MERCHANT_NAME;
		//echo $data;exit;
		//$data = "201201091346271234773300000003023115601042900303290047228001test";
		$fp = fopen(ROOT_PATH . self::$UNIONPAY_KEY_PATH, "r");
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

	protected function freezeCancel($transaction_number){

	}
	/**
	 * @param $addon array 其他数据
	 */
	protected function freeze($accountID,&$transaction_number,$money,$addon){

	}
	protected function freezeComplete($transaction_number,$money){

	}

	protected function withdraw(){

	}
	protected function charge($accountID,&$transaction_number,$money,$addon){

	}
	protected function consume($accountID,$money,$addon){

	}
}
?>