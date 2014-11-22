<?php
set_time_limit(0);
define('IN_ECS', true);
if (!defined('ROOT_PATH')) {
	require_once (dirname(__FILE__) . '/../../cp/includes/init.php');
}

include_once (ROOT_PATH . "../client/classes/LoginManager.php");
include_once (ROOT_PATH . "../client/classes/SmsCaptcha.php");

require_once (ROOT_PATH . "../client/classes/CarManager.php");

include_once (dirname(__FILE__) . '/../../cp/includes/cls_json.php');
include_once (dirname(__FILE__) . '/../../cp/admin/includes/lib_main.php');

define("SESSION_LOG_ID", 1);
define("SESSION_USER_ID", 'uid');
define("SESSION_PHONE", 'phone');
define("SESSION_USER_NAME", 'uname');
define("SESSION_LAST_MAC", 3);
define("SESSION_ALIAS", 4);
define("SESSION_ACCESS_TOKEN", 'token');

define("ERR_OK", 0);
define("ERR_USER_EXISTS", 2);
define("ERR_CAPTCHA", 3);
define("ERR_DB", -1);

define("SQL_USER_PARAM", " u.user_id , u.user_name , u.sex , u.alias , u.signature , u.status , u.type , birthday , address_id ");

class UserManager {

	/**
	 * @var cls_mysql
	 */
	private $mDbAgent = null;

	public function UserManager() {
		global $db;
		$this -> mDbAgent = $db;
	}

	public function login($argv) {
		// print_r($_SESSION);
		if (!self::isLogined()) {
			$loginInfo = array();

			$user = str_replace("\'", "\'\'", $argv['user_name']);
			$password = str_replace("\'", "\'\'", $argv['password']);
			$token = str_replace("\'", "\'\'", $argv['access_token']);
			$md5p = md5($password);

			if (empty($argv['user_name'])) {
				if (!empty($argv['access_token'])) {
					//使用token登陆
					$loginInfo = LoginManager::loginWithToken($token);
				} else {
					throw new Exception('', 4000);
				}
			} else {
				$loginInfo = LoginManager::loginWithUserName($user, $md5p);
			}

			$this -> setLoginSession($loginInfo);
			return array("accessToken" => $loginInfo['token'],"uname"=>$_SESSION[SESSION_USER_NAME]);
		} else {
			return array("accessToken" => $_SESSION[SESSION_ACCESS_TOKEN],"uname"=>$_SESSION[SESSION_USER_NAME]);
		}
	}

	public static function setLoginSession($row) {
		$_SESSION[SESSION_USER_ID] = $row['uid'];
		$_SESSION[SESSION_PHONE] = $row['phone'];
		$_SESSION[SESSION_LAST_MAC] = $row['last_mac'];
		$_SESSION[SESSION_ALIAS] = $row['alias'];
		$_SESSION['user_id'] = $row['uid'];
		$_SESSION[SESSION_USER_NAME] = $row['true_name'];
		$_SESSION[SESSION_ACCESS_TOKEN] = $row['token'];
	}

	public static function getLoginUserId() {
		$userId = empty($_SESSION[SESSION_USER_ID]) ? -1 : $_SESSION[SESSION_USER_ID];

		return $userId;
	}

	public static function isLogined() {
		//两种情况，一是本地系统登录的，二是openplat登录的
		$userId = self::getLoginUserId();
		if ($userId > 0) {
			//增加临时过滤条件,防止未绑定手机时不能做任何操作
			$item = $_REQUEST['item'];
			$igArr = array('bindLocal','logout');

			if (!in_array($item, $igArr)) {
				self::checkIsBindingPhone();
			}
			
			return true;
		}

		$accessToken = $_SESSION[SESSION_ACCESS_TOKEN];
		return !empty($accessToken);
	}

	public static function checkIsBindingPhone() {
		global $db;
		global $sns;
		$userId = UserManager::getLoginUserId();

		$sql = " SELECT phone FROM " . $sns -> table('user') . " WHERE  uid=$userId ";
		$phone = $db -> getOne($sql);
		$_SESSION[SESSION_PHONE]=$phone;
		if (empty($phone)) {
			throw new Exception("", 4021);

		}
	}

