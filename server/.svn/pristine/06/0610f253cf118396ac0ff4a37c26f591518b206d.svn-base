<?php

class Conf {

	/**
	 * Dom
	 *
	 * @var Dom
	 */
	protected $mDom;

	public function __construct($rootPath) {
		require_once ($rootPath . 'hfrm/hfc/Dom.php');

		$this -> mDom = new Dom();
		$this -> mDom -> loadXml(file_get_contents($rootPath . 'conf/conf.xml'));
	}

	public function __destruct() {
	}

	public function getNode($xpath, $index = 0) {
		return $this -> mDom -> getNode($xpath, $index);
	}

	public function getValue($xpath, $index = 0) {
		return $this -> mDom -> getValue($xpath, $index);
	}

	public function getLocalCharset() {
		return $this -> getValue('local_charset');
	}

	public function getLocalTimezone() {
		return $this -> getValue('local_timezone');
	}

}
?>
