
<?php 
//本类用于统计order信息
class StatisticsOrder
{

	static public function getCountOrderByStat($userId,$stat)
	{
		if (is_array($stat)) {
			$statStr=' 0 ';
			foreach ($stat as $v) {
				$statStr.= " OR  o.order_stat = $v ";
			}
			
		}
		else
		{
			$statStr=" o.order_stat=$stat ";
		}
		$sql = " SELECT count(1) FROM edo_cp_car_order o 
				 WHERE o.user_id = $userId AND ( $statStr ) GROUP BY o.user_id ";

		$db = Frm::getInstance() -> getDb();
		$counter = $db -> getOne($sql);

		return $counter;
	}
}

 ?>