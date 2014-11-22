<?php

class OrderRecord {

	static public $MAX_UNDONE_COUNT = 2;
	static public $MAX_MODIFY_TIMES = 3;

	public function __construct() {

	}
	public function getMyOrderList($userId,$start, $size) {
		$db = Frm::getInstance() -> getDb();
		$sql = "SELECT COUNT(1)  
				FROM edo_cp_car_order  o 
				LEFT JOIN edo_cp_car  c ON c.id = o.car_id 
				LEFT JOIN edo_cp_station st ON c.station = st.id
				LEFT JOIN 
				(SELECT COUNT(1) AS num , order_id  FROM edo_cp_invoice_log  
				GROUP BY order_id) i  ON i.order_id=o.order_id  
				LEFT JOIN 
				( SELECT user_id, COUNT(1) as open_login_num FROM edo_open_login 
				WHERE status=1 GROUP BY user_id )  w  ON w.user_id = o.user_id  
				WHERE o.user_id = $userId AND o.modify_stat = 0 ";
		$countNum = $db->getOne($sql);

		$sql = "SELECT o.order_id , o.order_no , o.order_start_time , o.order_end_time , o.order_org_cost ,
				o.car_id , c.name , c.number , st.name AS station , c.icon , o.order_stat ,w.open_login_num, o.add_time ,
				i.num as invoice_num , c.brand , c.gearbox,o.modify_stat
				FROM edo_cp_car_order  o 
				LEFT JOIN edo_cp_car  c ON c.id = o.car_id 
				LEFT JOIN edo_cp_station st ON c.station = st.id
				LEFT JOIN 
				(SELECT COUNT(1) AS num , order_id  FROM edo_cp_invoice_log  
				GROUP BY order_id) i  ON i.order_id=o.order_id  
				LEFT JOIN 
				( SELECT user_id, COUNT(1) as open_login_num FROM edo_open_login 
				WHERE status=1 GROUP BY user_id )  w  ON w.user_id = o.user_id  
				WHERE o.user_id = $userId AND o.modify_stat = 0 
				ORDER BY o.add_time DESC ";
		
		$orderRet = $db -> limitSelect($sql,$start, $size);
		if (empty($orderRet)) {
			return array();
		}

		$orderNoArr = array();
		$carIdArr = array();
		foreach ($orderRet as $key => &$row) {
			$row['order_start_time'] = date('Y-m-d H:i', $row['order_start_time']);
			$row['order_end_time'] = date('Y-m-d H:i', $row['order_end_time']);
			$row['add_time'] = date('Y-m-d H:i:s', $row['add_time']);
			$orderNoArr[] = '\'' . $row['order_no'] . '\'';
			$carIdArr = $row['car_id'];
		}

		$sql = "SELECT count(*),c.amount , c.order_no , c.item FROM order_charge c
			WHERE order_no IN (" . implode(',', $orderNoArr) . ") AND ( item = 1 OR item = 2 OR item = 3 )
			AND status IN ( 0 , 1 )
			ORDER BY order_no ";
		$arr = array();
		$depositCountArr = array();
		$ret = $db -> limitSelect($sql,$start, $size);
		if (!empty($ret)) {
			require_once (Frm::$ROOT_PATH . 'order_charge/classes/OrderCharge.php');
			foreach ($ret as $value) {
				$depositCountArr[$value['order_no']] += $value['amount'];

				$item = '';
				if (OrderCharge::$ITEM_USE_TIME_DEPOSIT == $value['item']) {
					$item = 'timeDeposite';
				} else if (OrderCharge::$ITEM_USE_MILE_DEPOSIT == $value['item']) {
					$item = 'mileDeposite';
				} else if (OrderCharge::$ITEM_VIOLATE_DEPOSIT == $value['item']) {
					$item = 'violateDeposite';
				}
				$arr[$value['order_no']][$item] = $value['amount'];
			}
		}

		foreach ($orderRet as &$value) {
			$value['deposit'] = $depositCountArr[$value['order_no']];
			$value['depositDetail'] = $arr[$value['order_no']];
		}
		
		$orderRet['countNum']=$countNum;
		return $orderRet;
	}
	function pager($page,$pageNum,$countNum)
	{
		$page++;
		$pageCountNum = ceil($countNum/$pageNum);
		if ($page>$pageCountNum ) {
			$page=$pageCountNum;
		}
		return array('page'=>$page,'pageNum'=>$pageNum,'countNum'=>$countNum,'pageCountNum'=>$pageCountNum);
	}

	function getMyOrderPageList($userId,$page,$pageSize=5)
	{
		$start = $page*$pageSize;

		$orders=$this->getMyOrderList($userId,$start, $pageSize);
		// print_r($orderList);
		$pager=$this->pager($page,$pageSize,$orders['countNum']);
		unset($orders['countNum']);
		return array('orders'=>$orders,'pager'=>$pager);

	}

}	

?>
