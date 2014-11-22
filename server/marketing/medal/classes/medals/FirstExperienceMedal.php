<?php
require_once (dirname(__FILE__) . '/../../../../hfrm/Frm.php');

class FirstExperienceMedal {

	private $dr = array();
	private $medalID = null;
	private $medalName = null;
	public $gainTime = null;
	public $useTime = null;
	public $expirenLife = null;
	public $status = null;
	//用于租金结算的标记
	private $settlement = null;
	public function __construct($medalInfo) {
		$this -> settlement = "settlement";
		$this -> expirenLife = 30 * 3600 * 24;
		$this -> medalID = $medalInfo['id'];
		$this -> medalName = $medalInfo['name'];

		$this -> gainTime = $medalInfo['time'];
		$this -> useTime = $medalInfo['use_time'];
		$this -> gainTime = $medalInfo['time'];
		$this -> status = $medalInfo['status'];
		$this -> userID = $medalInfo['user_id'];

	}

	/**
	 * 优惠计算
	 * @param  [array] $caculateRent ['timeRent'],[kmRent]
	 * @param  [object] $order 		['order']
	 * @param  int $userID       [description]
	 * @param  enum $useType         'deposit','settlement'
	 * @return [type]               [description]
	 */
	public function calculateRent(&$calculateRent, $order, $useType, $userID = "") {
		//如果奖章不使用于结算

		if (empty($userID)) {
			$userID = UserManager::getLoginUserId();
		}

		if ($this -> isExpiren($userID)) {
			//奖章过期了
			throw new Exception("", 4042);
		} else {
			//计算价格 优惠方式：总费用减去前三小时的时间租金
			//计算前3小时的时间租金
			$cp = new RentComputer();
			$order -> endTime = $order -> startTime + 3 * 3600;
			$rent = $cp -> computeRentDetail($order);
			$reduceMoney = $rent['timeRent'];

			$calculateRent['willReduceMoney'] = $rent['timeRent'];
			if ($useType != $this -> settlement) {
				return $calculateRent;
			}

			$allFee = $calculateRent['timeRent'] + $calculateRent['kmRent'];

			$calculateRent['timeRent'] = $calculateRent['timeRent'] > $reduceMoney ? $calculateRent['timeRent'] - $reduceMoney : 0;

			$calculateRent['kmRent'] = $calculateRent['timeRent'] > $reduceMoney ? $calculateRent['kmRent'] : $calculateRent['kmRent'] - ($reduceMoney - $calculateRent['timeRent']);

			$calculateRent['kmRent'] = $calculateRent['kmRent'] < 0 ? 0 : $calculateRent['kmRent'];

		}

	}

	/**
	 * 只要奖章的使用时间 在该奖章的 存活期 期间,就是可以使用的
	 * @param  [type]  $userID [description]
	 * @return boolean         [description]
	 */
	public function isExpiren() {
		$useTime = $this -> getUseTime();

		$tCtime = UserManager::getUserIdApproveTime($this -> userID);
		if ($useTime > $tCtime + $this -> expirenLife) {
			return true;
		} else {
			return false;
		}
	}

	public function getUseTime() {
		global $db;
		global $ecs;

		$sql = " SELECT time FROM " . $ecs -> table('user2medal') . " WHERE id=" . $this -> medalID;
		return $db -> getOne($sql);
	}

}
