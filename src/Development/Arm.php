<?php
namespace Trigves\Arm\Development;

use Trigves\Arm\ArmServiceProvider;
use Illuminate\Support\ServiceProvider;

class Arm {
	private $_db_host, $_db_name, $_db_user, $_db_pass;
	private $_tablesArray;
	public function __construct() {
		$this->_db_host = env('DB_HOST');
		$this->_db_name = env('DB_DATABASE');
		$this->_db_user = env('DB_USERNAME');
		$this->_db_pass = env('DB_PASSWORD');
		$this->_tablesArray = config('arm.tables');
	}
	
	private function _Connect() {
		return mysqli_connect($this->_db_host, $this->_db_user, $this->_db_pass, $this->_db_name);
	}
	
	private function _ArmCreateNewTable($rowArray) {
		$queryString = "CREATE TABLE IF NOT EXISTS ".$rowArray['tablename']."(";
		if(isset($rowArray['id'])) $queryString .= $rowArray['id']." INT NOT NULL AUTO_INCREMENT, ";
		if(isset($rowArray['created_at'])) $queryString .= $rowArray['created_at']." TIMESTAMP DEFAULT CURRENT_TIMESTAMP, ";
		if(isset($rowArray['updated_at'])) $queryString .= $rowArray['updated_at']." TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
		$rowcount = count($rowArray); $count = 1; 
		foreach($rowArray as $key => $value) {
			if($key == "id" || $key == "tablename" || $key == "created_at" || $key == "updated_at") {  } else {
				$args = explode("_", $value);
				if(isset($args[3])) $chars = "(" . $args[3] . ")"; else $chars = "";
				$queryString .= $value . " " . $args[2] . $chars . ", ";
			} $count++;			
		}
		if(isset($rowArray['id'])) $queryString .= "PRIMARY KEY (".$rowArray['id']."))"; else $queryString .= ")";
		//echo $queryString . "<br />";
		$mysqli = $this->_Connect(); $query = mysqli_query($mysqli, $queryString); mysqli_close($mysqli);
		if($query) echo '<p style="margin-top:50px;text-align:center;color:#006600;">The table '.$rowArray['tablename'].' has been added successfully!</p>';
		else echo '<p style="margin-top:50px;text-align:center;color:#660000;">There has been an error adding '.$rowArray['tablename'].'.</p>';
	}
	
	private function _TableExists($table) {
		$mysqli = $this->_Connect();
		$result = mysqli_query($mysqli, "select 1 from `" . $table . "` LIMIT 1");
		if($result) return true; else return false;
	}
	
	// SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE='BASE TABLE'
	private function _GetListOfTablesDatabase() {
		$mysqli = $this->_Connect(); $argsArray = array();
		$query = mysqli_query($mysqli, "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE='BASE TABLE'");
		while ($line = mysqli_fetch_array($query)) if($line[1] == $this->_db_name) array_push($argsArray, htmlentities($line['TABLE_NAME'])); 
		return $argsArray;
	}
	
	private function _GetListOfTablesArm() {
		$argsArray = array();
		foreach($this->_tablesArray as $table => $rows) array_push($argsArray, $rows['tablename']);
		//echo '<pre>' . print_r($argsArray) . '</pre>';
		return $argsArray;
	}
	private function _GetListOfDatabases($key) {
		$mysqli = $this->_Connect(); $argsArray = array();
		$query = mysqli_query($mysqli, "DESCRIBE ".$this->_tablesArray[$key]['tablename']);
		while ($line = mysqli_fetch_array($query)) array_push($argsArray, htmlentities($line['Field']));
		return $argsArray;
	}
	
	// to set TEXT fields searchable in LIKE %search% on 1and1 hosting......might not be needed on localhost.
	private function _CreateCIMettaFields() {
		$mysqli = $this->_Connect();
		$query = mysqli_query($mysqli, "ALTER TABLE ".$this->_tablesArray['users']['tablename']." MODIFY COLUMN ".$this->_tablesArray['users']['text']." TEXT CHARACTER SET UTF8 COLLATE UTF8_GENERAL_CI");
		$query = mysqli_query($mysqli, "ALTER TABLE ".$this->_tablesArray['users']['tablename']." MODIFY COLUMN ".$this->_tablesArray['users']['metta']." TEXT CHARACTER SET UTF8 COLLATE UTF8_GENERAL_CI");
		mysqli_close($mysqli);
	}
	
