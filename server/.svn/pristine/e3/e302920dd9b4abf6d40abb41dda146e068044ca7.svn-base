<?php
session_start();

class UserManager {

	//不能用数字
	static public $SESSION_USER_ID = 'uid';
	static public $SESSION_PHONE = 'phone';
	static public $SESSION_ACCESS_TOKEN = 'token';
	static public $SESSION_USER_NAME = 'uname';
	public function UserManager() {
	}

	public function login($userName, $password, $accessToken) 
	{
		if (!self::isLogined()) 
		{
			$loginInfo = array();

			$user = str_replace("\'", "\'\'", $userName);
			$password = str_replace("\'", "\'\'", $password);
			$token = str_replace("\'", "\'\'", $accessToken);
			$md5p = md5($password);

			require_once (Frm::$ROOT_PATH . 'user/classes/LoginManager.php');
			if (empty($user)) 
			{
				if (!empty($token)) 
				{
					//使用token登陆
					$loginInfo = LoginManager::loginWithToken($token);
				} 
				else 
				{
					throw new Exception('', 4000);
				}
			} 
			else 
			{
				$loginInfo = LoginManager::loginWithUserName($user, $md5p);
			}
			setcookie('user_name',$user);
			$this -> setLoginSession($loginInfo);
			return array("accessToken" => $loginInfo['token'], 'needModifyPassword' => $loginInfo['need_modify_password'],"uname"=>$_SESSION[self::$SESSION_USER_NAME]);
		} 
		else 
		{
			return array("accessToken" => $_SESSION[self::$SESSION_ACCESS_TOKEN],"uname"=>$_SESSION[self::$SESSION_USER_NAME]);
		}
	}

	static public function setLoginSession($row) {
		$_SESSION[self::$SESSION_USER_ID] = $row['uid'];
		$_SESSION[self::$SESSION_PHONE] = $row['phone'];
		$_SESSION[self::$SESSION_ACCESS_TOKEN] = $row['token'];
		$_SESSION[self::$SESSION_USER_NAME] = $row['true_name'];
	}

	static public function getLoginUserId() {
		$userId = empty($_SESSION[self::$SESSION_USER_ID]) ? -1 : $_SESSION[self::$SESSION_USER_ID];

		return $userId;
	}

	static public function isLogined() {
		//两种情况，一是本地系统登录的，二是openplat登录的
		$userId = self::getLoginUserId();
		if ($userId > 0) {
			if (empty($_SESSION[self::$SESSION_PHONE])) {

				//增加临时过滤条件,防止未绑定手机时不能做任何操作
				$item = $_REQUEST['item'];
				$igArr = array('bindLocal', 'logout');

				if (!in_array($item, $igArr)) {
					throw new Exception("", 4021);
				}
			}

			return true;
		}

		$accessToken = $_SESSION[self::$SESSION_ACCESS_TOKEN];
		return !empty($accessToken);
	}

	public function logout() {
		$_SESSION[self::$SESSION_USER_ID] = -1;
		$_SESSION[self::$SESSION_PHONE] = '';
		$_SESSION[self::$SESSION_ACCESS_TOKEN] = '';
	}

	protected function getSuperPassword($userId) {
		$sql = "SELECT super_password FROM edo_user WHERE uid = $userId";
		$db = Frm::getInstance() -> getDb();
		return $db -> getOne($sql);
	}

	public function hasSetSuperPassword($userId) {
		$p = $this -> getSuperPassword($userId);
		return !empty($p);
	}

	public function checkSuperPassword($userId, $superPassword) {
		$pass = $this -> getSuperPassword($userId);

		return md5($superPassword) == $pass;
	}

