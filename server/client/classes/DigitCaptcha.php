<?php
class DigitCaptcha{
	
	protected $SESSION_TAG = 'SESS_CAPTCHA';
	
	public function DigitCaptcha(){
	}
	
	public function get(){
		$arr = array( '0' , '1' , '2' , '3' , '4' , '5' , '6' , '7' , '8' , '9' );

        mt_srand( (double) microtime() * 1000000 );
        shuffle( $arr );

        $code = substr(implode('', $arr), 0, 5);
        
        $_SESSION[$this->SESSION_TAG] = $code;
        
        return $code;
	}
	
	public function check( $str ){
		$ret = $_SESSION[$this->SESSION_TAG] == $str;
		$_SESSION[$this->SESSION_TAG] = '';
		
		return $ret;
	}
}
?>