	public function registerEmptyUser() {
		// $this -> register('', '');

		return $this -> register('', '');
	}

	public function register($phonenum, $password) {
		if (!empty($phonenum)) {
			$sql = "SELECT 1 FROM edo_user WHERE phone = '$phonenum'";
			if (count($this -> mDbAgent -> getAll($sql)) > 0) {
				throw new Exception("该手机号码已被使用", 4002);
			}
		}

		if (empty($password)) {
			$ep = "";
		} else {
			$ep = md5($password);
		}

		$sql = "SELECT version FROM user_protocal ";
		$userProtocalVersion = $this -> mDbAgent -> getOne($sql);

		$now = time();
		$sql = "INSERT INTO edo_user ( phone , password , ctime , protocal_version ) VALUES ( '$phonenum' , '$ep' , $now , $userProtocalVersion )";
		$ret = $this -> mDbAgent -> query($sql);
		if (!$ret) {
			throw new Exception("数据库错误", 5002);
			//return ERR_DB;
		}

		$userId = $this -> mDbAgent -> insert_id();

		//开一个现金账户
		$account = new CashAccountManager();
		$account -> create($userId);

		return $userId;
	}

	public function setnewpassword($phonenum, $password) {

		if (empty($password)) {
			$temp = "";
		} else {
			$temp = md5($password);
		}
		$sql = "UPDATE edo_user SET password = '$password' WHERE phone = '$phonenum'";
		$res = $this -> mDbAgent -> query($sql);
		if (!$res) {
			throw new Exception("数据库错误", 5002);
			return ERR_DB;
		}

		return ERR_OK;
	}

	static public function getSessionToken() {
		return $_SESSION[SESSION_ACCESS_TOKEN];
	}

	public function captchaRegister($argv) {
		$phonenum = trim($argv['phone']);
		$password = $argv['password'];
		$captcha = $argv['captcha'];

		$smsCaptcha = new SmsCaptcha();
		if (!$smsCaptcha -> check($captcha, $phonenum)) {
			throw new Exception("", 4003);
			return ERR_CAPTCHA;
		}

		$this -> checkRegister($phonenum, $password);

		$userId = $this -> register($phonenum, $password);
		if ($userId <= 0) {
			throw new Exception('', 5000);
		}

		$this -> recordLocation($userId, $argv['lng'], $argv['lat'], $argv['city']);

		return $this -> login(array('user_name' => $phonenum, 'password' => $password));
	}

	protected function recordLocation($userId, $lng, $lat, $city) {
		global $db;

		if (empty($lng) || empty($lat)) {
			return;
		}

		//region_type=2表示市
		$sql = "SELECT region_id FROM edo_cp_region WHERE region_name = '$city' AND region_type = 2";
		$cityId = $db -> getOne($sql);
		$sql = "INSERT INTO user_location ( user_id , relate_operation , longitude , latitude , provider , city_id )
			VALUES ( $userId , 'register' , $lng , $lat , 'net' , $cityId)";
		$db -> query($sql);
	}

	public function unlogin() {
		$_SESSION[SESSION_ACCESS_TOKEN] = '';
		$_SESSION[SESSION_PHONE] = '';
		$_SESSION[SESSION_USER_ID] = '';
	}

	public function captchaFindpassword($argv) {
		$phone = trim($argv['phone']);
		$password = $argv['password'];
		$captcha = $argv['captcha'];

		if (!$this -> hasRegister($phone)) {
			throw new Exception('', 4224);
		}

		//$this -> checkphone($phone);
		if (!$this -> checkphone_s($phone)) {
			throw new Exception("手机号码不符合规则", 4000);
		}

		$smsCaptcha = new SmsCaptcha();
		if (!$smsCaptcha -> check($captcha, $phone)) {
			throw new Exception("", 4003);
			return ERR_CAPTCHA;
		}

		$sToday = date('Y-m-d', time());
		$sql = "SELECT find_password_date , find_password_times FROM edo_user WHERE phone = '$phone'";
		$row = $this -> mDbAgent -> getRow($sql);
		if ($sToday == $row['find_password_date']) {
			if ($row['find_password_times'] >= 3) {
				throw new Exception('', 4216);
			}
		}

		//return $this -> setnewpassword($phone, $password);
		if (empty($password)) {
			$temp = "";
		} else {
			$temp = md5($password);
		}
		$sql = "UPDATE edo_user SET password = '$temp' , find_password_date = '$sToday' , find_password_times = find_password_times + 1 WHERE phone = '$phone'";
		$res = $this -> mDbAgent -> query($sql);
		if (!$res) {
			throw new Exception("数据库错误", 5002);
			return ERR_DB;
		}

		$this -> unlogin();
	}

