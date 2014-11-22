<?php
class PublicClient {

	public function __construct() {

	}
	
	public function monitorLoginPage(){
		require_once( Frm::$ROOT_PATH . 'cp/admin/templates/monitor_login.php');
		exit;
	}
	
	public function monitorLogin($argv){
		require_once (Frm::$ROOT_PATH . 'user/classes/UserManager.php');
		$um = new UserManager();
		$loginRet = $um -> login($argv['user_name'], $argv['password'], '');
		
		header( 'Location: /client/http/monitor.php?item=carList' );
	}
	//add by yangbei  增加跳转函数 start
	public function carRecordLoginPage(){
		require_once( Frm::$ROOT_PATH . 'cp/admin/templates/carRecord_login.php');
		exit;
	}
	public function carRecordLogin($argv){
		require_once (Frm::$ROOT_PATH . 'user/classes/UserManager.php');
		$um = new UserManager();
		$loginRet = $um -> login($argv['user_name'], $argv['password'], '');
		header( 'Location: /client/http/carRecord.php?item=carRecordList');
		
	}
	//add by yangbei  增加跳转函数 end

    //add by matao  增加跳转函数 start
    public function dataImportLoginPage()
	{
		header('Location: /admin/data_import_login.php');
		exit;
	}
	public function dataImportLogin($argv)
	{
		require_once (Frm::$ROOT_PATH . 'user/classes/UserManager.php');
		$um = new UserManager();
		$loginRet = $um -> login($argv['user_name'], $argv['password'], '');
		header( 'Location: /admin/data_import.php' );
		exit;	
	}
    //add by matao  增加跳转函数 end

	public function login($argv) {
		$userName = trim($argv['user_name']);
		$password = trim($argv['password']);
		$accessToken = trim($argv['access_token']);

		require_once (Frm::$ROOT_PATH . 'user/classes/UserManager.php');
		$um = new UserManager();
		$loginRet = $um -> login($userName, $password, $accessToken);
		$userId = UserManager::getLoginUserId();

		require_once (Frm::$ROOT_PATH . 'user/classes/IdentificationManager.php');
		$im = new IdentificationManager();
		$passApprove = $im -> hasUploadApproveData($userId);

		$notice['type'] = 0;
		if (!$passApprove) {
			$notice['type'] |= 0x01;
			$notice['content'] = '您未上传实名认证信息。';
		}

		$loginRet['notice'] = $notice;
		return $loginRet;
	}

	public function getSmsCaptcha($argv) {
		$phoneNum = $argv['phone'];
		if (!is_numeric($phoneNum)) {
			throw new Exception('', 4000);
		}

		$usage = $argv['usage'];
		switch ( $usage ) {
			case 'register' :
				require_once (Frm::$ROOT_PATH . 'user/classes/UserManager.php');
				$um = new UserManager();
				if ($um -> hasRegister($phoneNum)) {
					throw new Exception('', 4002);
				}

				break;
			case 'getBackPassword' :
				require_once (Frm::$ROOT_PATH . 'user/classes/UserManager.php');
				$um = new UserManager();
				if (!$um -> hasRegister($phoneNum)) {
					throw new Exception('', 4224);
				}

				break;
			case 'bindLocal' :
				session_start();
				break;
			default :
				throw new Exception('', 4000);

				break;
		}

		require_once (Frm::$ROOT_PATH . 'system/classes/SysConfig.php');
		$captchaFmt = SysConfig::getValue('sms_captcha_fmt');

		require_once (Frm::$ROOT_PATH . 'system/classes/SmsCaptcha.php');
		$smsCaptcha = new SmsCaptcha($phoneNum);
		if (!$smsCaptcha -> get($captchaFmt, $phoneNum)) {
			throw new Exception('send sms error.', 5000);
		}
	}
	
	/**
	 * $Author: 邬国轩
	 * $time:	2013-04-25 23:35:46Z
	 * $modify:	天物系统,获取天物集团的车辆
	 */
	/****/
	public function twGetCarsInfo($argv)
	{


		require_once (Frm::$ROOT_PATH . 'resource/classes/CarManager.php');
		$carManager = new CarManager();

		$carIdArr=$carManager->twGetCarsIdByTitle('天物');

		$carArr = $carManager -> getCarList($carIdArr);


		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
		$orderManager = new OrderManager();
		$ret = array();
		$now = time();
		foreach ($carArr as $car) {
			$orderTimeArr = $orderManager -> getOrderTimeInDay($car -> id, $now);
			//@formatter:off
			$row = array('id' => $car -> id, 'status' => $car -> status, 'gearbox' => $car -> gearbox, 'name' => $car -> name, 'number' => $car -> number, 'brand' => $car -> brand, 'model' => $car -> model, 'icon' => $car -> icon, 'station' => $car -> station, 'timePriceMin' => $rangArr[$car -> id]['timePriceMin'], 'timePriceMax' => $rangArr[$car -> id]['timePriceMax'], 'milePrice' => $rangArr[$car -> id]['milePrice'], 'orderTime' => $orderTimeArr);
			//@formatter:on
			$ret[] = $row;
		}
		return $ret;
	}
}
