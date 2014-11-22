<?php
	class EdoBigGun
	{

		private $dr = array();
		private $medalID=null;
		private $medalName=null;
		public function __construct($medalInfo)
		{
			$this->dr=array(
						 	'DR1'=>99,
						   	'DR2'=>98,
						   	'DR3'=>96,
						   	'DR4'=>94,
						   	'DR5'=>92,
						   	'DR6'=>90
							);
			$this->medalID=$medalInfo['id'];
			$this->medalName=$medalInfo['name'];
		}

		/**
		 * 优惠计算
		 * @param  [array] $caculateRent ['timeRent']
		 * @param  string $userID       [description]
		 * @return [type]               [description]
		 */
		public function calculateRent(&$caculateRent,$order,$userID="")
		{
			if (empty($userID)) {
				$userID=UserManager::getLoginUserId();
			}

			$bigGunLevel=$this->getBigGunLevel($userID,$this->medalID);

			
			$caculateRent['timeRent']=($caculateRent['timeRent']*$this->dr[$bigGunLevel])/100;
		}

		public function getBigGunLevel($userID,$medalID)
		{
			global $ecs;
			global $db;
			$sql =  " SELECT relative_attr ".
					" FROM ".$ecs->table('user2medal').
					" WHERE user_id=".$userID.
					" AND medal_id=".$medalID;
			$bigGunLevel=$db->getOne($sql);

			return $bigGunLevel;
			// if (empty($bigGunLevel)) {
			// 	throw new Exception("你没有 ".$this->medalName." 这个奖章", 1);
				
			// }
			// else
			// {
			// 	return $bigGunLevel;
			// }
		}


	}








?>