	private function _CheckifEmpty($name) {
		$mysqli = $this->_Connect();
		$query = mysqli_query($mysqli, "SELECT * FROM ".$this->_tablesArray[$name]['tablename']);
		if($query == false || mysqli_num_rows($query) == 0) $ifempty = true;
		else $ifempty = false;
		mysqli_close($mysqli);
		return $ifempty;
	}
	
	private function _ResolveDifference($key, $ifmore) {
		if($ifmore == 1) { 
			$databaseArray = $this->_GetListOfDatabases($key); $argsArray = array();
			foreach($this->_tablesArray[$key] as $row => $value) if($row == "tablename") { } else array_push($argsArray, $value);
			$results = array_diff($argsArray, $databaseArray); $queryString = '';
			//echo count($results)."<br />";
			//echo '<pre>'; print_r($databaseArray); print_r($results); echo '</pre>';
			foreach($results as $idx => $element) {
				$args = explode("_", $element);
				if(isset($args[3])) $chars = "(" . $args[3] . ")"; else $chars = "";
				$queryString = "ALTER TABLE ".$this->_tablesArray[$key]['tablename']." ADD ".$element." ".$args[2].$chars." AFTER ".$databaseArray[$idx-1]; $mysqli = $this->_Connect();
				$query = mysqli_query($mysqli, $queryString);
				if($query) echo '<p style="margin-top:50px;text-align:center;color:#006600;">The row '.$element.' has been added successfully!</p>';
				else echo '<p style="margin-top:50px;text-align:center;color:#660000;">There has been an error adding '.$element.'. The most common error is two rows named the same.</p>';
				mysqli_close($mysqli);
				if(count($results) > 1) {
					$databaseArray = $this->_GetListOfDatabases($key); $argsArray = array();
					foreach($this->_tablesArray[$key] as $row => $value) if($row == "tablename") { } else array_push($argsArray, $value);
					$results = array_diff($argsArray, $databaseArray); $queryString = '';
				}
			}
		} else {
			$databaseArray = $this->_GetListOfDatabases($key); $argsArray = array();
			foreach($this->_tablesArray[$key] as $row => $value) if($row == "tablename") { } else array_push($argsArray, $value);
			$results = array_diff($databaseArray, $argsArray);
			//echo '<pre>'; print_r($databaseArray); print_r($argsArray); print_r($results); echo '</pre>';
			foreach($results as $idx => $element) {
				$queryString = "ALTER TABLE ".$this->_tablesArray[$key]['tablename']." DROP ".$element;
				$mysqli = $this->_Connect();
				$query = mysqli_query($mysqli, $queryString);
				if($query) echo '<p style="margin-top:50px;text-align:center;color:#006600;">The row '.$element.' has been dropped successfully!</p>';
				else echo '<p style="margin-top:50px;text-align:center;color:#660000;">There has been an error dropping '.$element.'.</p>';
				mysqli_close($mysqli);
			}
		}
	}
	
	protected function _RenameRow($tablename, $dbArrayVal, $dbVal) {
		$mysqli = $this->_Connect();
		$args = explode("_", $dbArrayVal);
		if(isset($args[2])) $type = $args[2]; else $type = "";
		if(isset($args[3])) $chars = "(" . $args[3] . ")"; else $chars = "";
		$queryString = "ALTER TABLE ".$tablename." CHANGE COLUMN ".$dbVal." ".$dbArrayVal." ".$type.$chars;
		//echo $queryString;
		$query = mysqli_query($mysqli, $queryString);
		if($query) echo '<p style="margin-top:50px;text-align:center;color:#006600;">The row '.$dbVal.' has been changed to '.$dbArrayVal.' successfully!</p>';
		//else echo '<p style="margin-top:50px;text-align:center;color:#660000;">There has been an error changing '.$dbVal.' to '.$dbArrayVal.'.</p>';
		mysqli_close($mysqli);
	}
	
	protected function _CheckForSameName($tablename, $arrayArgs) {
		$count = 0;
		foreach($arrayArgs as $value) {
			//echo $value .", ". $tablename ."<br />";
			if($tablename == $value) $count++;
			if($tablename == $value && $count == 2) {
				echo '<p style="margin-top:50px;text-align:center;color:#f00;">There has been an error adding '.$tablename.'. Two tables can not have the same name.</p>';
				die(); return true;
			}
		}
		return false;
	}
	
