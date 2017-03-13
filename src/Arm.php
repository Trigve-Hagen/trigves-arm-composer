<?php
namespace Trigves\Arm;

class Arm {
	private $_db_host, $_db_name, $_db_user, $_db_pass;
	
	public function __construct() {
		$this->_db_host = env('DB_HOST');
		$this->_db_name = env('DB_DATABASE');
		$this->_db_user = env('DB_USERNAME');
		$this->_db_pass = env('DB_PASSWORD');
	}
	
	/*
	* I take no responsibility for lost data! Use at your risk.
	* tablename and id INT NOT NULL AUTO_INCREMENT are manditory unless you want to adjust it
	* You can name the sections separated by the underscore anything - 'id'=>'blogid_45g234y5g5y' or 'id'=>'userid_rewquy3o45ouy'
	* The next two fields after tablename and id are optional. They are
	* create_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP - 'created_at'=>'createdat_c3po007r2d2'
	* and updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP - 'updated_at'=>'updatedat_c3po007r2d2'
	* ids don't have INT or characters on the end and niether do created_at or updated_at. They are created individually.
	* If you want a TEXT assign it TEXT with no characters - metta_c3po007r2d2_TEXT
	* Others must have a number of characters assigned like this rate_c3po007r2d2_VARCHAR_255 - 4 section divided by underscores
	* You can update multiple rows in a table and make several databases at a time and combine the two
	* You can add rows anywhere you want after updated_at. Will work on improvements to that soon. For now if you are unsure
	* build your database with create_at and updated_at in place. create_at keeps first insert. updated_at updates when updated.
	* added support for adding multiple rows in a table that are next to each other.
	* added support for droping unneeded tables - just erase them from the array.
	* For a ton of changes(erase two tables, add three rows to each table while erasing four rows in each table, create another table)
	* you may have to refresh twice, but it beats going back and forth.
	*/
	private $_tablesArray = array(
		'users' => array('tablename'=>'users_c3po007r2d2', 'id'=>'userid_c3po007r2d2', 'created_at'=>'createdat_c3po007r2d2', 'name'=>'name_c3po007r2d2_VARCHAR_255', 'phone'=>'phone_c3po007r2d2_VARCHAR_255', 'username'=>'username_c3po007r2d2_VARCHAR_255', 'email'=>'email_c3po007r2d2_VARCHAR_255', 'username'=>'username_c3po007r2d2_VARCHAR_255', 'password'=>'password_c3po007r2d2_VARCHAR_255'),
		'userdata' => array('tablename'=>'userdata_c3po007r2d2', 'id'=>'userid_c3po007r2d2', 'created_at'=>'createdat_c3po007r2d2', 'name'=>'name_c3po007r2d2_VARCHAR_255', 'phone1'=>'phone1_c3po007r2d2_VARCHAR_255', 'username'=>'username_c3po007r2d2_VARCHAR_255', 'email'=>'email_c3po007r2d2_VARCHAR_255', 'username'=>'username_c3po007r2d2_VARCHAR_255', 'password'=>'password_c3po007r2d2_VARCHAR_255'),
		'posts' => array('tablename'=>'posts_posts07rsecret2d2', 'id'=>'postid_posts07rsecret2d2', 'created_at'=>'created_posts07rsecret2d2', 'updated_at'=>'updated_posts07rsecret2d2', 'userid'=>'userid_related2Cusers2Cid_INT_255', 'posttitle'=>'title_posts07rsecret2d2_VARCHAR_255', 'postbody'=>'post_posts07rsecret2d2_VARCHAR_255')/* ,
		'comments' => array('tablename'=>'comments_posts07rsecret2d2', 'id'=>'commentid_posts07rsecret2d2', 'created_at'=>'created_posts07rsecret2d2', 'updated_at'=>'updated_posts07rsecret2d2', 'userid'=>'userid_related2Cusers2Cid_INT_255', 'postid'=>'postid_related2Cusers2Cid_INT_255', 'comment'=>'comment_posts07rsecret2d2_VARCHAR_255') */
	);
	
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
	
	public static function saySomething() {
        return 'Hello World!';
    }
}

?>