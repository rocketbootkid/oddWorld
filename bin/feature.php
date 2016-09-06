<html>

<head>
<title>OddWorld - Feature</title>
</head>

<body>

<?php

	include('functions/mysql_functions.php');
	include('functions/grid_functions.php');
	include('functions/feature_functions.php');
	include('functions/log_functions.php');
	
	if (isset($_GET['world']) && isset($_GET['type']) && isset($_GET['square'])) {
		$world_id = $_GET['world'];
		$square_type = $_GET['type'];
		$square_id = $_GET['square'];
		
		switch ($square_type) {
			case "forest":
				clearForest($square_id, $world_id);
				break;
			case "land":
			
				break;
			case "farm":
				# Determine if the farm variant needs to be selected
				if (isset($_GET['choice'])) {
					chooseFarmType($_GET['square'], $_GET['world'], $_GET['choice']);
				}
				echo displayFarm($_GET['square'], $_GET['world']);
				break;
			case "town":
			
				break;
			case "mountain":
				createMine($square_id, $world_id);
				echo displayMine($_GET['square'], $_GET['world']);
				break;
			case "mine":
				echo displayMine($_GET['square'], $_GET['world']);
				break;		
		}

		displayLog();
	
	}	

?>

</body>

</html>