	public function hasRegister($phoneNum) {
		$sql = "SELECT 1 FROM edo_user WHERE phone = '$phoneNum'";
		$db = $this -> mDbAgent;
		$one = $db -> getOne($sql);
		return !empty($one);
	}

	public function checkphone_l($str) {
		$sql = "SELECT 1 FROM edo_user WHERE phone = '$str'";
		return count($this -> mDbAgent -> getAll($sql));
	}

	public function checkphone_s($str2) {
		return $b = preg_match("/^1[3|4|5|8][0-9]\d{4,8}$/", $str2);
	}

	public function checkpassword($str) {
		return $a = preg_match("/^[0-9a-zA-Z]{6,30}$/i", $str);
	}

	public function checkRegister($phone, $pass) {

		$this -> checkphone($phone);
		if (!$this -> checkpassword($pass)) {
			throw new Exception("密码不符合要求（大小写英文加数字，6-30）", 4021);
		}
	}

	public function checkphone($phone) {
		if (!$this -> checkphone_s($phone)) {
			throw new Exception("手机号码不符合规则", 4000);
		}

		if ($this -> checkphone_l($phone)) {
			throw new Exception("该手机号码已被使用", 4002);
		}
	}

	public function bindLocal($argv) {
		$phonenum = trim($argv['phone']);
		$captcha = $argv['captcha'];

		$smsCaptcha = new SmsCaptcha();
		if (!$smsCaptcha -> check($captcha,$phonenum)) {
			throw new Exception("验证码不正确或已过期，请重新获取", 4016);
			return ERR_CAPTCHA;
		}

		global $sns;
		$userId = self::getLoginUserId();
		$sql = "UPDATE " . $sns -> table("user") . " SET phone = $phonenum WHERE uid = $userId";
		$this -> mDbAgent -> query($sql);

		//设置手机号码session
		$_SESSION[SESSION_PHONE]=$phonenum;
		return ERR_OK;
	}

	public function smsRegister($sender) {
		$password = sprintf("%06d", rand(1, 999999));

		require_once (ROOT_PATH . "../client/SmsPlatform/SmsSender.php");
		$s = new SmsSender();

		try {
			$ret = $this -> register($sender, $password);
		} catch(Exception $e) {
			$s -> send($e -> getMessage(), $sender);
			return;
		}

		global $REGISTER_PASSWORD;
		$msg = sprintf($REGISTER_PASSWORD, $password);
		$s -> send($msg, $sender);
	}

	public function modifyPassword($argv) {
		$this -> checkpassword($argv['new']);
		$old = md5($argv['old']);
		$new = md5($argv['new']);

		$userId = self::getLoginUserId();

		global $sns;
		$sql = "SELECT 1 FROM " . $sns -> table("user") . " WHERE uid = $userId AND password = '$old' ";
		if (1 != count($this -> mDbAgent -> getAll($sql))) {
			throw new Exception("", 4004);
		}

		$sql = "UPDATE " . $sns -> table("user") . " SET password = '$new' , token = '' , need_modify_password = 0 WHERE uid = $userId ";
		return $this -> mDbAgent -> query($sql);

		$this -> setLoginSession(array());
	}

	public function getUser($userId) {
		global $sns;

		$sql = "SELECT * FROM " . $sns -> table("user") . " WHERE uid = $userId";
		return $this -> mDbAgent -> getRow($sql);
	}

