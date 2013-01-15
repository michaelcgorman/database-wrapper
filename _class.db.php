<?php

class DB {
	protected $dbh;
	protected $dbn = '';
	protected $user = '';
	protected $pass = '';

	public function __construct() {
		try {
			$this->dbh = new PDO('mysql:host=localhost;dbname='.$this->dbn, $this->user, $this->pass, array(
				PDO::ATTR_PERSISTENT => true
			));
		} catch (PDOException $e) {
			echo 'Error connecting to database: ' . $e->getMessage();
			die();
		}
	}

	public function __destruct() {
		$this->dbh = null;
	}

	public function updateRow($table, $primary_key, $data) {
		$params = array();
		$update_cols = array();
		foreach($data as $key => $value) {
			$update_cols[] = $key.' = :'.$key;
			$params[':'.$key] = $value;
		}

		$where_cols = array();
		foreach($primary_key as $key => $value) {
			$where_cols[] = $key.' = :'.$key;
			$params[':'.$key] = $value;
		}

		$stmt = $this->dbh->prepare('UPDATE '.$table.' SET '.implode(', ', $update_cols).' WHERE '.implode(' AND ', $where_cols));

		foreach($params as $key => &$value) {
			$stmt->bindParam($key, $value);
		}

		$stmt->execute();

		$err = $stmt->errorInfo();
		if($DEV && count($err) > 1) {
			return array('table' => $table, 'primary_key' => $primary_key, 'data' => $data, 'params' => $params, 'SQL' => 'UPDATE '.$table.' SET '.implode(', ', $update_cols).' WHERE '.implode(' AND ', $where_cols), 'error' => $err);
		}
		else {
			return true;
		}
	}

	public function insertRow($table, $data) {
		$params = array();
		$insert_cols = array();
		foreach($data as $key => $value) {
			$insert_cols[] = $key.' = :'.$key;
			$params[':'.$key] = $value;
		}

		$stmt = $this->dbh->prepare('INSERT INTO '.$table.' SET '.implode(', ', $insert_cols));
		$stmt->execute($params);
		return $this->dbh->lastInsertId();
	}
}

?>
