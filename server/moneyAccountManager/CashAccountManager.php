<?php
require_once (Frm::$ROOT_PATH . 'moneyAccountManager/AccountManager.php');

class CashAccountManager extends AccountManager {

	static public $ACCOUNT_TYPE = 'cash_account';

	static public $CHARGE_REQ_STATUS_NO_RESP = 0;
	static public $CHARGE_REQ_STATUS_RESP = 1;

	private $preFree = null;

	public function __construct() {
		$this -> account_type = self::$ACCOUNT_TYPE;
	}


	public function regiserPreFree($func)
	{
		$this->preFree[] = $func;
	}

	public function hasArrearage($userId) {
		$sql = "SELECT amount FROM edo_cash_account WHERE user_id = $userId";
		$db = Frm::getInstance() -> getDb();
		$amount = $db -> getOne($sql);
		if ($this->isAfterPayUser($userId)) {
			return false;
		}
		return $amount < 0;
	}

	public function freeze($accountID, &$transaction_number, $money, $addon) {
		$db = Frm::getInstance() -> getDb();
		//冻结金额应该小于等于余额

		$now = time();

		$accountInfo = $this -> getAccountInfo($accountID);
		foreach ($this->preFree as $v) {
			if(is_object($v))
			{
				$v($accountInfo);
			}
		}

		//为光华暂时增加的判断

		

		if ((float)$accountInfo['freeze_money'] + $money > (float)$accountInfo['amount']) {
			if (!$this->isAfterPayUser($accountInfo['user_id'])) {
				throw new Exception('', 4050);
			}
			else
			{
				//是后付费用户,判断后付费额度,0代表不限制
				$afterPayInfo=$this->afterPayInfo($accountInfo['user_id']);
				$groupAmount=$this->getGroupLimitAmountByUid($accountInfo['user_id']);
				if ($afterPayInfo['limit_money']!=0 && $money - $afterPayInfo['limit_money']>$groupAmount ) {
					throw new Exception("", 4054);
					
				}
			}
			
		} 
		
		$transaction_number = $this -> newTransactionNnumber();
		$freeze_money = $accountInfo['freeze_money'] + $money;
		//update cash_account freeze_money 字段,
		$db -> autoExecute('edo_cash_account', array('freeze_money' => $freeze_money), 'update', 'id=' . $accountInfo['account_id']);

		//insert 冻结操作记录到freeze_record
		$fzData = array('account_id' => $accountInfo['account_id'], 'money' => $money, 'transaction_number' => $transaction_number, 'time' => $now);
		$db -> autoExecute('edo_freeze_record', $fzData);
		

		// @formatter:off
		$content = array(
		'money'=>$money,
		'use_type'=>'freeze',
		'time'=>$now,
		'freeze_money'=>(float)$freeze_money,
		'amount'=>(float)$accountInfo['amount'],
		'error_code'=>0
		);

		return $content;
	}

