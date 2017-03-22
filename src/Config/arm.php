<?php

return [

	/*
	************************************************************************
	 ********************** tablesArray for Arm Class *********************
	  ********************************************************************  
	  
	*************************** to use it in php ***************************
	* $query = mysqli_query($handle, "INSERT INTO ".$this->_tablesArray['users']['tablename']." VALUES('1', '2', 'etc')";
	* $query = mysqli_query($handle, "SELECT * FROM ".$this->_tablesArray['users']['tablename']." WHERE ".$this->_tablesArray['users']['id']."='3'");
	* $query = mysqli_query($handle, "DELETE FROM ".$this->_tablesArray['users']['tablename']);
	*
	* Neat thing about using it this way is you can change the names and create better security without having to go through all of your code..
	*/ 
	'tables' => [
		'users' => [
			'tablename'=>'users_c3po007r2d2', 'id'=>'userid_c3po007r2d2', 'created_at'=>'createdat_c3po007r2d2', 'name'=>'name_c3po007r2d2_VARCHAR_255', 'email'=>'email_c3po007r2d2_VARCHAR_255', 'password'=>'password_c3po007r2d2_VARCHAR_255'
		]
	]
];