<?php

class Order {

	static public $STATUS_NOT_PAY = 0;
	static public $STATUS_ORDERED = 1;
	static public $STATUS_STARTED = 2;
	static public $STATUS_COMPLETED = 3;
	static public $STATUS_CANCELED = 4;
	static public $STATUS_PAY_TIMEOUT = 5;
	static public $STATUS_REVERT_TIMEOUT = 6;
	// static public $STATUS_MODIFY = 7;
	static public $STATUS_DOOR_CLOSED = 8;
	static public $STATUS_SETTLED = 9;

	static public $MODIFY_STAT_NOT_MODIFY = 0;
	static public $MODIFY_STAT_IN_MODIFY = 1;
	static public $MODIFY_STAT_MODIFIED = 2;
	static public $MODIFY_STAT_FAILED = 3;

	public $order_id = -1;
	public $order_no = '';
	public $order_stat = 0;
	public $car_id = -1;
	public $user_id = -1;
	public $add_time = 0;
	public $order_start_time = 0;
	public $order_end_time = 0;
	public $real_start_time = 0;
	public $real_end_time = 0;
	public $start_mileage = 0;
	public $end_mileage = 0;
	public $mile = 0;
	public $medal_id = -1;
	public $old_order_id = -1;
	public $modify_times = 0;
	public $use_car_password = '';
	public $modify_stat = 0;
	public $car_has_bug = 0;

}
?>