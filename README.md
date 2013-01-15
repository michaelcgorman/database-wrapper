database-wrapper
================

After inheriting a bunch of code using PHP's old-style MySQL interface functions, I stuck with that style for a while. Ready for a change, I'm starting to learn the PDO style *and* trying to class-ify as much of my code as makes sense. So this is what I'm using for database interfactions now.

Example Usage
-------------

```php
class ClassroomDB extends DB {
	public function __construct() {
		$this->dbn = 'Classrooms';
		$this->user = 'USERNAME';
		$this->pass = 'PASSWORD';
		parent::__construct();
	}
	
	public function getAllRooms() {
		$stmt = $this->dbh->prepare('SELECT * FROM Classrooms');
		$stmt->execute();
		
		$result = array();
		while($row = $stmt->fetch()) {
			$result[] = new Classroom($row);
		}
		return $result;
	}
	
	public function getRooms($criteria = array()) {
		$where = array();
		$params = array();
		
		foreach($criteria as $column => $value) {
			switch($column) {
				case 'Seats':
					$where[] = $column . ' >= ?';
					$params[] = $value;
					break;
				case 'Building':
				case 'Room':
				case 'Computer':
					$where[] = $column . ' = ?';
					$params[] = $value;
			}
		}
		
		$stmt = $this->dbh->prepare('SELECT * FROM Classrooms'.((count($params) > 0) ? ' WHERE ' . join(' AND ', $where) : ''));
		$stmt->execute($params);
		
		$result = array();
		while($row = $stmt->fetch()) {
			$result[] = new Classroom($row);
		}
		return $result;
	}
	
	public function updateRow($Recno, $data) {
		return parent::updateRow('Classrooms', array('Recno' => $Recno), $data);
	}

	public function insertRow($data) {
		return parent::insertRow('Classrooms', $data);
	}
}
```

```php
class Classroom extends DataRow {
	public function __toString() {
		ob_start();
		?>
		<h1><?php echo $this->row['Building']; ?> <?php echo $this->row['Room']; ?></h1>
		...
		<?php
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}
}
```
