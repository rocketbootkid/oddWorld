<?php

$log = "";

function writeLog($message) {
	
	#HEAD:Writes provided message to the log global
	
	$GLOBALS['log'] = $GLOBALS['log'] . "\n<br/>" . date('Y-m-d H:i:s') . ": " . $message;
	
}

function displayLog() {
	
	#HEAD:Displays the contents of the log global
	
	echo "<p>Debug Log<hr>RDBMS Calls: " . $GLOBALS['calls'] . "<hr>" . $GLOBALS['log'];
	
}

function writeOperationToLog($command, $duration) {
	
	#HEAD:Log database command information to a logfile
	
	$file = fopen('../log/db_log.log', 'a');
	$text = date('Y-m-d H:i:s') . ": " . $command . " | " . $duration . "\n";
	fwrite($file, $text);
	fclose($file);
	
}

?>