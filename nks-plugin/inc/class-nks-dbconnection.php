<?php

/**
 * Based on David Adams code: https://codeshack.io/super-fast-php-mysql-database-class/
 */
class Nks_dbConnection {

	protected $connection;
	protected $query;
	protected $show_errors = TRUE;
	protected $query_closed = TRUE;
	protected $error_handler = false;
	protected $has_valid_connection = false;
	
	public $query_count = 0;

	public function __construct($dbhost = 'localhost', $dbuser = 'root', $dbpass = '', $dbname = '', $error_handler = false, $charset = 'utf8') {
		
		$this->error_handler = $error_handler;
	
		try {
			
			$this->connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
			
			if ( $this->connection->connect_error ) {
				$this->error('Failed to connect to MySQL - ' . $this->connection->connect_error);
			}
			else {
				$this->has_valid_connection = true;
				$this->connection->set_charset($charset);
			}
			
		}
		catch( Exception $e ) {
			$this->error('Failed to connect to MySQL - ' . $e->getMessage() );
		}
		
			
	
	}
	
	public function has_valid_connection() {
		return $this->has_valid_connection;
	}

	public function query($query) {

		if (!$this->query_closed) {
			$this->query->close();
		}

		try {
			if ($this->query = $this->connection->prepare($query)) {
				if (func_num_args() > 1) {
					$x = func_get_args();
					$args = array_slice($x, 1);
					$types = '';
					$args_ref = array();
					foreach ($args as $k => &$arg) {
						if (is_array($args[$k])) {
							foreach ($args[$k] as $j => &$a) {
								$types .= $this->_gettype($args[$k][$j]);
								$args_ref[] = &$a;
							}
						} else {
							$types .= $this->_gettype($args[$k]);
							$args_ref[] = &$arg;
						}
					}
					array_unshift($args_ref, $types);
					call_user_func_array(array($this->query, 'bind_param'), $args_ref);
				}
				$this->query->execute();
				if ($this->query->errno) {
					$this->error('Unable to process MySQL query (check your params) - ' . $this->query->error , true);
				}
				$this->query_closed = FALSE;
				$this->query_count++;
			} else {
				$this->error('Unable to prepare MySQL statement (check your syntax) - ' . $this->connection->error , true);
			}

			return $this;
		}
		catch( Exception $e ) {
			$this->error('Failed to run MySQL query - ' . $e->getMessage() , true );
		}

		return false;
	}

	public function fetchAll($callback = null) {
		$params = array();
		$row = array();
		$meta = $this->query->result_metadata();
		while ($field = $meta->fetch_field()) {
			$params[] = &$row[$field->name];
		}
		call_user_func_array(array($this->query, 'bind_result'), $params);
		$result = array();
		while ($this->query->fetch()) {
			$r = array();
			foreach ($row as $key => $val) {
				$r[$key] = $val;
			}
			if ($callback != null && is_callable($callback)) {
				$value = call_user_func($callback, $r);
				if ($value == 'break')
					break;
			} else {
				$result[] = $r;
			}
		}
		$this->query->close();
		$this->query_closed = TRUE;
		return $result;
	}

	public function fetchArray() {
		$params = array();
		$row = array();
		$meta = $this->query->result_metadata();
		while ($field = $meta->fetch_field()) {
			$params[] = &$row[$field->name];
		}
		call_user_func_array(array($this->query, 'bind_result'), $params);
		$result = array();
		while ($this->query->fetch()) {
			foreach ($row as $key => $val) {
				$result[$key] = $val;
			}
		}
		$this->query->close();
		$this->query_closed = TRUE;
		return $result;
	}

	public function close() {
		return $this->connection->close();
	}

	public function numRows() {
		$this->query->store_result();
		return $this->query->num_rows;
	}

	public function affectedRows() {
		return $this->query->affected_rows;
	}

	public function lastInsertID() {
		return $this->connection->insert_id;
	}

	public function error($error, $die = false ) {
		if ( ! $die && $this->error_handler && is_object( $this->error_handler ) && method_exists( $this->error_handler, 'handle' ) ) {
			$this->error_handler->handle( $error );
		}
		else {
			print( $error );
			die();
		}
	}

	private function _gettype($var) {
		if (is_string($var))
			return 's';
		if (is_float($var))
			return 'd';
		if (is_int($var))
			return 'i';
		return 'b';
	}

}

?>