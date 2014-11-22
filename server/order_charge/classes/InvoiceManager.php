<?php

class InvoiceManager {

	public function __construct() {
	}

	public function getInvoiceInfo($userId) {
		require_once (Frm::$ROOT_PATH . 'order_charge/classes/InvoiceInfo.php');

		$sql = " SELECT invoice_title , invoice_address , contact , phone FROM invoice_info 
			WHERE ext_id = $userId AND type = " . InvoiceInfo::$INFO_TYPE_USER;
		$db = Frm::getInstance() -> getDb();
		$row = $db -> getRow($sql);

		return $this -> generateInvoiceInfo($row);
	}

	protected function generateInvoiceInfo($row) {
		if (empty($row)) {
			return null;
		}

		require_once (Frm::$ROOT_PATH . 'order_charge/classes/InvoiceInfo.php');
		$info = new InvoiceInfo();
		$info -> invoiceTitle = $row['invoice_title'];
		$info -> invoiceAddress = $row['invoice_address'];
		$info -> contact = $row['contact'];
		$info -> phone = $row['phone'];

		return $info;
	}

	public function askForInvoice($orderId, $title, $address, $contact, $phone, $asDefault, $userId) {
		if (!is_numeric($userId) || !is_numeric($phone)) {
			throw new Exception('', 4000);
		}

		$db = Frm::getInstance() -> getDb();
		$trans = $db -> startTransaction();

		if (empty($orderId)) {
			$info = $this -> getInvoiceInfo($userId);
			if (null == $info) {
				$this -> addInvoiceInfo($title, $address, $contact, $phone, $userId, InvoiceInfo::$INFO_TYPE_USER);
			} else {
				$this -> updateInvoiceInfo($title, $address, $contact, $phone, $userId, InvoiceInfo::$INFO_TYPE_USER);
			}
			
			$trans->commit();

			return;
		}

		$sql = "SELECT 1 FROM edo_cp_invoice_log WHERE order_id = $orderId";
		$ret = $db -> getOne($sql);
		if (!empty($ret)) {
			throw new Exception('', 4239);
		}

		$sql = "INSERT INTO edo_cp_invoice_log ( user_id , order_id ) VALUES ( $userId , $orderId )";
		$db -> execSql($sql);

		if ($asDefault) {
			$info = $this -> getInvoiceInfo($userId);
			if (null == $info) {
				$this -> addInvoiceInfo($title, $address, $contact, $phone, $userId, InvoiceInfo::$INFO_TYPE_USER);
			} else {
				$this -> updateInvoiceInfo($title, $address, $contact, $phone, $userId, InvoiceInfo::$INFO_TYPE_USER);
			}
		} else {
			require_once (Frm::$ROOT_PATH . 'order_charge/classes/InvoiceInfo.php');
			$this -> addInvoiceInfo($title, $address, $contact, $phone, $userId, InvoiceInfo::$INFO_TYPE_ORDER);
		}

		$trans -> commit();
	}

	protected function addInvoiceInfo($title, $address, $contact, $phone, $extId, $type) {
		$title = str_replace("\'", "\'\'", $title);
		$address = str_replace("\'", "\'\'", $address);
		$contact = str_replace("\'", "\'\'", $contact);

		$sql = "INSERT INTO invoice_info ( type , ext_id , invoice_title , invoice_address , contact , phone )
					VALUES ( $type , $extId , '$title' , '$address' , '$contact' , '$phone' )";
		$db = Frm::getInstance() -> getDb();
		$db -> execSql($sql);
	}

	protected function updateInvoiceInfo($title, $address, $contact, $phone, $extId, $type) {
		$title = str_replace("\'", "\'\'", $title);
		$address = str_replace("\'", "\'\'", $address);
		$contact = str_replace("\'", "\'\'", $contact);

		$sql = "UPDATE invoice_info SET invoice_title = '$title', invoice_address = '$address', 
					contact = '$contact' , phone = '$phone' , type = $type WHERE ext_id = $extId";
		$db = Frm::getInstance() -> getDb();
		$db -> execSql($sql);
	}

}
?>