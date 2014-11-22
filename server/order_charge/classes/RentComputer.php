<?php

class RentComputer {

	public function __construct() {

	}

	public function getRent($order) {

	}

	/**
	 * 取得订单时长内所有完整时间item（24小时）的价格和
	 */
	public function getAllTopdayPrice($order, $basePrice, $holidayArr) {
		if (null == $order || !is_array($basePrice) || !is_array($holidayArr)) {
			throw new Exception('', 4000);
		}

		$rent = array('timeRent' => 0, 'containKm' => 0, 'mileageRent' => 0);

		$item['start'] = $order -> order_start_time;
		$item['end'] = $order -> order_start_time;

		require_once (Frm::$ROOT_PATH . 'order_charge/classes/PriceManager.php');
		$pm = new PriceManager();
		$conf = $pm -> getConfigHourKm();

		while ($item['start'] < $order -> order_end_time) {
			//86400代表一天的秒数
			$item['end'] = $item['start'] + 86400;
			if ($item['end'] > $order -> order_end_time) {
				//本函数只计算完整的24小时价格的和，因为这些时段都是按封顶价计算
				break;
			}

			//如果开始时间大于12:00，就按下一天的封顶价算
			$sStartDate = '';
			$tsHour = (int)(date('G', $item['start']));
			if ($tsHour > 12) {//12点以后，按第二天的价格算
				$sStartDate = date('Y-m-d', $item['start'] + 86400);
			} else {
				$sStartDate = date('Y-m-d', $item['start']);
			}

			//根据节假日，算价格
			if (in_array($sStartDate, $holidayArr)) {
				$rent['timeRent'] += $basePrice[HolidayManager::$HOLIDAY]['top_price'];
				$rent['containKm'] += $basePrice[HolidayManager::$HOLIDAY]['top_km'];
				$rent['mileageRent'] += $basePrice[HolidayManager::$HOLIDAY]['price_km'] * ($conf['day_km'] - $basePrice[HolidayManager::$HOLIDAY]['top_km']);
			} else {
				$rent['timeRent'] += $basePrice[HolidayManager::$WORKDAY]['top_price'];
				$rent['containKm'] += $basePrice[HolidayManager::$WORKDAY]['top_km'];
				$rent['mileageRent'] += $basePrice[HolidayManager::$WORKDAY]['price_km'] * ($conf['day_km'] - $basePrice[HolidayManager::$WORKDAY]['top_km']);
			}

			$item['start'] = $item['end'];
		}

		return $rent;
	}

}
?>