<?php

	include('functions/mysql_functions.php');
	include('functions/grid_functions.php');
	include('functions/feature_functions.php');
	include('functions/log_functions.php');
	
	# Found Town
	if (isset($_GET['found'])) {
		$world_id = $_GET['world'];
		$square_id = $_GET['square'];
		createTown($world_id, $square_id);
		$url = "towns.php?world=" . $world_id;
		header("Location: " . $url);
	}
	
	# Choose Farm
	if (isset($_GET['choice'])) {
		$world_id = $_GET['world'];
		$square_id = $_GET['square'];
		$choice = $_GET['choice'];
		createFarm($square_id, $world_id);
		chooseFarmType($square_id, $world_id, $choice);
		$url = "farms.php?world=" . $world_id;
		header("Location: " . $url);
	}
	
	# Fill Water
	if (isset($_GET['option']) && $_GET['option'] == "land") {
		$world_id = $_GET['world'];
		$square_id = $_GET['square'];
		clearForest($square_id, $world_id);
		$url = "world.php?world=" . $world_id;		
		header("Location: " . $url);
	}
	
	echo "<html><head><title>OddWorld - Feature</title></head><body>";
	
	if (isset($_GET['world']) && isset($_GET['type']) && isset($_GET['square'])) {
		$world_id = $_GET['world'];
		$square_type = $_GET['type'];
		$square_id = $_GET['square'];
		
		switch ($square_type) {
			case "forest":
				clearForest($square_id, $world_id);
				break;
			case "land":
				# Offer two choices; create farm, or create town
				echo chooseFarmOrTown($world_id, $square_id);
				break;
			case "farm":
				# Determine if the farm variant needs to be selected
				echo displayFarm($_GET['square'], $_GET['world']);
				break;
			case "town":
				echo displayTown($_GET['square'], $_GET['world']);
				break;
			case "mountain":
				$return = createMine($square_id, $world_id);
				if ($return == 1) {
					echo displayMine($_GET['square'], $_GET['world']);
				} else {
					echo "Can't afford it.";
				}
				break;
			case "mine":
				echo displayMine($_GET['square'], $_GET['world']);
				break;		
			case "sea":
				echo chooseSeaAction($world_id, $square_id);
				break;		
		}

		displayLog();
	
	}
	
	echo "</body></html>";

?>