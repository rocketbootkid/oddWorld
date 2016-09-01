<html>

<head>
<title>OddWorld - World</title>
</head>

<body>

<?php

	include('mysql_functions.php');
	include('grid_functions.php');
	include('log_functions.php');
	
	#generateNewGrid("world", 50, "world10");
	
	if (isset($_GET['action']) && isset($_GET['square'])) {
		if ($_GET['action'] == 'clear') {
			clearForest($_GET['square']);
		} elseif ($_GET['action'] == 'farm') {
			createFarm($_GET['square']);
		} elseif ($_GET['action'] == 'mine') {
			createMine($_GET['square']);
		}
	}	
	
	if (isset($_GET['world'])) {
		drawGrid($_GET['world']);
	} else {
		echo "Select a world to display.";
	}
	
	echo featureList($_GET['world']);
	
	displayLog();

?>

</body>

</html>