	/**
	 * 冻结完成,进行最后结算
	 * @param  [string] $transaction_number [交易流水号]
	 * @param  [int] 	$money              [结算费用金额,即需系统收取的金额数]
	 * @return [array]                     [description]
	 */
	function freezeComplete($transaction_number,$money)
	{
		$db = Frm::getInstance() -> getDb();
		// if (empty($money))
		// {
		// 	return array('errorCode' => 3 ,'content'=>array('transaction_number'=>$transaction_number,'money'=>$money) );
		// }

		$frInfo = $this->getAccountInfoByTN($transaction_number);
		// print_r($frInfo);
		if (!$frInfo)
		{
			//该流水号信息不存在,或已处理
			$error_code = 2;
			$content = array(
			'money'=>$money,
			'use_type'=>'freezeComplete',
			'time' => time(),
			'transaction_number'=>$transaction_number,
			'account_type'=>$this->account_type,
			'user_id'=>$frInfo['user_id'],
			'account_id'=>$frInfo['account_id'],
			'error_code'=>$error_code
			);
			return array('errorCode' => $error_code ,'content'=>$content);

		}


		//进行结算,返回结算后的解冻金额给用户现金账户

		//update 用户现金账户 冻结金额数 和 余额amount
		$newFreezeMoney = $frInfo['allFreezeMoney'] - $frInfo['frMoney'];
		$newAmount 		= $frInfo['amount']-$money;

		$caData = array(
		'amount'      => $newAmount,
		'freeze_money'=> $newFreezeMoney
		);
		$db->autoExecute('edo_cash_account',$caData,'update','id='.$frInfo['account_id']);

		//delete 该冻结流水号记录 DELETE FROM `edo`.`edo_freeze_record` WHERE `edo_freeze_record`.`id` =

		$sql =  " DELETE FROM ".'edo_freeze_record'.
		" WHERE id=".$frInfo['frID'];
		$db->execSql($sql);
		if ((float)$money>(float)$frInfo['allFreezeMoney'])
		{
			//结算金额 大于 冻结金额, 操作金额不足

			//增加光华后付费的判断
			if ($this->isAfterPayUser($frInfo['user_id'])) {
				$error_code = 0;
			}
			else
			{
				$error_code = 1;
			}
			
		}
		else
		{
			$error_code = 0 ;
		}


		/*日志内容*/
		$content = array(
		'money'=>$money,
		'use_type'=>'freezeComplete',
		'time' => time(),
		'transaction_number'=>$transaction_number,
		'account_type'=>$this->account_type,
		'user_id'=>$frInfo['user_id'],
		'account_id'=>$frInfo['account_id'],
		'error_code'=>$error_code
		);
		return array('errorCode' => $error_code ,'content'=>$content);
	}

	/**
	 * 取消冻结,该流水号金额全额解冻
	 * @param  [type] $transaction_number [description]
	 * @param  [type] $money              [description]
	 * @return [type]                     [description]
	 */
	function freezeCancel($transaction_number)
	{
		//取消冻结 = 完全冻结不收取费用,所以传入金额数为 0 调用 完成冻结
		$retData = $this->freezeComplete($transaction_number,0);
		if (isset($retData['errorCode']))
		{
			$retData['content']['use_type']='freezeCancel';
			return $retData;
		}

	}

	/**
	 * [获取次交易流水号的现金账户信息]
	 * @param  [int] $transaction_number [交易流水号]
	 * @return [array]                     [description]
	 */
	function getAccountInfoByTN($transaction_number)
	{
		$db = Frm::getInstance() -> getDb();
		$sql =  " SELECT ca.id as account_id,ca.user_id,ca.amount,
		ca.freeze_money as allFreezeMoney, fr.id as frID,fr.money as frMoney  
		FROM ".'edo_cash_account'.' ca '.
		" LEFT JOIN ".'edo_freeze_record'.' fr '.
		" ON fr.account_id=ca.id ".
		" WHERE transaction_number='".$transaction_number."'";

		$ret = $db->getRow($sql);

		if(empty($ret))
		{
			return 0;
		}
		else
		{

			return $ret;
		}

	}
	/**
	 * 获取该现金账户对应的用户ID
	 * @param int $accountID 现金账户id
	 * 
	 * @return array(user_id,amount,amount,freeze_money)
	 */
	function getAccountInfo($accountID)
	{
		$db = Frm::getInstance() -> getDb();
		$sql =  " SELECT ca.id as account_id,ca.user_id,ca.amount,ca.freeze_money FROM ".'edo_cash_account'.' ca '.
		" WHERE id=".$accountID;
		// print_r($sql);
		$ret = $db->getRow($sql);

		if(empty($ret))
		{
			return 0;
		}
		else
		{
			return $ret;
		}
	}

	/**
	 * 生成交易流水号
	 * @return [string] [交易流水号]
	 */
	public function newTransactionNnumber()
	{
		return uniqid();
	}


