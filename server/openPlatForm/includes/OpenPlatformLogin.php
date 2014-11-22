<?php
require_once (ROOT_PATH . '/../client/classes/UserManager.php');
require_once (ROOT_PATH . '/../openPlatForm/qq/Tencent.php');
require_once (ROOT_PATH . '/../openPlatForm/qq/Config.php');
require_once (ROOT_PATH . '/../openPlatForm/weibo/includes/saetv2.ex.class.php');
require_once (ROOT_PATH . '/../openPlatForm/weibo/includes/config.php');

//require_once (ROOT_PATH . '/../openPlatForm/renren/class/RenrenRestApiService.class.php');
//require_once (ROOT_PATH . '/../openPlatForm/renren/class/RenrenOAuthApiService.class.php');
/*数据库操作,记录该平台用户id,if id存在,判断accesstoken是否存在*/
define('sina', 1);
define('qq', 0);
class OpenPlatformLogin {

	protected $dbAgent = null;
	const sina = 1;
	const qq = 0;
	const renren = 2;

	static protected $aa = array( 'renren' => self::renren , 'qq' => self::qq , 'sina' => self::sina);
	function OpenPlatformLogin() {
		global $db;

		/*全局db对象,需要注意*/
		$this -> dbAgent = $db;

	}

	function sina_put_in_db($token) {
		global $sns;

		$userId = -1;

		/*全局sns对象,需要注意*/
		$sql =" SELECT COUNT(*) FROM " . $sns -> table('open_login') . " WHERE	open_id='$token[uid]'" . " AND login_from=" . self::sina;
		if ($this -> dbAgent -> getOne($sql) > 0) {
			/*检查是否更新accesstoken*/
			$sql =  " SELECT ol.access_token , ol.user_id,ol.status,u.phone FROM edo_open_login ol
					LEFT JOIN edo_user u ON u.uid=ol.user_id
					WHERE ol.open_id='$token[uid]'  AND ol.login_from=" . self::sina;
			$row = $this -> dbAgent -> getRow($sql);

			$userId = $row['user_id'];
			$phone = $row['phone'];

			if ($row['access_token'] != $token['access_token'] || $row['status']==0) {
				/*更新sina_access_token*/
				$data['access_token'] = $token['access_token'];
				$data['login_from'] = self::sina;
				$data['timeout'] = $token['expires_in'];
				$data['last_login_time'] = date('Y-m-d H:i:s');
				$data['status'] = 1;
				$this -> dbAgent -> autoExecute($sns -> table('open_login'), $data, "update", "open_id=" . $token['uid']);
			}
		} else {
			//首先在自身平台加一个账户，这样就有自身平台的用户ID了，在写入open_login表
			$isNewUser=true;
			$um = new UserManager();

			if($um->isLogined())
			{
				//绑定账号
				
				$userId = $um->getLoginUserId();
			}
			else
			{
				//新账号登陆
				// echo "why!!!";
				// print_r($_SESSION);
				// die($um->isLogined());
				$userId = $um -> registerEmptyUser();
				
			}
			$data['access_token'] = $token['access_token'];
			$data['open_id'] = $token['uid'];
			$data['login_from'] = self::sina;
			$data['timeout'] = $token['expires_in'];
			$data['last_login_time'] = date('Y-m-d H:i:s');
			$data['user_id'] = $userId;
			$this -> dbAgent -> autoExecute($sns -> table('open_login'), $data);

			$phone = UserManager::getUserPhone();
			// array $argv['content']=>[sayContent],[car_icon],[shareTo]=>([qq] => 1,[sina] => 1)

		}
		$eduoToken=LoginManager::saveToken($userId,'');
		UserManager::setLoginSession(array('uid' => $userId,'token'=>$eduoToken,'phone'=>$phone));

		if ($isNewUser) {
			$this->shareBinding($userId,'sina');
		}
	}

