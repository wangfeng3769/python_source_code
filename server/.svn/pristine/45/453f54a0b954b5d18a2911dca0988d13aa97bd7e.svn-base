<?php
set_time_limit(0);
if(!defined('ROOT_PATH'))
{
	define('IN_ECS', true);
	require_once (dirname(__FILE__). "/../client/classes/UserManager.php" );
}
// require_once (dirname(__FILE__). "/../client/classes/UserManager.php" );


define("SQL_USER_PARAM", " u.user_id , u.user_name , u.sex , u.alias , u.signature , u.status , u.type , birthday , address_id ");


	class MedalContainer{
		const STATUS_NOT_GAIN = '0';
		const STATUS_GAINED = '1';
		const STATUS_USED = '2'; 
		
		static private $_instance = null;

		public function __construct()
		{

		}

		static public function getInstance()
		{
			if (self::$_instance==null) {
				self::$_instance = new MedalContainer();
			}
			return self::$_instance;
		}

		public function getMedalObj($medalID)
		{
			$medalInfo=$this->getMedalInfoByUserMedalId($medalID);
			if (empty($medalInfo)) {
				throw new Exception("", 4044);
				
			}
			$medalClassName=$medalInfo['class_name'];
			$medalFile = $medalInfo['file'];
			require_once($medalFile);
			$medalObj=new $medalClassName($medalInfo);

			return $medalObj;

		}

		//添加奖章
		public function addMedal($name , $file , $className , $icon,$desc ){
			global $db;
			global $ecs;
			$sql="INSERT INTO ". $ecs->table('medal') . "(name,file,class_name,icon,medaldesc) VALUES ( '$name','$file','$className','$icon','$desc')";
			$addMedalRow=$db->query($sql);
			return $addMedalRow;
		}
		
		//通过Id删除该奖章所有信息
		public function delMedal($id){
			global $db;
			global $ecs;
			$sql= "DELETE FROM".$ecs->table('medal') . "WHERE id = {$id}";
			$delMedalRow=$db->query($sql);
			return $delMedalRow;
		}
		//通过Id获得该奖章的所有信息
		public function getMedal($id){
			global $db;
			global $ecs;
			$sql="SELECT * FROM ".$ecs->table('medal'). " WHERE id={$id}";
			$res=$db->getRow($sql);
			return $res;
		}

		public function getMedalInfoByUserMedalId($id)
		{
			global $db;
			global $ecs;
			$sql=  	" SELECT m.*,um.time,um.status,um.use_time,um.user_id FROM ".$ecs->table('user2medal'). ' um '.
					" LEFT JOIN ".$ecs->table('medal').' m '.
					" ON m.id = um.medal_id ".
					" WHERE um.id=".$id; 
			$res=$db->getRow($sql);
			return $res;
		}
		//该用户已领取的所有的奖章
		public function getMyMedalArr($argv){
			global $db;
			global $ecs;
			
			if (empty($argv['user_id'])) {
				$userId=UserManager::getLoginUserId();
			}

			$sql="SELECT um.id,m.name,um.medal_id,um.status,m.medaldesc,m.icon FROM ".$ecs->table('medal')." m 
				LEFT JOIN ".$ecs->table('user2medal')." um ON m.id = um.medal_id
				 WHERE um.user_id ={$userId} 
				AND um.status = ".self::STATUS_GAINED;
			$res=$db->getAll($sql);
			return $res;
		}
		

		//获得该用户的所有的奖章
		public function generateUserMedal($user_id,$medal_name,$relative_attr){
			global $db;
			global $ecs;
			$sql="INSERT INTO ". $ecs->table('user2medal') . "(user_id,medal_id,status,relative_attr) VALUES 
			( '$user_id','SELECT id FROM '". $ecs->table('medal') ."'WHERE name = {$medal_name}','".self::STATUS_NOT_GAIN." ','$relative_attr')";
			$res=$db->query($sql);
			return $res;
		}
		
		//领取奖章
		public function gainMedal($userId,$medalId){
			global $db;
			global $ecs;
			$sql="UPDATE".$ecs->table('user2medal')."SET status= ".self::STATUS_GAINED ." WHERE medal_id={$medalId}  AND user_id={$userId}";
			$gainMedalRow=$db->query($sql);
			return $res;
		} 
		
		
		//该用户使用的某个奖章
		public function useMedal($userId,$medalId,$useTime){
			global $db;
			global $ecs;
			$sql=" UPDATE ".$ecs->table('user2medal')." SET status= ".self::STATUS_USED .",use_time=$useTime WHERE id = {$medalId} AND user_id={$userId} ";//AND status=".self::STATUS_GAINED;
			$userMedalRow=$db->query($sql);
			return $res;
		}
			
		//修改用户使用的奖章状态；
		public function setMedalStatus($medalId, $userId){
			global $db;
			global $ecs;
			
			$sql="UPDATE ".$ecs->table('user2medal')." SET status = ".self::STATUS_NOT_GAIN ." WHERE medal_id = {$medalId} AND user_id={$userId}";
			$setMedalStatusRow=$db->query($sql);
			return $res;
		}
		//返回用户奖章信息
		public function getMedalAndBonus(){
			global $db;
			global $ecs;
			$userId=UserManager::getLoginUserId();
				//可领取
			$sql_a="SELECT m.name,m.icon,m.id,m.medaldesc FROM ".
					$ecs->table('medal')." as m LEFT JOIN ".
					$ecs->table('user2medal')." as um ON m.id = um.medal_id
				 	WHERE um.user_id = '".$userId."' 
					AND um.status = ".self::STATUS_NOT_GAIN.";";
				//已领取
			$sql_b="SELECT m.name,m.icon,m.id,m.medaldesc FROM ".
					$ecs->table('medal')." as m LEFT JOIN ".
					$ecs->table('user2medal')." as um ON m.id = um.medal_id
				 	WHERE um.user_id = '".$userId."' 
					AND um.status = ".self::STATUS_GAINED.";";
			$a=$db->getAll($sql_a);	
			$b=$db->getAll($sql_b);
			$res1=($a)?$a:'';
			$res2=($b)?$b:'';
			
			$res[availableMedal]=$res1;
			$res[MyMedal]=$res2;
			
			return($res);
		}	

		public function pickMedal($argv){
			global $db;
			global $ecs;
			$id = $argv[id];
			$userId=UserManager::getLoginUserId();
			
			$sql_select="SELECT * FROM ".
						$ecs->table('user2medal'). "
						WHERE user_id={$userId} 
						AND medal_id={$id}
						AND status = ".self::STATUS_NOT_GAIN;
			
			$sql_update="UPDATE".
						$ecs->table('user2medal')."
						SET status= ".self::STATUS_GAINED." 
						WHERE medal_id={$id}  
						AND user_id={$userId}";
			
			/*$sql_insert="INSERT INTO ". 
						$ecs->table('user2medal') . "
						(user_id,medal_id,status) 
						VALUES
						( {$userId},{$id},'1')";	*/			
			
			$res_select=$db->getAll($sql_select);
			
			if (count($res_select)==0) {
				return false;
			}
			
			$res=$db->query($sql_update);
			
			return $this->getMedalAndBonus();
				
		}


		public function allocMedal2User($userID,$medalID)
		{	
			global $ecs;
			global $db;
			$data = array('user_id' => $userID, 'medal_id'=>$medalID,'time'=>date('Y-m-d H:i:s'),'status'=>0);
			$db->autoExecute($ecs->table('user2medal'),$data);
		}





		function isMedalCanBeUse($medalObj)
		{
			if (!$this->medalHadGained($medalObj)) {
				throw new Exception("", 4044);
			}

			$this->isMedalExpiren($medalObj);



		}


		function isMedalExpiren($medalObj)
		{

			if($medalObj->isExpiren())
			{
				throw new Exception("", 4042);
			}
		}


		function medalHadGained($medalObj)
		{
			return $medalObj->status == self::STATUS_GAINED;
		}
		function medalHadUsed($medalObj)
		{
			return $medalObj->status == self::STATUS_USED;
		}
		function medalNoGain($medalObj)
		{
			return $medalObj->status == self::STATUS_NOT_GAIN;
		}


	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	