	public function saveVerificationImgs($argv) {
		global $ecs;
		$userId = self::getLoginUserId();

		$user = $this -> getUser($userId);
		if (empty($user['phone'])) {
			throw new Exception('', 4210);
		}

		$sql = "SELECT approve_stat FROM " . $ecs -> table("identity_approve") . "WHERE user_id = $userId LIMIT 1";
		$oldInfo = $this -> mDbAgent -> getRow($sql);
		if (3 == $oldInfo['approve_stat']) {
			throw new Exception('', 4032);
		}

		//@formatter:off		
		 if (0 == $_FILES['id']['error'] && $_FILES['id']['size'] > 0 && $this -> checkPictureinfo(($_FILES['id']))) {
			$idPath = "images/user_verification/" . uniqid() . $_FILES['id']['name'];
			move_uploaded_file($_FILES['id']['tmp_name'], ROOT_PATH . "/" . $idPath);
		}

		if (0 == $_FILES['others']['error'] && $_FILES['others']['size'] > 0 && $this -> checkPictureinfo($_FILES['others'])) {
			$othersPath = "images/user_verification/" . uniqid() . $_FILES['others']['name'];
			move_uploaded_file($_FILES['others']['tmp_name'], ROOT_PATH . "/" . $othersPath);
		}
		//@formatter:on

		if (0 == $_FILES['student']['error'] && $_FILES['student']['size'] > 0 && $this -> checkPictureinfo($_FILES['student'])) {
			$studentPath = "images/user_verification/" . uniqid() . $_FILES['student']['name'];
			move_uploaded_file($_FILES['student']['tmp_name'], ROOT_PATH . "/" . $studentPath);
		}

		$t = time();
		if (!empty($oldInfo)) {
			$sql = "UPDATE " . $ecs -> table("identity_approve") . " SET post_time = $t , img_identity_front = '$idPath' , img_others = '$othersPath',is_processed=0 WHERE user_id = $userId";
		} else {
			$sql = "INSERT INTO " . $ecs -> table("identity_approve") . " ( user_id , post_time , img_identity_front , img_others , img_student ) VALUES ( $userId , $t , '$idPath' , '$othersPath' , '$studentPath'  ) ";
		}

		return $this -> mDbAgent -> query($sql);
	}

	public function checkPictureinfo($filename) {
		$filenameinfo = substr(strtoupper($filename['name']), -3, 3);
		switch ($filenameinfo) {
			case GIF :
				$filenameinfoo = 1;
				break;
			case JPG :
				$filenameinfoo = 2;
				break;
			case PNG :
				$filenameinfoo = 3;
			case SWF :
				$filenameinfoo = 4;
			case PSD :
				$filenameinfoo = 5;
			case BMP :
				$filenameinfoo = 6;
				break;
			default :
				$filenameinfoo == false;
				break;
		}

		$imgInfo = getimagesize($filename['tmp_name']);
		$tmpArr = explode("\"", $imgInfo[3]);
		$width = $tmpArr[1];
		$height = $tmpArr[3];
		if ($width < 400 || $height < 400) {
			throw new Exception("上传的图片尺寸太小", 4031);
		}
		if ($width > 4000 || $height > 4000) {
			throw new Exception("上传的图片尺寸太大", 4030);
		}

		if (false == $filenameinfoo && $filenameinfoo != 2 && $filenameinfoo != 3) {
			throw new Exception("图片格式限制为JPG或PNG", 400);
		}
		foreach ($_FILES as $v) {
			if ($v['error']!=0) {
				throw new Exception("上传图片出错", 400);
			}
		}
		return true;
	}

	/**
	 * 审核信息is_processed
	 */
	public function checkIs_processed($argv) {
		global $ecs;
		$userId = $this -> getLoginUserId();
		$sql = "select is_processed,approve_stat from " . $sns -> table("identity_approve") . " where user_id=" . $user_id;
		$res = $this -> mDbAgent -> getRow($sql);

		if (isset($res['is_processed'])) {
			if ($res['is_processed'] == 1)//1审核中	0未审核  2已审核
			{
				return false;
			}
			if ($res['is_processed'] == 2 && $res['approve_stat'] == 3 || $res['approve_stat'] == 7) {
				return false;
				//查看已审核的资料状态
			}
		}

		return true;
		//可以继续上传实名认证图片
	}

