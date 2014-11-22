<?php
class ECM {

	public function __construct() {

	}

	public function getDeviceInfo($idArr) {
		if (!is_array($idArr)) {
			throw new Exception('', 4000);
		}
		
		require_once( Frm::$ROOT_PATH . 'ecm/include/PubMarco.php');

		$infoArr = array();
		foreach ($idArr as $carId) {
			$strIni = ROOT_PATH . CAR_FOLDER . "/" . $carId . "/" . CAR_CONFIG_FILE;
			$arrItem = parse_ini_file($strIni);
			
			$infoArr[$carId] = $arrItem;
		}
		
		return $infoArr;
	}

}
?>