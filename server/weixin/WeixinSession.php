<?php 

class WeixinSession
{


	public $maxLifeTime = 1800;
	// SESSION 过期时间
	private $db=NULL;
	public $sessionKey = '';

	public $sessionExpiry = '';

	function __construct($openId)
	{	
		if (empty($openId)) {
			throw new Exception("WeixinSession init failed", 1);
		}

		$this->db=Frm::getInstance() -> getDb();
		$this->sessionKey =$openId;
		$this->sessionExpiry=$this->maxLifeTime+time();

		$this->loadSession();
		register_shutdown_function(array(&$this, 'closeSession'));
	} 

	function hasSessionKeyInDb()
	{
		$sql=" SELECT COUNT(1) FROM weixin_session WHERE open_id='".$this->sessionKey."'";
		return $this->db->getOne($sql)>0;
	}

	function insertSession($data)
	{
		
		$sql=" INSERT INTO weixin_session (open_id,data,expiry) VALUES ('".$this->sessionKey."','$data',".$this->sessionExpiry.")";
		return $this->db->execSql($sql);
	}

	function updateSession($data)
	{


		$sql=" UPDATE weixin_session SET data='$data' WHERE open_id='".$this->sessionKey."'";
		return $this->db->execSql($sql);
	}

	function saveSession($data)
	{
		
		if($this->hasSessionKeyInDb())
		{
			$this->updateSession($data);
		}
		else
		{
			$this->insertSession($data);
		}
	}

	function destroySession()
	{
		$now=time();
		$sql=" DELETE  FROM weixin_session  WHERE expiry<$now";
		$this->db->execSql($sql);
	}

	function closeSession()
	{
		global $_SESSION;
		$data = json_encode($_SESSION);
		if (!empty($data))
		{
			$this->saveSession($data);
		}

		$this->destroySession();
		echo "close";
	}

	function loadSession()
	{
		global $_SESSION;
		$oldSessions = $_SESSION;
		$dbSession = $this->getSessionInDb();

		$_SESSION = array_merge($dbSession,$oldSessions);

	}

	function getSessionInDb()
	{
		$sql = " SELECT * FROM weixin_session WHERE open_id=".$this->sessionKey;
		$ret=$this->db->getRow($sql);
		if (count($ret)>0) {
			$dbSession=json_decode($ret['data'],true);	
		}
		else
		{
			return array();
		}
		return $dbSession;
	}

}


 ?>