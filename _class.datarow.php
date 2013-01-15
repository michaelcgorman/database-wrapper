<?php

class DataRow {
	protected $row;

	public function __construct($data) {
		$this->row = $data;
	}

	public function __destruct() {
		$this->row = null;
	}

	public function __toString() {
		return print_r($this->row, true);
	}
}

?>