	public function ArmCheckTables() {
		$dbArgs = $this->_GetListOfTablesDatabase();
		$arrayArgs = $this->_GetListOfTablesArm();
		//echo '<pre>'; print_r($dbArgs); print_r($arrayArgs); echo '</pre>';
		if(count($dbArgs) == count($arrayArgs) || count($dbArgs) < count($arrayArgs)) {
			foreach($this->_tablesArray as $key => $value) {
				if($this->_TableExists($this->_tablesArray[$key]['tablename'])) {
					if($this->_CheckForSameName($this->_tablesArray[$key]['tablename'], $arrayArgs) == false) {
						$tablename = $this->_tablesArray[$key]['tablename'];
						$mysqli = $this->_Connect(); $query = mysqli_query($mysqli, "SELECT * FROM ".$tablename);
						$numFields = mysqli_num_fields($query); mysqli_close($mysqli);
						//echo $numFields.", ".(count($this->_tablesArray[$key])-1)."<br />";
						if($numFields < (count($this->_tablesArray[$key])-1)) $string = $this->_ResolveDifference($key, 1);
						if($numFields > (count($this->_tablesArray[$key])-1)) $string = $this->_ResolveDifference($key, 0);
						if(isset($string) && $string != '') $this->_ArmAlterTable($tablename, $string);
						
						$databaseArray = $this->_GetListOfDatabases($key); $argsArray = array();
						foreach($this->_tablesArray[$key] as $row => $value) if($row != "tablename") array_push($argsArray, $value);
						for($i=0; $i<count($argsArray); $i++) {
							//echo $argsArray[$i] .", ". $databaseArray[$i] ."<br />";
							if($argsArray[$i] != $databaseArray[$i]) {
								$this->_RenameRow($tablename, $argsArray[$i], $databaseArray[$i]);
							}
						}
						//echo '<pre>'; print_r($databaseArray); print_r($argsArray); echo '</pre>';
					}
				} else {
					$this->_ArmCreateNewTable($this->_tablesArray[$key]);
					//$this->_CreateCIMettaFields();
				}
			}
		} else {
			foreach($dbArgs as $table) { // $arrayArgs - arm array
				if(!in_array($table, $arrayArgs)) {
					$mysqli = $this->_Connect();
					$query = mysqli_query($mysqli, "DROP TABLE IF EXISTS ".$table);
					if($query) echo '<p style="margin-top:50px;text-align:center;color:#006600;">The table '.$table.' has been dropped successfully!</p>';
					else echo '<p style="margin-top:50px;text-align:center;color:#660000;">There has been an error dropping '.$table.'.</p>';
					mysqli_close($mysqli);
				}
			}
			//echo '<pre>'; print_r($dbArgs); print_r($arrayArgs); echo '</pre>';
		}
	}
	
	private function _BackUpToSql() { // escape commas as not to mess up sql statements
		if(file_exists('backup.sql')) unlink('backup.sql');
		foreach($this->_tablesArray as $key => $value) {
			if($this->_TableExists($this->_tablesArray[$key]['tablename'])) {
				$mysqli = $this->_Connect();
				$query = mysqli_query($mysqli, "SELECT * FROM ".$this->_tablesArray[$key]['tablename']);
				while ($line = mysqli_fetch_array($query)) {
					$dbrowcount = 1;
					$string = 'INSERT INTO '.$this->_tablesArray[$key]['tablename'].' VALUES(';
					foreach( $value as $key1 => $value1 ) {
						if($key1 != 'tablename') {
							$value = $line[$this->_tablesArray[$key][$key1]];
							if(preg_match("/,/", $value)) $escaped_value = str_replace(',', '%-2-C-;', $value);
							if($dbrowcount == count($value)-1) $string .= "'".$escaped_value."'";
							else $string .= "'".$escaped_value."', ";
							$dbrowcount++;
						}
					}
					$string .= ");" . PHP_EOL;
					$handle = fopen('backup.sql', 'ab');
					fwrite($handle,$string,strlen($string));
					fclose($handle);
				}
			}
		}
	}
	
	private function _InsertDataFromSql() {
		if(file_exists("backup.sql")) {
			$handle = fopen("backup.sql", "r");
			if($handle) {
				$mysqli = $this->_Connect();
				while(($line = fgets($handle)) !== false) $query = mysqli_query($mysqli, $line);
				mysqli_close($mysqli);
				fclose($handle);
			}
		}
	}
}

?>