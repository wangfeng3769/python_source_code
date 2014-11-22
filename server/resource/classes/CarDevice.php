<?php
class CarDevice {

	//idle空闲状态，等待刷车窗卡开门（会员卡）
	static public $SYS_STATE_IDLE = 1;
	//刷会员卡正确后，等待输入用车密码状态
	static public $SYS_STATE_WAIT_FOR_PASSWORD = 2;
	//没有刷卡锁门状态
	static public $SYS_STATE_DONT_CLOSE_DOOR = 7;
	
	static public $CTS_KEY = 0x01;

	public $id;
	public $status;
	public $sysState;
	public $carId;
	public $longitude;
	public $latitude;
	public $cts;
	public $vss_counter;
	public $software_version;

}
?>