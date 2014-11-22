<?php

require_once (dirname(__FILE__) . '/Error.php');

/**
 * mysql客户端类。该类用长连接实现。
 *
 */
class MysqlClient {

	static public $MAX_ROWS_ONCE = 200;

	protected $conn = NULL;

	/**
	 * @param string $dbname
	 * @param string $dbuser
	 * @param string $dbpass
	 * @param string $dbhost
	 * @param string $port
	 * @param string $charset
	 * @return void
	 */
	public function MysqlClient($dbname = '', $dbuser = 'root', $dbpass = '', $dbhost = 'localhost', $port = '3306', $charset = "utf8") {
		$server = $dbhost . ":" . $port;
		$this -> conn = @mysql_connect($server, $dbuser, $dbpass);
		if (false === $this -> conn) {
			throw new Exception("connect to mysql $dbuser@$server error.", 2);
		}

		if (!@mysql_select_db($dbname, $this -> conn)) {
			throw new Exception('select database:' . $dbname . ' error.', 3);
		}

		if (false === @mysql_query("SET NAMES $charset")) {//相当于后面版本的mysql_set_charset函数
			throw new Exception("set database $server charset $charset error.", 4);
		}
	}

	/**
	 * 执行一条SQL语句
	 *
	 * @param string $sql
	 * @param stmt $stmt
	 */
	public function execSql($sql, &$stmt = -1) {
		$stmt = @mysql_query($sql, $this -> conn);
		if (false === $stmt) {
			throw new Exception("mysql query error,sql: $sql", 5);
		}
	}

	/**
	 * 选取从指定位置开始的指定条数的数据
	 *
	 * @param string $sql
	 * @param int $start
	 * @param int $size
	 * @return array
	 */
	public function limitSelect($sql, $start, $size) {
		if ($start < 0 || $size <= 0) {
			throw new Exception('', 1);
		}

		$ret = array();

		$this -> sqlTransLimit($sql, $start, $size);

		$stmt = null;
		$this -> execSql($sql, $stmt);

		$numRows = @mysql_num_rows($stmt);
		if ($numRows < $size) {
			$size = $numRows;
		}

		for ($i = 0; $i < $size; ++$i) {
			$row = @mysql_fetch_array($stmt, MYSQL_ASSOC);
			if (false === $row) {
				@mysql_free_result($stmt);
				throw new Exception('', 6);
			} else {
				$ret[] = $row;
			}
		}

		return $ret;
	}

	public function getAll($sql) {
		return $this -> limitSelect($sql, 0, self::$MAX_ROWS_ONCE);
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

	protected function sqlTransLimit(&$sql, $start, $size) {
		$iselect = stripos($sql, "select");
		if (false !== $iselect) {
			$sql .= " LIMIT $start , $size ";
		}
	}

}

/**
 * 事务类，要把一些sql当成事务，实例化该类
 *
 */
class MysqlTransaction {

	/**
	 *
	 * @var MysqlClient
	 */
	protected $mDb = null;

	protected $mCommited = false;

	public function __construct($mysqlClient) {
		$this -> mDb = $mysqlClient;
		$this -> mDb -> execSql('BEGIN');
	}

	public function __destruct() {
		if( !$this->mCommited ){
			$this -> rollback();
		}
	}

	/**
	 * 回滚一个事务
	 *
	 * @return bool
	 */
	public function rollback() {
		$this -> mDb -> execSql('ROLLBACK');
	}

	/**
	 * 提交一个事务
	 *
	 * @return bool
	 */
	public function commit() {
		$this -> mDb -> execSql('COMMIT');
		$this->mCommited = true;
	}

	/**
	 * 取得上次插入语句所引起的自增长字段的值，要取得值，插入的表必须有自增长字段。
	 * 注意，该函数返回的是同一连接下上次插入语句所引起的自增长字段的值，所以放在事务类里。
	 *
	 * mysql_insert_id() 将 MySQL 内部的 C API 函数 mysql_insert_id() 的返回值转换成 long（PHP 中命名为 int）。如果 AUTO_INCREMENT 的列的类型是 BIGINT，则 mysql_insert_id() 返回的值将不正确。可以在 SQL 查询中用 MySQL 内部的 SQL 函数 LAST_INSERT_ID() 来替代。
	 *
	 * @param integer $errno
	 * @param string $dberrno
	 * @param string $dberrstr
	 * @return string
	 */
	public function lastId() {
		return $this -> mDb -> getOne('SELECT LAST_INSERT_ID()');
	}

}
?>