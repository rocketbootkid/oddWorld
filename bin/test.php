<?php

	include('functions/mysql_functions.php');
	include('functions/grid_functions.php');
	include('functions/feature_functions.php');
	include('functions/log_functions.php');
	include('functions/tick_functions.php');

	echo countAdjacentFeatures(69065, 'variant', 'beef', 33);

	displayLog();

?>