	public function setSuperPassword($userId, $password) {
		$enPass = md5($password);

		$db = Frm::getInstance() -> getDb();

		$sql = "SELECT password , super_password FROM edo_user WHERE uid = $userId";
		$row = $db -> getRow($sql);
		if (!empty($row['super_password'])) {
			throw new Exception('', 4217);
		}
		if ($row['password'] == $enPass) {
			throw new Exception('', 4218);
		}

		$sql = "UPDATE edo_user SET super_password = '$enPass' WHERE uid = $userId";
		$db -> execSql($sql);
	}
	/**
	 * 修改超级密码
	 * add by yangebei 2013-06-26
	 * 
	 */
	public function modifySuperPassword($userId, $password,$oldPassword) {
		$enPass = md5($password);
		$oldPass = md5($oldPassword);
		$db = Frm::getInstance() -> getDb();

		$sql = "SELECT password , super_password FROM edo_user WHERE uid = $userId";
		$row = $db -> getRow($sql);
		if (empty($row['super_password'])) {
			throw new Exception('', 4242);
		}
		if ($row['password'] == $enPass) {
			throw new Exception('', 4218);
		}
		if ($row['super_password']!=$oldPass) {
			throw new Exception('', 4004);
		}

		$sql = "UPDATE edo_user SET super_password = '$enPass' WHERE uid = $userId";
		$db -> execSql($sql);
	}

	public function getUser($userId) {
		$db = Frm::getInstance() -> getDb();

		$sql = "SELECT u.*, ca.amount,ca.freeze_money FROM edo_user u
				LEFT JOIN edo_cash_account ca ON ca.user_id=u.uid 
				WHERE u.uid = $userId ";
		return $db -> getRow($sql);
	}

	public function hasRegister($phoneNum) {
		$sql = "SELECT 1 FROM edo_user WHERE phone = '$phoneNum'";
		$db = Frm::getInstance() -> getDb();
		$one = $db -> getOne($sql);
		return !empty($one);
	}

	public function getCardId($userId) {
		$sql = "SELECT c.card_number FROM edo_cp_vip_card_issue i
			LEFT JOIN edo_cp_vip_card c ON i.vip_card_id = c.id WHERE i.user_id = $userId
			ORDER BY c.id DESC ";
		$db = Frm::getInstance() -> getDb();
		$card = $db -> getOne($sql);
		return $card;
	}

	/**
	 * 获取违章信息
	 * 不用GEt传参的话,默认使用登陆用户的user_id
	 * @return array 违章信息数组
	 */
	public function getViolateInfo($userId) {
		$db = Frm::getInstance() -> getDb();
		if (empty($argv['user_id'])) {
			$userID = $this -> getLoginUserId();
		}

		$sql = " SELECT co.order_no,cv.violate_time,cv.violate_location,cv.violate_type,cv.violate_cost,cv.violate_point,cv.agency_cost,c.number
				FROM edo_cp_car_violate cv  
				LEFT JOIN edo_cp_car_order  co   ON cv.order_id=co.order_id  
				LEFT JOIN edo_cp_car  c   ON c.id=co.car_id  
				WHERE co.user_id=" . $userID;
		// return $userID;
		$violateInfo = $db -> getAll($sql);

		foreach ($violateInfo as $k => $v) {
			$violateInfo[$k]['violate_time'] = date('Y-m-d H:i', $v['violate_time']);
			# code...
		}
		return $violateInfo;
	}

	public static function businessConsumeLog($orderId, $isPublic, $moneyProvenance, $reason) {
		$db = Frm::getInstance() -> getDb();
		$db -> autoExecute('business_consume', array('money_provenance' => $moneyProvenance, 'reason' => $reason, 'order_id' => $orderId, 'is_public' => $isPublic, 'ctime' => time()));
	}

	public function bindLocal($phone, $captcha) {
		$phonenum = trim($phone);
		if (empty($phonenum) || empty($captcha)) {
			throw new Exception('', 4000);
		}

		require_once( Frm::$ROOT_PATH . 'system/classes/SmsCaptcha.php' );
		$smsCaptcha = new SmsCaptcha();
		if (!$smsCaptcha -> check($captcha, $phonenum)) {
			throw new Exception('', 4003);
		}

		$userId = self::getLoginUserId();
		$user = $this -> getUserByPhone($phonenum);
		$db = Frm::getInstance()->getDb();
		if (empty($user)) {
			$sql = "UPDATE edo_user SET phone = '$phonenum' WHERE uid = $userId";
			$db->execSql($sql);
		} else {
			$sql = "UPDATE edo_open_login SET user_id = {$user['uid']} WHERE user_id = $userId";
			$db->execSql($sql);

			$sql = "DELETE FROM edo_user WHERE uid = $userId";
			$db->execSql($sql);

			$userId = $user['uid'];
		}

		//设置手机号码session
		$this->setLoginSession(array('uid'=>$userId , 'phone'=>$phonenum));
	}

