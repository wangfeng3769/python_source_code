<?php
set_time_limit(0);

define('IN_ECS', true);
require_once (dirname(__FILE__).'/../../cp/includes/init.php');

class SmsReceiver{

	/**
	 *
	 * @var cls_mysql
	 */
	protected $mDb = null;

	public function SmsReceiver(){
		global $db;
		$this->mDb = $db;
	}

	public function receive( $argv ){
		$spId = $argv['spid'];
		$serviceCode = $argv['spsc'];
		$msgId = $argv['momsgid'];
		$sender = $argv['sa'];
		$receiver = $argv['da'];
		$esmClass = $argv['ec'];
		$productId = $argv['pid'];
		$msg = pack( "H*" , $argv['sm'] );//传过来的是16进制字符串表示

		$dc = $argv['dc'];
		$canParsed = true;
		switch ( $dc ){
			case 0:
				break;
			case 8:
				$msg = iconv( "UCS-2BE" , "utf-8" , $msg );
				break;
			case 15:
				$msg = iconv( "GBK" , "utf-8" , $msg );
				break;
			default:
				$msg .= sprintf( "%02x" , $dc ) . $argv['sm'];//如果是不认识的编码，把编码信息转成16进制放在消息的最前面一起保存
				$canParsed = false;
				break;
		}
		
		if( $canParsed ){
			if( 0 == strncasecmp( "zc" ,  $msg , 2 ) ){
				require_once (ROOT_PATH."../client/classes/UserManager.php");
				$um = new UserManager();
				$um->smsRegister( $sender );
			}
		}

		global $ecs;
		$time = date( 'Y-m-d H:i:s' );
		$sql = "INSERT INTO " . $ecs->table( "mo_log" ) . "( sender , msg , time , service_code , 	msg_id , receiver , esm_class , product_id ) VALUES ( '$sender' , '$msg' , '$time' , '$serviceCode' , '$msgId' , '$receiver' , '$esmClass' , '$productId' )";
		$this->mDb->query( $sql );

		echo "command=MO_RESPONSE&spid=$spId&momsgid=$msgId&mostat=ACCEPT&moerrcode=000";
	}
}

?>