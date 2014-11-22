<?php
	require_once( ROOT_PATH . "../client/classes/UserManager.php" );
	class MedalContainer{
		static public $STATUS_NOT_GAIN = 0;
		static public $STATUS_GAINED = 1;
		static public $STATUS_USED = 2; 
		
		//添加奖章
		public function addMedal($name , $file , $className , $icon,$desc ){
			global $db;
			global $ecs;
			$sql="INSERT INTO ". $ecs->table('Medal') . "(name,file,class_name,icon,medaldesc) VALUES ( '$name','$file','$className','$icon','$desc')";
			$addMedalRow=$db->query($sql);
			return $addMedalRow;
		}
		
		//通过Id删除该奖章所有信息
		public function delMedal($id){
			global $db;
			global $ecs;
			$sql= "DELETE FROM".$ecs->table('Medal') . "WHERE id = {$id}";
			$delMedalRow=$db->query($sql);
			return $delMedalRow;
		}
		//通过Id获得该奖章的所有信息
		public function getMedal($id){
			global $db;
			global $ecs;
			$sql="SELECT * FROM ".$ecs->table('Medal'). "WHERE id={$id}";
			$res=$db->getRow($sql);
			return $res;
		}
		//该用户已领取的所有的奖章
		public function getMyMedalArr($userId){
			global $db;
			global $ecs;
			$sql="SELECT name,medal_id,status FROM ".$ecs->table('Medal')." m LEFT JOIN ".$ece->table('User2Medal')." um ON m.id = um.medal_id
				 WHERE um.user_id ={$userId} 
				AND um.status = $STATUS_GAINED";
			$res=$db->getAll($sql);
			return $res;
		}
		
		//获得该用户的所有的奖章
		public function generateUserMedal($user_id,$medal_name,$relative_attr){
			global $db;
			global $ecs;
			$sql="INSERT INTO ". $ecs->table('User2Medal') . "(user_id,medal_id,status,relative_attr) VALUES 
			( '$user_id','SELECT id FROM '". $ecs->table('Medal') ."'WHERE name = {$medal_name}','$STATUS_NOT_GAIN','$relative_attr')";
			$res=$db->query($sql);
			return $res;
		}
		
		//领取奖章
		public function gainMedal($userId,$medalId){
			global $db;
			global $ecs;
			$sql="UPDATE".$ecs->table('User2Medal')."SET status= $STATUS_GAINED WHERE medal_id={$medalId}  AND user_id={$userId}";
			$gainMedalRow=$db->query($sql);
			return $res;
		} 
		
		
		//该用户使用的某个奖章
		public function useMedal($userId,$medalId){
			global $db;
			global $ecs;
			$sql="UPDATE".$ecs->table('User2Medal')."SET status= $STATUS_USED WHERE medal_id = {$medalId} AND user_id={$userId}";
			$userMedalRow=$db->query($sql);
			return $res;
		}
		
		
		
		
		//修改用户使用的奖章状态；
		public function setMedalStatus($medalId, $userId){
			global $db;
			global $ecs;
			
			$sql="UPDATE".$ecs->table('User2Medal')."SET status = $STATUS_NOT_GAIN WHERE medal_id = {$medalId} AND user_id={$userId}";
			$setMedalStatusRow=$db->query($sql);
			return $res;
		}
		//返回用户奖章信息
		public function getMedalinfo(){
			global $db;
			global $ecs;
			$userId=UserManager::getLoginUserId;
			//可领取
			$sq1l="SELECT name,icon,id,desc FROM ".$ecs->table('Medal')." m LEFT JOIN ".$ece->table('User2Medal')." um ON m.id = um.medal_id
				 WHERE um.user_id ={$userId} 
				AND um.status = $STATUS_NOT_GAIN";
			$res1=$db->getAll($sql1);
			//已领取
			$sql2="SELECT name,icon,id,desc FROM ".$ecs->table('Medal')." m LEFT JOIN ".$ece->table('User2Medal')." um ON m.id = um.medal_id
				 WHERE um.user_id ={$userId} 
				AND um.status = $STATUS_GAINED";
			$res2=$db->getAll($sql2);
			echo $sql1,$sql2; exit;
			return $res = array("availableMedal"=>$res1,"MyMedal"=>$res2);
		}		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	