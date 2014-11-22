<?php
abstract class AccountManager {

	static public $MONEY_OP_TYPE_CHARGE = 'charge';
	static public $MONEY_OP_TYPE_CONSUME = 'consume';
	static public $MONEY_OP_TYPE_FREEZE = 'freeze';
	static public $MONEY_OP_TYPE_FREEZE_COMPLETE = 'freezeComplete';
	static public $MONEY_OP_TYPE_FREEZE_CANCEL = 'freezeCancel';
	static public $MONEY_OP_TYPE_WITHDRAW = 'withdraw';

	protected $account_type;

	/**
	 * [现金操作]
	 * @param  [string] $type               操作类型 charge,consume....
	 * @param  [int] $accountID          现金账户拥有者 charge,consume....
	 * @param  string $transaction_number 交易流水号 charge,consume....
	 * @param  string $money              操作金额
	 * @param  string $remark             备注
	 * @param  array $addon 附加的参数(比如订单号)
	 * @return [type]                     [description]
	 */
	public function moneyOperate($type, $accountID, &$transaction_number = '', $money = '', $remark = '', $addon = array()) {
		switch ($type) {
			case self::$MONEY_OP_TYPE_CHARGE :
				$retData = $this -> charge($accountID, &$transaction_number, $money, $addon);
				break;

			case self::$MONEY_OP_TYPE_CONSUME :
				$retData = $this -> consume($accountID, &$transaction_number, $money, $addon);
				break;

			case self::$MONEY_OP_TYPE_FREEZE :
				$retData = $this -> freeze($accountID, &$transaction_number, $money, $addon);
				break;

			case self::$MONEY_OP_TYPE_FREEZE_COMPLETE :
				$frInfo = $this->getAccountInfoByTN($transaction_number);
				$accountID=$frInfo['account_id'];
				$retData = $this -> freezeComplete($transaction_number, $money);

				break;

			case self::$MONEY_OP_TYPE_FREEZE_CANCEL :
				$retData = $this -> freezeCancel($transaction_number);
				break;
			case self::$MONEY_OP_TYPE_WITHDRAW :
				# code...
				break;
			case 'withdraw' :
				# code...
				break;

			case 'balanceInquiry' :
				# code...
				break;
		}

		$balance = $this -> balanceInquiry($accountID);

		//require_once (Frm::$ROOT_PATH . 'user/classes/UserManager.php');
		//@formatter:off
		$logData = array(
			'money' => $money, 
			'use_type' => $type, 
			'user_id' => $this->getUserIdByAccountId($accountID),
			'time' => time(), 
			'transaction_number' => $transaction_number, 
			'remark' => $remark, 
			'account_type' => $this -> account_type, 
			'account_id' => $accountID, 
			'error_code' => $retData['error_code'],
			'balance_amount' => $balance
		);
		//@formatter:on
		//操作记录动作
		$this -> log($logData);

		return $retData;
	}

	/**
	 * 账户操作记录
	 */
	protected function log($logData) {
		$db = Frm::getInstance() -> getDb();
		$db -> autoExecute('edo_account_log', $logData);
	}

	abstract protected function charge($accountID, &$transaction_number, $money, $addon);

	abstract protected function consume($accountID, $money, $addon);

	/**
	 * @param $addon array 其他数据
	 */
	abstract protected function freeze($accountID, &$transaction_number, $money, $addon);

	abstract protected function freezeCancel($transaction_number);

	abstract protected function freezeComplete($transaction_number, $money);

	abstract protected function withdraw();

	public function balanceInquiry($accountId) {

	}

	public function detail($accountId) {
		$db = Frm::getInstance() -> getDb();

		$sql = " SELECT l.money , l.use_type , l.time , l.remark , l.balance_amount FROM edo_account_log l 
			WHERE l.account_id = $accountId";
		$tmpUserAccountInfo = $db -> getAll($sql);

		return $tmpUserAccountInfo;

	}

}
