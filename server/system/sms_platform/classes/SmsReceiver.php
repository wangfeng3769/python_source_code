<?php

class SmsReceiver {

	public function SmsReceiver() {
	}

	public function receive($argv) {
		$spId = $argv['spid'];
		$serviceCode = $argv['spsc'];
		$msgId = $argv['momsgid'];
		$sender = $argv['sa'];
		$receiver = $argv['da'];
		$esmClass = $argv['ec'];
		$productId = $argv['pid'];
		$msg = pack("H*", $argv['sm']);
		//传过来的是16进制字符串表示

		$dc = $argv['dc'];
		$canParsed = true;
		switch ( $dc ) {
			case 0 :
				break;
			case 8 :
				$msg = iconv("UCS-2BE", "utf-8", $msg);
				break;
			case 15 :
				$msg = iconv("GBK", "utf-8", $msg);
				break;
			default :
				$msg .= sprintf("%02x", $dc) . $argv['sm'];
				//如果是不认识的编码，把编码信息转成16进制放在消息的最前面一起保存
				$canParsed = false;
				break;
		}

		echo "command=MO_RESPONSE&spid=$spId&momsgid=$msgId&mostat=ACCEPT&moerrcode=000";
		//flush();

		$time = date('Y-m-d H:i:s');
		//@formatter:off 
		$sql = "INSERT INTO edo_cp_mo_log ( sender , msg , time , service_code , 	msg_id , receiver , 
			esm_class , product_id ) VALUES 
			( '$sender' , '$msg' , '$time' , '$serviceCode' , '$msgId' , '$receiver' , '$esmClass' , '$productId' )";
		$db = Frm::getInstance()->getDb();
		$db ->execSql($sql);
		//@formatter:on

		if ($canParsed) {
			require_once( Frm::$ROOT_PATH . 'system/sms_platform/classes/SmsMsg.php');
			$m = new SmsMsg();
			$m -> content = $msg;
			$m -> receiver = $receiver;
			$m -> sender = $sender;
			$m -> time = $time;

			require_once (Frm::$ROOT_PATH . 'system/sms_platform/conf/listener.php');
			foreach ($listener as $val) {
				require_once( $val['classPath'] );
				$l = new $val['className'];
				$l -> onSmsReceived($m);
			}
		}
	}

}
?>