	public function balanceInquiry($accountId)
	{
		$sql = "SELECT amount FROM edo_cash_account WHERE id = $accountId";
		$db = Frm::getInstance()->getDb();
		return $db->getOne($sql);
	}
	function withdraw()
	{
		echo '未实现';
	}

	function charge($accountID,&$transaction_number,$money,$addon){
		if( empty( $accountID ) ){
			return ;
		}

		$db = Frm::getInstance() -> getDb();

		$sql = "UPDATE " . 'edo_cash_account' . " SET amount = amount + $money WHERE id = $accountID ";
		$db->execSql($sql);

		$transaction_number=$this->newTransactionNnumber();
	}

	function create( $userId , $amount = 0 ){
		$db = Frm::getInstance() -> getDb();
		$sql = "INSERT INTO " . "edo_cash_account"  . "( user_id , amount , freeze_money ) VALUES ( $userId , $amount , 0 ) ";
		$db->execSql( $sql );
	}

	function consume($accountID,$money,$addon)
	{
		echo '未实现';
	}

	function getAccountId( $userId ){
		$db = Frm::getInstance() -> getDb();

		$sql = "SELECT id FROM " . 'edo_cash_account' . " WHERE user_id = $userId";
		return $db->getOne( $sql );
	}

	function getAccountDetail($accountId)
	{
		$db = Frm::getInstance() -> getDb();
		$sql = " SELECT *,(amount-freeze_money) as useable_money FROM edo_cash_account WHERE id='$account_id'";
		$ret = $db->getRow($sql);
		return $ret;
	}

	public function chargeRequst($userId , $amount,$type="mobile"){
		$now = time();

		$sql = "INSERT INTO cash_account_charge_req ( user_id , amount , status , time )
			VALUES ( $userId , $amount , " . self::$CHARGE_REQ_STATUS_NO_RESP . " , '" . date('Y-m-d H:i:s',$now). "' )";
		$db = Frm::getInstance()->getDb();
		$trans = $db->startTransaction();
		$db->execSql($sql);
		$id = $trans->lastId();
		$trans->commit();

		if ($type=='mobile') {
			// modify by yangbei 2013-06-03 修改银联移动端充值订单生成函数
			// require_once(Frm::$ROOT_PATH . 'PayAPI/upmobile/classes/UnionPayMobileManager.php');
			// $upmm = new UnionPayMobileManager();

			// return $upmm->getUPPayOrderInfo('charge' . $id, $amount);
			require_once(Frm::$ROOT_PATH.'PayAPI/upmp/classes/UnionMobilePayManager.php');
			$upmp = new UnionMobilePayManager();
			return $upmp->charge($id, $amount);
			// modify by yangbei 2013-06-03 修改过银联移动端充值订单生成函数
		}
		elseif ($type=='web') {
			require_once (Frm::$ROOT_PATH . 'PayAPI/UPPop/classes/UnionWebPayManager.php');
			$upweb = new UnionWebPayManager();
			//10代表充值
			$upweb->pay('100000' . $id,date('YmdHis', $now), $amount);
		}


		
	}

	public function getChargeUserId($orderId ){
		$sql = "SELECT user_id FROM cash_account_charge_req WHERE id = $orderId AND status = " . self::$CHARGE_REQ_STATUS_NO_RESP;
		$db = Frm::getInstance()->getDb();
		return $db->getOne($sql);
	}

