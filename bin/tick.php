<?php

	include('functions/mysql_functions.php');
	include('functions/grid_functions.php');
	include('functions/feature_functions.php');
	include('functions/log_functions.php');
	include('functions/tick_functions.php');
	
	if (isset($_GET['world']) || isset($_COOKIE['world'])) {
		
		if (isset($_GET['world'])) {
			$world_id = $_GET['world'];
		} else {
			$world_id = $_COOKIE['world'];
		}
		setcookie('world', $world_id);
		writeLog("tick.php: World ID: " . $world_id);
		
		# Read / update / write new tick
		tick();
		
		# Handle economic changes	
		$arrPrices = generatePrices();
		logPrices($arrPrices); # Log prices to CSV
		
		# Handle farm maturity
		# Handle decrease of mine remaining
		# Handle increase of town population
		manageFeatureLife($world_id);
				
		# Handle random disasters that affect feature productivity
		manageDisasters($world_id);
		
		# Handle income from remaining / working features
		manageEconomy($arrPrices, $world_id);
		
	} else {
		echo "Select World.";
	}
	
	#displayLog();
	
	header('Refresh: 5; URL=tick.php');

?>