	public function getUserByPhone($phone) {
		$phonenum = trim($phone);
		if (empty($phonenum)) {
			throw new Exception('', 4000);
		}

		$sql = "SELECT u.* FROM edo_user u WHERE phone = '$phonenum'";
		$db = Frm::getInstance() -> getDb();
		$user = $db -> getRow($sql);

		return $user;
	}

	public function isTwUserGroup($userId)
	{
		$db = Frm::getInstance() -> getDb();
		$sql =" SELECT ug.title 
				FROM edo_user_group ug 
				LEFT JOIN edo_user  u  
				ON ug.user_group_id=u.user_group_id 
				WHERE  u.uid=".$userId;
		$groupTitle=$db -> getOne($sql);
		if (strpos($groupTitle, '天物')>-1) {
			return true;
		}
		return false;
	}

	public function register($phonenum, $password) 
	{
		$db = Frm::getInstance() -> getDb();
		if (!empty($phonenum)) 
		{
			$sql = "SELECT 1 FROM edo_user WHERE phone = '$phonenum'";
			if (count($db -> getAll($sql)) > 0)
			{
				throw new Exception("该手机号码已被使用", 4002);
			}
		}

		if (empty($password))
		{
			$ep = "";
		} 
		else 
		{
			$ep = md5($password);
		}
		$sql = "SELECT version FROM user_protocal ";
		$userProtocalVersion = $db -> getOne($sql);
		$now = time();		
		$sql = "INSERT INTO edo_user ( phone , password , ctime , protocal_version ) VALUES ( '$phonenum' , '$ep' , $now , $userProtocalVersion )";
		/*$ret = */
		$db -> execSql($sql);
		/*echo !$ret;
		if (!$ret) 
		{
			throw new Exception("数据库错误", 5002);
		}*/
        
        $sql = "SELECT uid FROM edo_user WHERE phone = '$phonenum'";
		$userId = $db -> getOne($sql);
        
		//开一个现金账户
		require_once (Frm::$ROOT_PATH . 'moneyAccountManager/CashAccountManager.php');
		$account = new CashAccountManager();
		$account -> create($userId);

		return $userId;
	}
    
    /**
	 * $Author: 马涛
	 * $time:	2013-05-16 17:07:46Z
	 * $modify:	
	 *copy from www\client\UserManager.php
	 */
    /*判断金额是否合法（以分为单位）*/
	function isValidMoney($amount)
    {
		return $b = preg_match("/^[-]?[1-9]{1}\d*$/", $amount);
    }
    /*验证文本是否合法*/
    function isValidText($text)
    {
        $b = preg_match("/\*|-{2}|['\";%<>]+/",$text);
        return !$b;
    }
    /*验证手机号码是否合法*/
    public function checkphone_s($str2) {
		return $b = preg_match("/^1[3|4|5|8][0-9]\d{8}$/", $str2);
	}
    /*通过手机号码获得用户ID*/
	static public function getUserIdByPhone($phone) 
	{
		$db = Frm::getInstance() -> getDb();
		$sql = "SELECT uid FROM edo_user WHERE phone = {$phone}";
		return $db -> getOne($sql);
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
	 * 获取审核状态
	 * @return [type] [description]
	 */
	function getApproveStat($userID = '') 
	{
		$db = Frm::getInstance() -> getDb();
		if (empty($userID)) {
			$userID = $this -> getLoginUserId();
		}

		$sql = " SELECT approve_stat FROM edo_cp_identity_approve WHERE user_id=" . $userID;

		$approve_stat = $db -> getOne($sql);

		return $approve_stat;
	}


	public function getOpenPlatInfo($userId) {
		$db = Frm::getInstance() -> getDb();
		$sql = "select login_from,status from edo_open_login where user_id=" . $userId;

		$medal = $db -> getAll($sql);

		//固定数组格式
		return $medal;
		//make_json_result($medal);
	}

	public function untie($userId,$from) {
		$db = Frm::getInstance() -> getDb();
		$sql = "update edo_open_login set status=0 where user_id=" . $userId . " and login_from=".$from;
		//print_r($argv);
		if ($db -> execSql($sql)) {
			return array('id' => $argv['id']);
			// make_json_result(array('id'=>$argv['id']));
		}

	}
}
?>