	public function getOpenPlatInfo($argv) {
		global $db, $sns;

		$user_id = self::getLoginUserId();
		// $user_id = 0 ;
		$sql = "select login_from,status from " . $sns -> table("open_login") . " where user_id=" . $user_id;

		$medal = $db -> getAll($sql);

		//固定数组格式

		//echo "<script type='text/javascript' >alert(".$s.")</script>";
		return $medal;
		//make_json_result($medal);
	}

	public function untie($argv) {
		global $sns, $db;
		$sql = "update " . $sns -> table("open_login") . " set status=0 where user_id=" . self::getLoginUserId() . " and login_from=" . $argv['id'];
		//print_r($argv);
		if ($db -> query($sql)) {
			return array('id' => $argv['id']);
			// make_json_result(array('id'=>$argv['id']));
		}

	}

	public function getMybonus() {
		global $ecs, $db;
		$sql = " SELECT t.icon,t.send_type FROM " . $ecs -> table('user_bonus') . ' b ' . " LEFT JOIN " . $ecs -> table('bonus_type') . ' t ' . " ON t.type_id=b.bonus_type_id " . " WHERE user_id=" . $this -> getLoginUserId();

		$arr = $db -> getAll($sql);
		return $arr;
	}

	/**
	 * 分享到各微博平台,晒单
	 * @param  array $argv['content']=>[sayContent],[car_icon],[shareTo]=>([qq] => 1,[sina] => 1)
	 * @return [type]       [description]
	 */
	public function showOrder($argv) {
		require_once (ROOT_PATH . '/../openPlatForm/includes/OpenPlatformLogin.php');
		//echo $argv['content'];

		$content = $argv['content'];
		if (get_magic_quotes_gpc()) {//如果get_magic_quotes_gpc()是打开的
			//将字符串进行处理
			$content = stripslashes($content);
		}
		$content = json_decode($content, true);

		// print_r(json_decode('"{q:1,a:2}"',true));
		$l = new OpenPlatformLogin();
		$l -> showOrder($content);
	}

	/**
	 * 获取会员资料,不传id则获取登陆会员的资料
	 * @param  int $userID 会员id
	 * @return array        会员资料数组
	 */
	public function getUserInfo() {
		global $sns;

		if (empty($argv['user_id'])) {
			$userID = $this -> getLoginUserId();
		}

		// print_r($userID);
		$sql = " SELECT true_name , phone , 'data/avatar/avatar.jpg' AS avatar " . " FROM " . $sns -> table('user') . " WHERE uid=" . $userID;

		$ret = $this -> mDbAgent -> getRow($sql);

		return $ret;
	}

	/**
	 * 获取违章信息
	 * 不用GEt传参的话,默认使用登陆用户的user_id
	 * @return array 违章信息数组
	 */
	public function getViolateInfo($argv) {
		global $ecs;
		if (empty($argv['user_id'])) {
			$userID = $this -> getLoginUserId();
		}

		$sql = " SELECT co.order_no,cv.violate_time,cv.violate_location,cv.violate_type,cv.violate_cost,cv.violate_point,cv.agency_cost,c.number " . " FROM " . $ecs -> table('car_violate') . ' cv ' . " LEFT JOIN " . $ecs -> table('car_order') . ' co ' . " ON cv.order_id=co.order_id " . " LEFT JOIN " . $ecs -> table('car') . ' c ' . " ON c.id=co.car_id" . " WHERE co.user_id=" . $userID;
		// return $userID;
		$violateInfo = $this -> mDbAgent -> getAll($sql);

		foreach ($violateInfo as $k => $v) {
			$violateInfo[$k]['violate_time'] = date('Y-m-d H:i', $v['violate_time']);
			# code...
		}
		return $violateInfo;
	}

	/**
	 * 获取违章信息
	 * 不用GEt传参的话,默认使用登陆用户的user_id
	 * @return array 用户默认发票信息数组
	 */
	public function getInvoiceInfo() {
		global $sns;
		if (empty($argv['user_id'])) {
			$userID = $this -> getLoginUserId();
		}
		$sql = " SELECT u.uid,u.invoice_address,u.invoice_title,u.invoice_zip" . " FROM " . $sns -> table('user') . ' u ' . " WHERE u.uid=" . $userID;

		return $this -> mDbAgent -> getRow($sql);
	}