	function qq_put_in_db($token) {
		global $sns;

		$userId = -1;
		/*全局sns对象,需要注意*/
		$sql = " SELECT COUNT(*) FROM " . $sns -> table('open_login') . " WHERE	open_id='$token[open_id]'" . " AND login_from=" . self::qq;
		if ($this -> dbAgent -> getOne($sql) > 0) {
			/*检查是否更新accesstoken*/
			$sql = " SELECT ol.access_token , ol.user_id,ol.status,u.phone FROM edo_open_login ol
					LEFT JOIN edo_user u ON u.uid=ol.user_id
					WHERE ol.open_id='$token[open_id]'  AND ol.login_from=" . self::qq;
			$row = $this -> dbAgent -> getRow($sql);

			$userId = $row['user_id'];
			$phone = $row['phone'];

			if ($row['access_token'] != $token['access_token'] || $row['status']==0 ) {
				/*更新sina_access_token*/
				$data['access_token'] = $token['access_token'];
				$data['login_from'] = self::qq;
				$data['timeout'] = $token['expires_in'];
				$data['last_login_time'] = date('Y-m-d H:i:s');
				$data['status'] = 1;
				$this -> dbAgent -> autoExecute($sns -> table('open_login'), $data, "update", "open_id='" . $token['open_id']."'");
			}
		} else {
			//首先在自身平台加一个账户，这样就有自身平台的用户ID了，在写入open_login表
			$um = new UserManager();
			$isNewUser=true;

			if($um->isLogined())
			{
				//绑定qq账号
				
				$userId = $um->getLoginUserId();
			}
			else
			{
				//新账号登陆
				// echo "why!!!";
				// print_r($_SESSION);
				// die($um->isLogined());
				$userId = $um -> registerEmptyUser();

			}
			

			$data['access_token'] = $token['access_token'];
			$data['open_id'] = $token['open_id'];
			$data['login_from'] = self::qq;
			$data['timeout'] = $token['expires_in'];
			$data['last_login_time'] = date('Y-m-d H:i:s');
			$data['user_id'] = $userId;
			$this -> dbAgent -> autoExecute($sns -> table('open_login'), $data);

			$phone = UserManager::getUserPhone();
			
		
		}
		$eduoToken=LoginManager::saveToken($userId,'');
		UserManager::setLoginSession(array('uid' => $userId,'token'=>$eduoToken,'phone'=>$phone));
		// print_r($_SESSION);
		if ($isNewUser) {
			$this->shareBinding($userId,'qq');
		}
	}


	function rr_put_in_db($token) {
		global $sns;
		$userId = -1;
		/*全局sns对象,需要注意*/
		$sql = " SELECT COUNT(*) FROM " . $sns -> table('open_login') . " WHERE	open_id='$token[open_id]'" . " AND login_from=" . self::renren;
		if ($this -> dbAgent -> getOne($sql) > 0) {
			/*检查是否更新accesstoken*/ 
			$sql = " SELECT ol.access_token , ol.user_id,ol.status,u.phone FROM edo_open_login ol
					LEFT JOIN edo_user u ON u.uid=ol.user_id
					WHERE ol.open_id='$token[open_id]'  AND ol.login_from=" . self::renren;
			$row = $this -> dbAgent -> getRow($sql);

			$userId = $row['user_id'];
			$phone = $row['phone'];
			if ($row['access_token'] != $token['access_token'] ||$row['status']==0) {
				/*更新sina_access_token*/
				$data['access_token'] = $token['access_token'];
				$data['login_from'] = self::renren;
				$data['timeout'] = $token['expires_in'];
				$data['status'] = 1;
				$data['last_login_time'] = date('Y-m-d H:i:s');
				$this -> dbAgent -> autoExecute($sns -> table('open_login'), $data, "update", "open_id=" . $token['open_id']." AND login_from=" . self::renren);
			}
		} else {
			//首先在自身平台加一个账户，这样就有自身平台的用户ID了，在写入open_login表
			$isNewUser=true;
			$um = new UserManager();

			if($um->isLogined())
			{
				//绑定rr账号
				$userId = $um->getLoginUserId();
			}
			else
			{
				//新账号登陆
				$userId = $um -> registerEmptyUser();
			}

			$data['access_token'] = $token['access_token'];
			$data['open_id'] = $token['open_id'];
			$data['login_from'] = self::renren;
			$data['timeout'] = $token['expires_in'];
			$data['last_login_time'] = date('Y-m-d H:i:s');
			$data['user_id'] = $userId;
			$this -> dbAgent -> autoExecute($sns -> table('open_login'), $data);

			$phone = UserManager::getUserPhone();
			
		}
		$eduoToken=LoginManager::saveToken($userId,'');
		UserManager::setLoginSession(array('uid' => $userId,'token'=>$eduoToken,'phone'=>$phone));
		if ($isNewUser) {
			$this->shareBinding($userId,'renren');
		}
	}

	// function tiePlatform($token)
	// {
	// 	global $sns;
	// 	//session中取得自身平台的账户，在写入open_login表
	// 	$data['access_token'] = $token['access_token'];
	// 	$data['open_id'] = $token['open_id'];
	// 	$data['login_from'] = self::$aa[$token['platform']];
	// 	$data['timeout'] = $token['expires_in'];
	// 	$data['last_login_time'] = date('Y-m-d H:i:s');
	// 	$data['user_id'] = $token['user_id'];
	// 	$this -> dbAgent -> autoExecute($sns -> table('open_login'), $data);
	// }

	/*判断该平台账号是否已经绑定的其他edo账号
	 *
	 * $token['open_id']
	 * $token['platform']
	 *
	*/
	function isUsedAccount($token)
	{
		global $sns;
		$sql =  " SELECT COUNT(*) ".
				" FROM ".$sns->table('open_login').
				" WHERE open_id=".$token['open_id'].
				" AND login_from=".self::$aa[$token['platform']];
		$count = $this->dbAgent->getOne($sql);

		return $count>0;

	}

