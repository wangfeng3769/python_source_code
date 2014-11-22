<?php
set_time_limit(0);
define('IN_ECS', true);
if (!defined('ROOT_PATH')) {
	require_once (dirname(__FILE__) . '/../../cp/includes/init.php');
}

require_once (ROOT_PATH . "/../moneyAccountManager/AccountManager.php");

class CreditAccountManager extends AccountManager {

	/**
	 * @var cls_mysql
	 */
	private $mDbAgent = null;

	public function CreditAccountManager() {
		$this -> account_type = 'CashAccountManager';

		global $db;
		$this -> mDbAgent = $db;
	}

	public function createKeyPair() {
		$res = openssl_pkey_new(array('private_key_bits' => 1024, 'private_key_type' => OPENSSL_KEYTYPE_RSA, ));

		//openssl_pkey_export_to_file( $privateKey , "c:\\private_key.pem" );
		openssl_pkey_export($res, $privateKey);
		//print_r($privateKey);exit;

		$publicKey = openssl_pkey_get_details($res);
		$fp = fopen(ROOT_PATH . "data/public.key", "wb");
		fwrite($fp, $publicKey['key']);
		fclose($fp);

		self::download("private.key");

		echo $privateKey;
	}

	public function encrypt($source) {
		$maxlength = 117;
		$pubkey['bit'] = 1024;
		$pubkey['key'] = file_get_contents(ROOT_PATH . "data/public.key");

		$encryptedData = "";
		while ($source) {
			$input = substr($source, 0, $maxlength);
			$source = substr($source, $maxlength);
			$ok = openssl_public_encrypt($input, $encrypted, $pubkey['key']);

			$encryptedData .= $encrypted;
		}

		return $encryptedData;
	}

	public function saveToServer($id, $argv) {
		$cardNumber = $argv['credit_card_id'];
		$cvv2 = $argv['credit_card_CVN2'];
		$bankCode = $argv['credit_card_BANK'];
		$validYear = (int)($argv['credit_card_year']);
		$validMonth = (int)($argv['credit_card_month']);

		$cardData = "$cardNumber\0$cvv2\0" . pack("C", $validYear) . pack("C", $validMonth);
		$enCardData = $this -> encrypt($cardData);
		//echo $enCardData;exit;

		$ret = 0;
		$fp = fsockopen("192.168.1.63", 6000);
		if (!is_resource($fp)) {
			$ret = -1;
		}

		$reqData = pack("L", $id) . $enCardData;
		$len = strlen($reqData);
		$reqData = "eduo\1" . pack("L", $len) . $reqData;
		if ($len != fwrite($fp, $reqData, $len + 9)) {//9代表消息头的长度
			$ret = -2;
		}

		$str = fread($fp, 8);
		//返回8个字节
		$retArr = unpack("LerrLlen", $str);
		if (0 == $retArr['err']) {
			$ret = -3;
		}

		return $ret;
	}

	protected function freezeCancel($transaction_number) {

	}

	protected function packString($str, $len) {
		$bin = "";

		$strlen = strlen($str);
		if ($strlen <= $len) {
			$bin .= $str;
			$bin = str_pad($bin, $len, "\0", STR_PAD_RIGHT);
		} else {
			return substr($str, 0, $len);
		}

		return $bin;
	}

	protected function freeze($accountID, $money, $addon) {
		$IDNumber = $addon['IDNumber'];
		$phone = $addon['phone'];
		$trueName = $addon['trueName'];
		$orderId = $addon['orderId'];
		$userId = $addon['userId'];

		//取得信用卡记录ID
		global $ecs, $db;
		$sql = "SELECT id FROM " . $ecs -> table("credit_card") . " WHERE user_id = $userId";
		$id = $db -> getOne($sql);
		if (empty($id)) {
			throw new Exception('', 4054);
		}

		$reqData = pack("L", $id) . $this -> packString($IDNumber, 32) . $this -> packString($phone, 16) . $this -> packString($trueName, 16) . $this -> packString($orderId, 32) . pack("L", $money);

		$ret = 0;
		$fp = fsockopen("192.168.1.63", 6000);
		if (!is_resource($fp)) {
			$ret = -1;
		}

		$len = strlen($reqData);
		$reqData = "eduo\3" . pack("L", $len) . $reqData;
		//3代表支付
		$allLen = strlen($reqData);
		if ($allLen != fwrite($fp, $reqData, $allLen)) {
			$ret = -2;
		}

		$str = fread($fp, 8);
		//返回8个字节
		$retArr = unpack("LerrLlen", $str);
		if (0 == $retArr['err']) {
			$ret = -3;
		}
	}

	protected function freezeComplete($transaction_number, $money) {

	}

	protected function balanceInquiry() {

	}

	protected function withdraw() {

	}

	protected function charge() {

	}

	protected function consume($accountID, $money, $addon) {

	}

	public static function download($fileName) {
		header("Content-Type:application/octet-stream");
		header("Content-Disposition: attachment; filename=$fileName");
		header("Content-Transfer-Encoding: binary");
		header("Cache-Control: cache, must-revalidate");
		header("Pragma: public");
		header("Expires: 1");
	}

}
?>