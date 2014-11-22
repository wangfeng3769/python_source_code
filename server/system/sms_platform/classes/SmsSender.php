<?php

class SmsSender {

	public static $STATUS_SEND_SUC = 1;
	public static $STATUS_SEND_FAILED = 2;
	public static $STATUS_RECEIVE_SUC = 3;
	public static $STATUS_RECEIVE_FAILED = 4;

	public function SmsSender() {
	}

	public function send($content, $receiver) {
		$conf = Frm::getInstance()->getConf();
		$node = $conf->getNode( 'sms_platform' );
		$smsApiUrl = $node->getValue( "url" );
		$userId = $node->getValue( 'user_id');
		$password = $node->getValue( 'password' );

		$uc = urlencode($content);
		$mc = substr_count($receiver, ',') + 1;
		$url = $smsApiUrl . '?userId=' . $userId . '&password=' . $password . "&pszMobis=$receiver&pszMsg=$uc&iMobiCount=$mc&pszSubPort=0";

		$s = file_get_contents($url);
		//echo $s;exit;
		$start = strpos($s, "\">") + 2;
		$end = strpos($s, "<", $start);

		$status = self::$STATUS_SEND_FAILED;

		$msgId = substr($s, $start, $end - $start);
		$msgIdLen = strlen($msgId);
		if ($msgIdLen > 10 && $msgIdLen < 25) {
			$status = self::$STATUS_SEND_SUC;
		}

		$st = date('Y-m-d H:i:s');
		$sql = "INSERT INTO edo_cp_mt_log ( receiver , time , msg , msg_id , status ) VALUES ( '$receiver' , '$st' , '$content' , '$msgId' , $status )";
		$db = Frm::getInstance()->getDb();
		$db -> execSql($sql);

		return self::$STATUS_SEND_SUC == $status;
	}

}
?>