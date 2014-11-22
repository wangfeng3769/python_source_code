<?php

class DatabaseAgent {

	protected $mDbClient = NULL;

	protected $mMaxOnceSelected = 0;
	protected $mDbMode = null;

	protected $mRootPath = '';

	public function __construct($rootPath, $confNode) {
		require_once ($rootPath . 'hfrm/Log.php');

		$this -> mRootPath = $rootPath;

		$this -> mDbMode = strtolower($confNode -> getValue("mode"));
		$charset = $confNode -> getValue("charset");
		$dbname = $confNode -> getValue("name");
		$dbuser = $confNode -> getValue("user");
		$dbpass = $confNode -> getValue("password");
		$dbhost = $confNode -> getValue("address");
		$dbport = $confNode -> getValue("port");
		$this -> mMaxOnceSelected = $confNode -> getValue('max_once_selected');

		switch ($this -> mDbMode) {
			case 'mysql' :
				require_once $rootPath . 'hfrm/hfc/MysqlClient.php';
				$this -> mDbClient = new MysqlClient($dbname, $dbuser, $dbpass, $dbhost, $dbport, $charset);
				break;
			case 'db2' :
				require_once $rootPath . 'hfrm/hfc/DB2Client.php';
				$this -> mDbClient = new DB2Client($dbname, $dbuser, $dbpass, $dbhost, $dbport, $charset);
				break;
			case 'oracle' :
				require_once $rootPath . 'hfrm/hfc/OracleClient.php';
				$this -> mDbClient = new OracleClient($dbname, $dbuser, $dbpass, $dbhost, $dbport, $charset);
				break;
			default :
				throw new Exception("can not support database mode: $dbmode", 5001);
		}
	}

	public function startTransaction() {
		switch ($this->mDbMode) {
			case 'mysql' :
				require_once $this -> mRootPath . 'hfrm/hfc/MysqlClient.php';
				return new MysqlTransaction($this -> mDbClient);

				break;
			case 'db2' :
				require_once $this -> mRootPath . 'hfrm/hfc/DB2Client.php';
				return new Db2Transaction($this -> mDbClient);

				break;
			case 'oracle' :
				require_once $this -> mRootPath . 'hfrm/hfc/OracleClient.php';
				return new OracleTransaction($this -> mDbClient);

				break;
			default :
				throw new Exception("can not support database mode: $dbmode", 5001);
		}
	}

	public function execSql($sql) {
		try {
			$ret = $this -> mDbClient -> execSql($sql, $errno, $dberrno, $dberrstr, $stmt, $id);
		} catch(Exception $e ) {
			Log::e($e -> getMessage(), $e -> getCode(), $e -> getFile(), $e -> getLine());

			throw $e;
		}
	}

	public function limitSelect($sql, $start, $size) {
		try {
			return $this -> mDbClient -> limitSelect($sql, $start, $size);
		} catch(Exception $e) {
			Log::e($e -> getMessage(), $e -> getCode(), $e -> getFile(), $e -> getLine());

			throw $e;
		}
	}

	public function getAll($sql) {
		return $this -> limitSelect($sql, 0, $this -> mMaxOnceSelected);
	}

	public function getRow($sql) {
		$ret = $this -> limitSelect($sql, 0, 1);
		return $ret[0];
	}

	public function getOne($sql) {
		$val = NULL;

		$row = $this -> limitSelect($sql, 0, 1);
		if (is_array($row[0])) {
			foreach ($row[0] as &$val) {
				break;
			}
		}

		return $val;
	}

	/**
	 * 建议少用
	 */
	public function autoExecute($table, $field_values, $mode = 'INSERT', $where = '', $querymode = '') {
		$field_names = $this -> getCol('DESC ' . $table);

		$sql = '';
		if ($mode == 'INSERT') {
			$fields = $values = array();
			foreach ($field_names AS $value) {
				if (array_key_exists($value, $field_values) == true) {
					$fields[] = "`" . $value . "`";
					$values[] = "'" . $field_values[$value] . "'";
				}
			}

			if (!empty($fields)) {
				$sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
			}
		} else {
			$sets = array();
			foreach ($field_names AS $value) {
				if (array_key_exists($value, $field_values) == true) {
					$sets[] = "`" . $value . "` = '" . $field_values[$value] . "'";
				}
			}

			if (!empty($sets)) {
				$sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $sets) . ' WHERE ' . $where;
			}
		}
		if ($sql) {
			return $this -> getAll($sql, $querymode);
		} else {
			return false;
		}
	}

	protected function getCol($sql) {
		$arr = array();

		$res = $this -> getAll($sql);
		if (empty($res)) {
			return array();
		}

		foreach ($res as $row) {
			$arr[] = $row['Field'];
		}

		return $arr;
	}

}
?>