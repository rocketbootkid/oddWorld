<?php

	include('mysql_functions.php');
	include('grid_functions.php');
	include('feature_functions.php');
	include('log_functions.php');
	include('tick_functions.php');
	
	if (isset($_GET['world'])) {
		
		$world_id = $_GET['world'];
		
		# Read / update / write new tick
		tick();
		
		# Handle economic changes	
		$arrPrices = generatePrices();
			
		# Handle random disasters that affect feature productivity
		
		# Handle increase of town population
		
		# Handle decrease of mine remaining

		
		
		# Handle income from remaining / working features
		manageEconomy($arrPrices, $world_id);
		
		
		
		
		
		
		
	} else {
		echo "Select World.";
	}
	
	displayLog();
	
	#header('Refresh: 10; URL=tick.php');

?>