	public function onPurchase($purchaseObj){
		if( 0 != strpos($purchaseObj->orderId, 'charge') ){
			return ;
		}

		$orderId = substr($purchaseObj->orderId, 6);

		$userId = $this->getChargeUserId($orderId);
		if( empty( $userId ) ){
			return;
		}

		$accountId = $this->getAccountId($userId);

		$transId = null;
		$this->moneyOperate( 'charge' ,  $accountId, $transId, $purchaseObj->amount, '银联手机支付充值' , array());

		$this->chargeRequstComplete($orderId);
	}
	//add by yangbei 2013-06-04 银联手机支付后台操作充值
	public function onPurchaseMobile($purchaseObj){
		$orderId = $purchaseObj['orderId'];
		$userId = $this->getChargeUserId($orderId);
		if( empty( $userId ) ){
			return;
		}
		$accountId = $this->getAccountId($userId);

		$transId = null;
		$this->moneyOperate( 'charge' ,  $accountId, $transId, $purchaseObj['amount'], '银联手机支付充值' , array());

		$this->chargeRequstComplete($orderId);
	}
	//add by yangbei 2013-06-04 
	public function onPurchaseWeb($purchaseObj)
	{
		if( 0 != strpos($purchaseObj->orderId, 'charge') ){
			return ;
		}

		$orderId = substr($purchaseObj->orderId, 6);

		$userId = $this->getChargeUserId($orderId);
		if( empty( $userId ) ){
			return;
		}

		$accountId = $this->getAccountId($userId);

		$transId = null;
		$this->moneyOperate( 'charge' ,  $accountId, $transId, $purchaseObj->amount, '银联网页支付充值' , array());

		$this->chargeRequstComplete($orderId);
	}

	public function chargeRequstComplete($id){
		$now = time();

		$sql = "UPDATE cash_account_charge_req SET status = " . self::$CHARGE_REQ_STATUS_RESP  . ' WHERE id = ' . $id;
		$db = Frm::getInstance()->getDb();
		$db->execSql($sql);
	}

	public function getUserIdByAccountId($accountId)
	{
		$sql = "SELECT user_id FROM edo_cash_account WHERE id = $accountId ";
		$db = Frm::getInstance()->getDb();
		return $db->getOne($sql);
	}

	//判断用户是否为后付费用户
	public function isAfterPayUser($userId)
	{
		$db = Frm::getInstance() -> getDb();
		// $sql =" SELECT ug.title FROM edo_user_group ug LEFT JOIN edo_user  u  ON ug.user_group_id=u.user_group_id WHERE  u.uid=".$userId;
		// $groupTitle=$db -> getOne($sql);
		// if (strpos($groupTitle, '光华')>-1) {
		// 	return true;
		// }
		// return false;
		// throw new Exception("Error Processing Request", 1);
		
		$sql = " SELECT apg.group_id FROM edo_user u 
				LEFT JOIN after_pay_group apg 
				ON apg.group_id=u.user_group_id 
				WHERE u.uid=$userId";
		$ret = $db->getOne($sql);
		return !empty($ret);
	}	


	public function afterPayInfo($userId)
	{
		$db = Frm::getInstance() -> getDb();
		$sql = " SELECT apg.group_id,apg.limit_money 
				FROM after_pay_group apg 
				LEFT JOIN edo_user u ON u.user_group_id=apg.group_id 
				WHERE u.uid=$userId ";
		$afterPayInfo=$db->getRow($sql);
		return $afterPayInfo;
	}

	public function getGroupLimitAmountByUid($userId)
	{
		//获取群组下所有会员的uid,再他们的所有金额的余额,冻结金额总和
		$db = Frm::getInstance() -> getDb();
		$sql = " 	SELECT uid FROM edo_user u 
					LEFT JOIN edo_cash_account ca ON u.uid = ca.user_id 
					WHERE u.user_group_id  in ( SELECT user_group_id FROM edo_user WHERE uid=$userId) ";
		$uidArr = $db->getAll($sql);

		$uidStr = '0';
		foreach ($uidArr as $v) {
			$uidStr .= ','.$v['uid'];
		}

		$sql = " SELECT SUM(amount) as amount,SUM(freeze_money) as freeze_money 
				FROM edo_cash_account 
				where user_id in ($uidStr) ";
		$ret=$db->getRow($sql);
		$amount = $ret['amount']-$ret['freeze_money'];
		return $amount;

	}
}
