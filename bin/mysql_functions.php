<?php

$calls = 0;
$connection = 0;

function connect() {

	#HEAD:Connects to MySQL
	
	$connection = mysqli_connect('localhost', 'root', 'root', 'oddworld');
	if (mysqli_connect_errno()) {
		die("Could not connect: " . mysqli_connect_error());
	}
	
	return $connection;
	
}

function disconnect($connection) {

	#HEAD:Disconnects from MySQL
	
	mysqli_close($connection);

}

function doInsert($dml) {
	
	#HEAD:Does DDL-type operations
	
	$GLOBALS['calls']++;
	
	writeLog("doInsert(): DML: " . $dml);
	$connection = connect();
	
	$status = mysqli_query($connection, $dml);
	
	if ($status == TRUE) {
		writeLog("doInsert(): Insert Successful!");
	} else {
		writeLog("doInsert(): ERROR: Insert Failed!");
	}

	disconnect($connection);

	return $status;
	
}

function doSearch($sql) {

	#HEAD:Does SQL-type operations

	$GLOBALS['calls']++;

	writeLog("doSearch(): SQL: " . $sql);
	$connection = connect();
	
	$result = mysqli_query($connection, $sql);		

	disconnect($connection);

	return mysqli_fetch_all($result, MYSQLI_ASSOC);
	
}

function createDatabase() {
	
	#HEAD:Creates required schema / tables
	
	$connection = mysqli_connect('localhost', 'root', 'root');
	
	$ddl = "CREATE SCHEMA `oddworld`;";
	$status = mysqli_query($connection, $ddl);
	
	$ddl = "USE `oddworld`;";
	$status = mysqli_query($connection, $ddl);

	$ddl = "CREATE TABLE `oddworld`.`grid` (`grid_id` INT NOT NULL AUTO_INCREMENT, `grid_type` VARCHAR(45) NULL, `grid_size` INT NULL, `grid_name` VARCHAR(45) NULL, PRIMARY KEY (`grid_id`));";
	$status = mysqli_query($connection, $ddl);
	
	$ddl = "CREATE TABLE `oddworld`.`square` (`square_id` INT NOT NULL AUTO_INCREMENT, `grid_id` INT NULL, `grid_x` INT NULL, `grid_y` INT NULL, `grid_type` VARCHAR(45) NULL, PRIMARY KEY (`square_id`));";
	$status = mysqli_query($connection, $ddl);
	
	$ddl = "CREATE TABLE `oddworld`.`feature` ( `feature_id` INT NOT NULL AUTO_INCREMENT, `feature_type` VARCHAR(45) NULL, `feature_name` VARCHAR(45) NULL, `square_id` INT NULL, PRIMARY KEY (`feature_id`));";
	$status = mysqli_query($connection, $ddl);
	
	mysqli_close($connection);
	
}
	
?>