	/**
	 * 更新用户发票信息
	 * @return [type] [description]
	 */
	public function updateUserInvoiceInfo($argv) {
		global $sns;
		if (empty($argv['user_id'])) {
			$userID = $this -> getLoginUserId();
		}

		$data = array('invoice_title' => $argv['invoice_title'], 'invoice_address' => $argv['invoice_address']);

		$this -> mDbAgent -> autoExecute($sns -> table('user'), $data, 'update', "uid=" . $userID);

		return true;
	}

	/**
	 * 驾照是否过期
	 * @return [type] [description]
	 */
	function expiryDrivingLicence() {
		$expireTime = $this -> getDrivingLicenceExpire();

		if ($expireTime - 7 * 24 * 3600 < time()) {
			return true;
		}
		return false;
	}

	/**
	 * 是否已经上传了实名审核资料
	 * @return boolean [description]
	 */
	function hasUploadApproveData($userID = '') {
		global $ecs;
		if (empty($userID)) {
			$userID = $this -> getLoginUserId();
		}

		$sql = " SELECT COUNT(1) FROM " . $ecs -> table('identity_approve') . " WHERE user_id=" . $userID;

		return $this -> mDbAgent -> getOne($sql) > 0;
	}

	/**
	 * 获取审核状态
	 * @return [type] [description]
	 */
	function getApproveStat($userID = '') {
		global $ecs;
		if (empty($userID)) {
			$userID = $this -> getLoginUserId();
		}

		$sql = " SELECT approve_stat FROM " . $ecs -> table('identity_approve') . " WHERE user_id=" . $userID;

		$approve_stat = $this -> mDbAgent -> getOne($sql);

		return $approve_stat;
	}

	/**
	 * 是否通过审核
	 * @return boolean [description]
	 */
	function isPassApprove($userID = '') {
		if (empty($userID)) {
			$userID = $this -> getLoginUserId();
		}

		$approve_stat = $this -> getApproveStat($userID);

		$retStr = '';

		if ($approve_stat == 7 || $approve_stat == 3) {
			return true;
		}
		return false;
	}

	/**
	 * 验证审核状态
	 * @return [type] [description]
	 */
	function approveValidation() {

	}

	/**
	 * 获取用户所有违章信息
	 * @param  [type] $userID [description]
	 * @return [type]         [description]
	 */
	function getUserViolate($userID) {
		global $db;
		global $sns;
		global $ecs;
		$sql = " SELECT cv.process_stat FROM " . $ecs -> table('car_violate') . ' cv ' . " LEFT JOIN " . $ecs -> table('car_order') . ' co ' . " ON co.order_id=cv.order_id" . " LEFT JOIN " . $sns -> table('user') . ' u ' . " ON u.uid=co.user_id " . " WHERE (cv.process_stat=0 or cv.process_stat=1 or cv.process_stat=2 or cv.process_stat=3)" . " AND co.user_id=" . $userID;
		$arrViolateRet = $db -> getAll($sql);
		return $arrViolateRet;
	}

	/**
	 * 检查违章状态,并返回违章状态
	 * @param  array $arrViolateInfo 违章信息数组,可多条
	 * @return [type]                 [description]
	 */
	function isHandledViolate($arrViolateInfo) {
		if (!empty($arrViolateInfo)) {
			//有没未处理的违章
			$isHandledViolate = true;

			foreach ($arrViolateInfo as $key => $value) {
				if ($value['process_stat'] == 0 || $value['process_stat'] == 1) {
					$isHandledViolate = $isHandledViolate & false;
				}
			}
		} else {
			$isHandledViolate = true;
		}
		return $isHandledViolate;
	}

	function userIsViolated() {
		$userID = UserManager::getLoginUserId();
		$violateInfo = $this -> getUserViolate($userID);
		return !$this -> isHandledViolate($violateInfo);
	}

