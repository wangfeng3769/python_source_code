<?php
class TwCarManager
{

    public function getCarList() 
    {

        require_once Frm::$ROOT_PATH . '/resource/classes/CarManager.php';
		$cm = new CarManager();
		$title='天物';
        $carIdArr = $cm ->twGetCarsIdByTitle($title);
        $sqlCarId = implode(',', $carIdArr);
         
		$sql = "SELECT c.id , c.status , c.model, c.name , c.gearbox , s.name AS station_name ,
		               c.brand , c.icon ,c.number , c.mile_factor
			    FROM edo_cp_car c LEFT JOIN edo_cp_station s ON c.station = s.id
			    WHERE c.id IN ($sqlCarId) AND c.delete_stat=0";
		
		$db = Frm::getInstance ()->getDb ();
		$ret = $db-> getAll($sql);
		return $ret;
	}

    //获得特定车辆正在使用中的订单
    public function getOrderingListByCarList($carIdArr)
     {
		if (empty($carIdArr))
		{
			return array();
		}

        date_default_timezone_set("Asia/Shanghai");
        $thisTime=time();        
        //echo $thisTime;echo"<br>";
        //echo date("Y-m-d H:i:s",$thisTime);echo "<br>";
        require_once Frm::$ROOT_PATH . 'order/classes/Order.php';

        $orderStatArr=array(Order::$STATUS_ORDERED,Order::$STATUS_STARTED,Order::$STATUS_REVERT_TIMEOUT);
        $modifyStat=Order::$MODIFY_STAT_NOT_MODIFY;

        $orderStatSql = implode(',', $orderStatArr);
		$sqlCarId = implode(',', $carIdArr);

		$sql ="SELECT o.car_id,o.order_stat,o.order_start_time,o.order_end_time,u.true_name,u.phone,u.uid 
				FROM edo_cp_car_order o 
				LEFT JOIN  edo_user u ON o.user_id=u.uid  
				WHERE o.car_id IN ( $sqlCarId ) 
				AND o.order_stat IN ($orderStatSql) 
				AND o.modify_stat=$modifyStat ";

        //echo $sql;echo"<br>";

		$db = Frm::getInstance() -> getDb();
		$ret = $db -> getAll($sql);
         
		if (empty($ret)) 
		{
			return array();
			//echo "kong";echo"<br>";
		}

		return $ret;
	}
}