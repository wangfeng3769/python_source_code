<?php

class RechargeManager 
{

	public function __construct() 
	{

	}

	function creatRecharge($userId , $amount , $operatorId , $reason) 
	{
		$time=time();
		$db = Frm::getInstance() -> getDb();

		$sqls="SELECT amount FROM edo_cash_account
		       WHERE user_id={$userId}";
        $balance=$db -> getOne($sqls);
        $balance+=$amount;

		$sqlu="UPDATE edo_cash_account as a 
			   SET  a.amount=$balance WHERE a.user_id={$userId} ";

		$sqli="INSERT INTO edo_cp_recharge ( user_id , amount , operator_id , recharge_time , reason , balance) 
		       VALUES ( $userId , $amount , $operatorId , $time , '$reason',$balance )";
		$db -> execSql($sqli);
		$db -> execSql($sqlu);

		$ret=$this->findOneRecharge($userId , $amount , $operatorId , $time , $balance);
		return $ret;
	}
    
 
    function findOneRecharge($userId , $amount , $operatorId , $time , $balance)
    {
    	$sql = "SELECT id FROM  edo_cp_recharge
    	        WHERE user_id={$userId} 
    	        AND amount={$amount} 
    	        AND operator_id={$operatorId}
    	        AND recharge_time={$time}
    	        AND balance={$balance}";
		$db = Frm::getInstance() -> getDb();
		$ret = $db -> getOne($sql);
		return $ret;
    }

	function getRechargeRecordById($id)
	{
		$sql = "SELECT u.uid , u.true_name AS userName , u.phone , r.id , r.amount , r.reason , r.recharge_time ,
		        r.balance , a.true_name AS operator
		        FROM edo_cp_recharge AS r 
		        	LEFT JOIN edo_user AS u 
		        ON r.user_id=u.uid  
		        	LEFT JOIN edo_user AS a 
		        ON r.operator_id=a.uid 
		        WHERE r.id = {$id}";
		$db = Frm::getInstance() -> getDb();
		$ret = $db -> getRow($sql);
		return $ret;
	}
    
    function getRechargeRecordsByUserId($userId)
	{
		$sql = "SELECT u.uid , u.true_name AS userName , u.phone , r.id , r.amount , r.reason , r.recharge_time ,
		        	r.balance , a.true_name AS operator
		        FROM edo_cp_recharge AS r 
		        	LEFT JOIN edo_user AS u 
		        ON r.user_id=u.uid 
		        	LEFT JOIN edo_cash_account AS c 
		        ON r.user_id=c.user_id 
		        	LEFT JOIN edo_user AS a 
		        ON r.operator_id=a.uid
		        WHERE r.user_id = {$userId}";
		$db = Frm::getInstance() -> getDb();
		$ret = $db -> getAll($sql);
		return $ret;
	}



}

  