	/**
	 * 获取驾照过期时间
	 * @return [type] [description]
	 */
	function getDrivingLicenceExpire() {
		global $sns;
		$userID = $this -> getLoginUserId();

		$sql = " SELECT validfor FROM " . $sns -> table('user') . " WHERE uid=" . $userID;

		return $this -> mDbAgent -> getOne($sql);

	}

	/**
	 * 违章判断 验证
	 * @return [type] [description]
	 */
	function violateValidation() {
		if ($this -> userIsViolated()) {
			throw new Exception("您有违章未处理", 1);
		}

	}

	/**
	 * 验证驾照是否过期 验证
	 *
	 */
	function drivingLicenceValidation() {
		if ($this -> expiryDrivingLicence()) {
			throw new Exception("驾照有效期到期前七天，不允许订车", 4015);

		}
	}

	function identityApproveValidation() {
		if (!$this -> hasUploadApproveData() || !$this -> isPassApprove()) {
			throw new Exception("未通过实名认证的用户,不能订车", 1);
		}

	}

	public function GraphVerification($argv) {
		$str = $argv['graphic_code'];
		//throw new Exception("验证码不正确123或已过期，请重新获取".$str, 4016);exit;

		if ($this -> isOutRecentGraph($str) == 1) {
			return 1;
		} else {
			return -1;
		}
	}

	protected function isOutRecentGraph($str) {
		return 1;
		global $sns;
		$userId = self::getLoginUserId();
		$sql = "SELECT GraphVerification FROM " . $sns -> table("user") . " WHERE GraphVerification = '$str' AND uid = '$userId' ";
		$result = $this -> mDbAgent -> getRow($sql);
		//throw new Exception("验证码不正确123或已过期，请重新获取".$userId.$str.$result, 4016);exit;
		if ($result) {
			return 1;
		} else {
			return -1;
		}

	}

	public function findcreditcard() {
		global $ecs;
		$userId = self::getLoginUserId();
		$sql = "SELECT credit_card_id FROM " . $ecs -> table("credit_card") . " WHERE user_id = '$userId' ";
		$result = $this -> mDbAgent -> getOne($sql);
		//throw new Exception("验证码不正确123或已过期，请重新获取".$userId.$str.$result, 4016);exit;
		return $result;
	}

	public function delete_creditcard() {
		global $ecs;
		$userId = self::getLoginUserId();
		$sql = "DELETE  FROM " . $ecs -> table("credit_card") . " WHERE  user_id= '$userId' ";
		$this -> mDbAgent -> query($sql);
		$sql2 = "SELECT credit_card_id FROM " . $ecs -> table("credit_card") . " WHERE user_id = '$userId' ";
		$result = $this -> mDbAgent -> getOne($sql2);
		return $result;

	}

	//edouserdetail

	//操作  id 自增id,日志id	money 操作金额	use_type 操作动作
	//time 操作时间	transaction_number 交易流水号	user_id 操作者id	remark 备注
	//account_type 操作账户类型	account_id 操作账户ID	error_code 操作错误码

	public function edouserdetail() {
		global $sns;
		$userId = self::getLoginUserId();
		$sql1 = " SELECT money, time , use_type ,remark" . " FROM " . $sns -> table('account_log') . " as A left join " . $sns -> table('cash_account') . " as B on A.account_id=B.id WHERE B.user_id=" . $userId . " AND account_type='CashAccountManager'";
		$result = $this -> mDbAgent -> getAll($sql1);
		for ($i = 0; $i < count($result); $i++) {
			$result[$i][time] = date("Y-m-d H:i:s", $result[$i][time]);
			if ($result[$i][use_type] == 'freeze') {
				$result[$i][use_type] = '预授权';
			}
			if ($result[$i][use_type] == 'freezeComplete') {
				$result[$i][use_type] = '预授权完成';
			}
			if ($result[$i][use_type] == 'freezeCancel') {
				$result[$i][use_type] = '预授权取消';
			}
			if ($result[$i][use_type] == 'charge') {
				$result[$i][use_type] = '充值';
			}
		}
		return $result;
	}

