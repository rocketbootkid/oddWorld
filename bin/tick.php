<?php

	include('mysql_functions.php');
	include('grid_functions.php');
	include('feature_functions.php');
	include('log_functions.php');
	include('tick_functions.php');
	
	# Read / update / write new tick
	tick();
	
	# Handle economic changes	
	$arrPrices = generatePrices();
	
	
	
	
	
	# Handle random disasters that affect feature productivity
	
	# Handle increase of town population
	
	# Handle decrease of mine remaining

	
	
	# Handle income from remaining / working features
	
	
	
	
	
	
	
	
	
	
	displayLog();
	
	#header('Refresh: 10; URL=tick.php');

?>