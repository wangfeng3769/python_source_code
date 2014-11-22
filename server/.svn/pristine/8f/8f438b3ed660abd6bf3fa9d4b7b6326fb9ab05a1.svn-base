<?php
require_once 'SmsCaptcha.php';
class ModifyPhone{
	
	
	public function getCaptcha( $argv ){
		$strPhone = $argv['phone'];
		$phone=explode(",",$strPhone);
		$phone1 = $phone[0];
		$phone2 = $phone[1];
		$smsCaptcha = new SmsCaptcha();
		$cc1 = $smsCaptcha->get( array( 'phone'=>$phone1 ) );
		$_SESSION['modify_phone'][$phone1] = $cc1;
		
		$cc2 = $smsCaptcha->get( array( 'phone'=>$phone2 ) );
		$_SESSION['modify_phone'][$phone2] = $cc2;
	}
	
	public function check( $str ){
		$ret1 = $_SESSION['modify_phone'][$phone1] == $str[0];
		$_SESSION['modify_phone'][$phone1] = '';
		$ret2 = $_SESSION['modify_phone'][$phone1] == $str[1];
		$_SESSION['modify_phone'][$phone2] = '';
		$ret=array($ret1,$ret2);
		return $ret;
	}
}