	static public function getUserIdByPhone($phone) {
		global $sns, $db;
		$sql = "SELECT uid FROM " . $sns -> table("user") . " WHERE phone = $phone";
		return $db -> getOne($sql);
	}

	public function removeFollowByUserId($argv) {
		global $sns, $db;
		$userId = self::getLoginUserId();
		$car_id = $argv[carId];
		$sql = "DELETE FROM " . $sns -> table("car_follow") . " WHERE user_id = {$userId} AND car_id = {$car_id}";
		$carmanager = new CarManager();
		$res = $db -> query($sql);
		$carinfo = $carmanager -> getPreferenceCarList();
		if (!$res) {
			return false;
		} else {
			return $carinfo;
		}
	}

	static public function getUserViolateDeposit($userID = "") {
		global $sns;
		global $db;
		if (empty($userID)) {
			$userID = UserManager::getLoginUserId();

		}

		//获取未处理的交易流水号
		$sql = " SELECT sum(vdl.money)
		        FROM " . $sns -> table('violate_deposit_log') . ' vdl' . " WHERE vdl.status=0 AND vdl.user_id=$userID 
		        GROUP BY vdl.user_id";
		// echo $sql;
		return $db -> getOne($sql);
	}

	/**
	 * 取得用户消费等级
	 */
	static public function getUserConsumeLevel($userID = "", $fields = " * ") {
		global $ecs;
		global $sns;
		global $db;
		if (empty($userID)) {
			$userID = UserManager::getLoginUserId();
		}

		$sql = " SELECT $fields " . " FROM " . $ecs -> table('user_buy_rank') . " WHERE point <=( SELECT consume_point FROM " . $sns -> table("user") . " where uid=$userID) ORDER BY id DESC limit 1 ";

		return $db -> getRow($sql);
	}

	public function getMessageInfo($argv) {
		global $ecs;
		global $sns;
		global $db;
		$userID = UserManager::getLoginUserId();
		$sql = "SELECT * FROM " . $sns -> table("message") . " WHERE to_uid = {$userID} ORDER BY ctime desc LIMIT 0,20";
		$result = $db -> getAll($sql);

		$sql2 = "SELECT true_name FROM " . $sns -> table("user") . " WHERE uid = {$result[0][to_uid]}";
		for ($i = 0; $i < count($result); $i++) {
			$sql1[$i] = "SELECT true_name FROM " . $sns -> table("user") . " WHERE uid = {$result[$i][from_uid]}";
			$result1[$i] = $db -> getAll($sql1[$i]);
			$result[$i][from_uid] = $result1[$i][0][true_name];
			$result[$i][to_uid] = $db -> getOne($sql2);
		}

		$sql3 = " SELECT g.id_approve
	        FROM " . $ecs -> table('identity_approve') . " a " . " LEFT JOIN " . $sns -> table('user') . " u " . " ON u.uid = a.user_id " . " LEFT JOIN " . $ecs -> table('approve_log') . " g " . " ON g.id_approve = a.id " . " WHERE a.user_id =" . $userID . " ORDER BY g.approve_time DESC";
		$res = $db -> getOne($sql3);

		return $result;
	}

	public function getCashInfo($argv) {
		global $sns;
		global $db;
		$userID = UserManager::getLoginUserId();
		$sql = "SELECT * FROM " . $sns -> table("cash_account") . " WHERE user_id = {$userID}";
		$result = $db -> getRow($sql);

		$sql = "SELECT true_name FROM " . $sns -> table("user") . " WHERE uid = $userID";
		$trueName = $db -> getOne($sql);
		$result['true_name'] = $trueName;
		return $result;
	}

	/**
	 * 获取用户第一次通过身份审核的时间
	 * @param  [type] $userID [description]
	 * @return [type]         [description]
	 */
	static public function getUserIdApproveTime($userID) {
		global $ecs;
		global $db;

		$sql = " SELECT first_approve_time FROM " . $ecs -> table('identity_approve') . " WHERE user_id=" . $userID;
		$tCtime = $db -> getOne($sql);

		return $tCtime;
	}


	static public function getUserPhone()
	{
		return $_SESSION[SESSION_PHONE];
	}
}
?>