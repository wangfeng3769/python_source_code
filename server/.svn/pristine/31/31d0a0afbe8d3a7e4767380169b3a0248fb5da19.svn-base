<?php
set_time_limit(0);

//define( DONOT_ADD_SLASH , true );
//define('IN_ECS', true);
//require_once (dirname(__FILE__).'/../../cp/includes/init.php');

require_once ( ROOT_PATH . "/include/Utile.php");
require_once ( ROOT_PATH . "/client/CarAgent.php");
//add by yangbei 2013-04-12 增加Logger.php文件
require_once (ROOT_PATH . "/include/Logger.php");
//add by yangbei 2013-04-12
class UdpServer{

	protected $RECV_MAX_LEN = 1024;
	protected $SERVER_PORT = 6001;

	protected $CMD_TRANS = "\0";//转发命令
	protected $CMD_EXIT = "\1";//退出命令

	protected $mSocket = null;

	/**
	 * 启动UdpServer，接收车载设备的消息上报。收到消息后，不在本类处理，用http post方式发给web服务器处理。
	 *
	 * @param string $rootUrl client目录在整个URL中的前缀，比如http://www.edoauto.com/edo/client,那么该参数为/edo，如果直接是/，也要传/
	 * @param unknown_type $webPort web服务器的端口
	 * @return unknown
	 */
	public function start( $rootUrl , $webPort ){
		if( empty( $rootUrl ) ){//如果要目录直接是"/"，也必须输入
			return;
		}

		echo 'Starting UDP server.';
		$ret = $this->init();
		if( null != $ret ){
			return $ret;
		}

		while( true ){
			$recvStr = "";
			$a = "";
			$p = 0;
					$recvLen = socket_recvfrom( $this->mSocket , $recvStr , $this->RECV_MAX_LEN , 0 , $a , $p );
			
			//车载设备的gprs模块不能接收服务器以外的端口发送的数据，所以，这儿要转发一下。
			if( $this->CMD_TRANS == $recvStr[0] ){
				$apos = strpos( $recvStr , "\0" , 1 );
				$ip = substr( $recvStr , 1 , $apos - 1 );
				
				$ppos = strpos( $recvStr , "\0" , $apos + 1 );
				$port = (int)substr( $recvStr , $apos + 1 , $ppos - $apos - 1 );
				
				$tmsg = substr( $recvStr , $ppos + 1 );
				$tmsglen = strlen( $tmsg );
				socket_sendto( $this->mSocket , $tmsg , $tmsglen , 0 , $ip , $port );

				continue;
			}else if( $this->CMD_EXIT == $recvStr[0] ){
				break;
			}

			$strJs = substr( $recvStr , 0 , $recvLen - 5 );
			if( $recvStr[$recvLen - 1] == "\4" ){
				$c = Utile::crc16( $strJs );
				$s = sprintf( "%02x" , $c % 256 ) . sprintf( "%02x" , $c / 256 );
				if( substr( $recvStr , $recvLen - 5 , 4 ) != $s ){
					//记录日志
					continue;
				}
			}

			$this->sendToProcessor( $strJs , $rootUrl , $webPort , $a , $p );
		}

		return "Successful Exit.";
	}

	public function stop(){
		$s = socket_create( AF_INET , SOCK_DGRAM , 0 ) ;
		socket_sendto( $s , $this->CMD_EXIT , strlen($this->CMD_EXIT) , 0 , "127.0.0.1" , $this->SERVER_PORT );
	}

	/**
	 * 客户端只能接收this->SERVER_PORT端口下发的数据，所以要转发一下。
	 *
	 * @param string $msg
	 * @return boolean
	 */
	public function transMsg( $msg , $clientIp , $clientPort ){
		$content = $this->CMD_TRANS . $clientIp . "\0" . $clientPort . "\0" . $msg;

		$s = socket_create( AF_INET , SOCK_DGRAM , 0 ) ;
		$len = strlen( $content );
		return socket_sendto( $s , $content , $len , 0 , "127.0.0.1" , $this->SERVER_PORT ) == $len;
	}

	public function ack( $carId , $packetNumber , $clientIp , $clientPort , $addOn = "" ){
		$ntime = time() - strtotime( "2009-01-01 00:00:00" );
		$ltime = unpack( "N" , pack( "V" , $ntime ) );
		$xtime = sprintf( "%08X" , $ltime[1] );

		$str = "";
		if( "" == $addOn ){
			$str = '{"pt":"ctrl","spt":"pkack","cid":'.$carId.',"pn":'.$packetNumber.',"ntime":"'.$xtime.'"}';
		}else{
			$str = '[{"pt":"ctrl","spt":"pkack","cid":'.$carId.',"pn":'.$packetNumber.',"ntime":"'.$xtime.'"},'.$addOn.']';
		}
		//add by yangbei 2013-04-12 增加下发指令日志
		Logger('out '.$str, ROOT_PATH . CAR_FOLDER . "/" . $carId . "/" . date('Y-m-d') . ".log", true);
		//add by yangbei 2013-04-12
		$nc = Utile::crc16( $str );
		$lc = unpack( "n" , pack( "v" , $nc ) );
		$xc = sprintf( "%04X" , $lc[1] );

		$str .= $xc . "\4";

		return $this->transMsg( $str , $clientIp , $clientPort );
	}

	protected  function init(){
		$this->mSocket = socket_create( AF_INET , SOCK_DGRAM , 0 ) ;
		if( ! is_resource( $this->mSocket ) ){
			return "system error: can't create socket.";
		}

		if( ! socket_bind( $this->mSocket , 0 , $this->SERVER_PORT ) ){
			return @socket_strerror( socket_last_error( $this->mSocket ) );
		}

		return null;
	}

	protected function sendToProcessor( $strJs , $rootUrl , $webPort , $clientIp , $clientPort ){
		$strJs = urlencode( $strJs );

		$ret = false;
		$errno = 0;
		$errstr = "";
		$fp = fsockopen( '127.0.0.1' , $webPort , $errno , $errstr , 5 );
		if( $fp ){
			$content = "msg=$strJs";
			$contentLen = strlen( $content );

			$str = "POST {$rootUrl}client/DataAnalysis.php?action=CDataAnalysis&method=Analysis&client_ip=$clientIp&client_port=$clientPort HTTP/1.1\r\n";
			$str .= "Host: 127.0.0.1\r\n";
			$str .= "Content-Length: $contentLen\r\n";
			$str .= "Cookie: debug_port=10000; debug_host=127.0.0.1; debug_start_session=1; start_debug=1; debug_stop=1; send_sess_end=1; debug_jit=1;\r\n";
			$str .= "Content-Type: application/x-www-form-urlencoded\r\n\r\n";
			$str .= $content;//echo $str;exit;
			fwrite( $fp , $str );
			fclose( $fp );
			
			print $str."\n";

		}else{
			//应该不会吧，这儿都出错了，记录日志。
		}
		
		return $ret;
	}
}

?>