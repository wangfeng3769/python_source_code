<?php

class MedalDesc {

	static public $STATUS_NOT_GAIN = '0';
	static public $STATUS_GAINED = '1';
	static public $STATUS_USED = '2';

	public $id;
	public $name;
	public $file;
	public $className;
	public $icon;
	public $desc;
	public $validDate;
	public $status;
}
