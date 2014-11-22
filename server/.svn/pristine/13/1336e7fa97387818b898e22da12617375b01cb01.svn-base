<?php
	if ($argc != 3) {
		die("Usage: index.php <url> <port>");
	}
	
	// remove first argument
	array_shift($argv);

	$url = $argv[0];
	$port= $argv[1];
	
	$Socket = @socket_create(AF_INET,SOCK_DGRAM,0);

		if( ! is_resource( $Socket ) ){
			return "system error: can't create socket.";
		}

		if( ! socket_bind( $Socket , 0 , $port ) ){
			return @socket_strerror( socket_last_error( $this->mSocket ) );
		}
		
		$recvStr = "";
		$a = "";
		$p = 0;
		while (true)
		{
			$recvLen = socket_recvfrom( $Socket , $recvStr , 1024, 0 , $a , $p );
			echo $recvStr.'\r';
		}
?>