	// function renrenShare($token)
	// {
	// 	$restApi = new RenrenRestApiService;
	// 	$sharePararms = array('type'=>'6','comment'=>'测试测试测试~~~~~','url'=>'http://www.eduoauto.com','access_token'=>$token);
	// 	$share = $restApi->rr_post_curl('share.share',$sharePararms);
	// 	return $share;
	// }

	/**
	 * 分享首次绑定易多平台消息到第三方开放平台
	 * @param  [type] $userId   [description]
	 * @param  [string] $platform  
	 * @return [type]           [description]
	 */
	function shareBinding($userId,$platform)
	{
		$plfArr[$platform]=1;
		$newBinderSaying='手机里的汽车~我刚刚安装了#易多汽车共享# http://www.eduoauto.com ';
		$icon_url="http://www.eduoauto.com/img/logo.jpg";
		$this->showOrder(array('sayContent'=>$newBinderSaying,'car_icon'=>$icon_url,'shareTo'=>$plfArr,'userId'=>$userId));
	}

	/**
	 * 分享到各微博平台,晒单
	 * @param  array $argv['content']=>[sayContent],[car_icon],[shareTo]=>([qq] => 1,[sina] => 1)
	 * @return [type]       [description]
	 */
	function showOrder($content)
	{
		global $ecs;
		global $sns;
		$user_id = UserManager::getLoginUserId();
		$loginFrom = array_keys($content['shareTo']);

		$where = ' 0 ';
		foreach ($loginFrom as $v)
		{
			$where .= ' or login_from='.self::$aa[$v]; 
		}


		$sql = 	" SELECT * FROM " . $sns -> table("open_login") . 
				" WHERE user_id = '$user_id' AND status = 1 AND (".$where.')';
		$allToken = $this -> dbAgent -> getAll($sql);
		//if (time() - strtotime($row['last_login_time']) > $row['timeout'])		

		$sayContent = $content['sayContent'];
		$carIcon = $content['car_icon'];

		foreach ($allToken as $v)
		{
			# code...
			switch ($v['login_from']) {
				case self::sina :
					//sina发微博
					if (time() - strtotime($v['last_login_time']) < $v['timeout'])
					{
						$c = new SaeTClientV2( WB_AKEY , WB_SKEY , $v['access_token'] );
						$ret = $c->upload($sayContent,$carIcon);	//发送微博
						if ( isset($ret['error_code']) && $ret['error_code'] > 0 ) {
							// echo "<p>发送失败，错误：{$ret['error_code']}:{$ret['error']}</p>";
							
						} else {
							//echo "<p>发送成功</p>";
						}
					}
					else
					{
						//acesstoken超时
					}
					
					break;

				case self::qq :
					//qq 发微博

					if (time() - strtotime($v['last_login_time']) < $v['timeout'])
					{
						//acesstoke未超时
						//$_SESSION[]
						require(ROOT_PATH . '/../openPlatForm/qq/Config.php');
						OAuth::init($client_id, $client_secret);
						$_SESSION['t_expire_in'] = $v['timeout'];
						$_SESSION['t_access_token'] = $v['access_token'];
						$_SESSION['t_openid'] = $v['open_id'];

						$params = array(
								        'content' => $sayContent,
								        'pic_url' => $carIcon
									    );
					    $r = Tencent::api('t/add_pic_url', $params, 'POST');
					    // echo $r;

					}	
					else
					{
						//acesstoken超时
					}
					break;

				case self::renren :
					//人人分享

					if (time() - strtotime($v['last_login_time']) < $v['timeout'])
					{
						//require(ROOT_PATH . '/../openPlatForm/renren/class/config.inc.php');
						
						$restApi = new RenrenRestApiService();
						$sharePararms = array('type'=>'6','comment'=>$sayContent,'url'=>$carIcon,'access_token'=>$v['access_token']);
						$share = $restApi->rr_post_curl('share.share',$sharePararms);
						// print_r($share);
					}
					else
					{
						//acesstoken超时
						// echo '超时';
					}
					break;
			}
		}

	}

	/**
	 * 成功登陆第三方平台后,如何返回到本平台
	 * @param  string $loginFrom  从哪种设备登陆进来,android,ios,web
	 * @return viod
	 */
	function loginFeedback($loginFrom)
	{
		switch ($loginFrom) {
			case 'android':
				echo '<script type="text/javascript">lapp.onOpenPlatLoginRet(1 , "' . UserManager::getSessionToken() . '" , "android")</script>';
				break;
			case 'ios':
				header("Location:login://token=".UserManager::getSessionToken()."&error=0&phone=".$_SESSION[SESSION_PHONE]);
				break;
			case 'web':
				header("Location: /index.php");

				break;

		}
	}

}
