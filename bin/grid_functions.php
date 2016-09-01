<?php

function generateNewGrid($type, $size, $name) {
	
	# This function will generate a new grid, returns grid_id
	writeLog("generateNewGrid()");
	
	# Create Grid Record
	$dml = "INSERT INTO oddworld.grid (grid_type, grid_size, grid_name) VALUES ('" . $type . "', " . $size . ", '" . $name . "');";
	$status = doInsert($dml);
	
	# Get Grid ID
	$sql = "SELECT grid_id FROM oddworld.grid ORDER BY grid_id DESC LIMIT 1;";
	$results = doSearch($sql);
	$grid_id = $results[0]['grid_id'];
	writeLog("generateNewGrid(): Grid ID: " . $grid_id);
		
	# Generate Grid (passing grid_id)
	createGrid($grid_id, $size);
	
	drawGrid($grid_id);
	
}

function createGrid($grid_id, $size) {
	
	# This function will generate the actual grid squares
	writeLog("createGrid()");
	
	for ($x = 0; $x < $size; $x++) {
		for ($y = 0; $y < $size; $y++) {
		
			# Determine what sort of square it is
			$type = determineSquareType($grid_id, $x, $y, $size);
		
			# Write the square information to the database
			$dml = "INSERT INTO oddworld.square (grid_id, square_x, square_y, square_type) VALUES (" . $grid_id . ", " . $x . ", " . $y . ", '" . $type . "');";
			$status = doInsert($dml);
					
		}
	}
	
}

function determineSquareType($grid_id, $x, $y, $size) {
	
	# This function will determine what the square type is (town, farm, mountain, sea, river, forest, etc)
	
	# Ways to determine what is generated
	# Square coordinate: round the outside (outer 10%) the chance of sea is high, Inside that chance is zero
	# Neighbours: if neighbour is mountain, river, or forest, chance of being that again is increased
	# Basically regenerating thresholds every time
	
	# First, by zone
	$min = $size/10;
	$max = $size - $min;
	if ($x < $min || $x >= $max || $y < $min || $y >= $max) { # sea
		srand();
		if (rand(1, 10) > 1) {
			$type = "sea";
		} else {
			# Determine if it's gonna be mountain or forest
			srand();
			$choice = rand(1, 8);
			if ($choice >= 3) { # forest
				$type = "forest";
			} else {
				$type = "mountain";
			}
		}
	} else { # land
		# Determine if it's gonna be mountain, forest, town or farm
		srand();
		$choice = rand(1, 25);
		if ($choice <= 3) { # lake
			$type = "sea";
		} elseif ($choice >= 4 && $choice < 20) {
			$type = "forest";
		} else {
			$type = "mountain";
		}
	}
	
	return $type;
		
}

function drawGrid($grid_id) {
	
	# This function will produce and return an existing grid for display
	writeLog("drawGrid(): Grid ID: " . $grid_id);

	$sql = "SELECT * FROM oddworld.square WHERE grid_id = " . $grid_id . ";";
	$results = doSearch($sql);
	
	echo "<table cellpadding=0 cellspacing=0 border=0 align=center>";

	$columns = sqrt(count($results)) - 1;
	writeLog("drawGrid(): Grid Size: " . sqrt(count($results)));
	
	foreach ($results as $square) {
		
		if ($square['square_y'] == 0) {
			echo "<tr>";
		}
		
		# Forest
		if ($square['square_type'] == 'forest') {
			echo "<td><a href='world.php?world=" . $_GET['world'] . "&action=clear&square=" . $square['square_id'] . "'><img src='../images/" . $square['square_type'] . ".png' title='ACTION: Clear Forest'></a>";
		} elseif ($square['square_type'] == 'land') {
			echo "<td><a href='world.php?world=" . $_GET['world'] . "&action=farm&square=" . $square['square_id'] . "'><img src='../images/" . $square['square_type'] . ".png' title='ACTION: Create Farm'></a>";
		} elseif ($square['square_type'] == 'farm') {
			$farm_name = getFeatureName($square['square_id']);
			echo "<td><a href=''><img src='../images/" . $square['square_type'] . ".png' title='" . $farm_name . "'></a>";
		} else {
			echo "<td><a href=''><img src='../images/" . $square['square_type'] . ".png' title='" . $square['square_x'] . "," . $square['square_y'] . ": " . $square['square_type'] . "'></a>";
		}
				
		
		if ($square['square_y'] == $columns) {
			echo "</tr>";
		}			
		
	}

	echo "</table>";
	
}

function clearForest($square_id) {
	
	# This function will clear a forest square
	writeLog("clearForest(): Square ID: " . $square_id);
	
	$square_type = getSquareType($square_id);
	writeLog("clearForest(): Square Type: " . $square_type);
	
	if ($square_type == "forest") {
	
		$dml = "UPDATE oddworld.square SET square_type = 'land' WHERE square_id = " . $square_id . ";";
		$status = doInsert($dml);
		if ($status == TRUE) {
			writeLog("clearForest(): Square ID " . $square_id . " cleared!");
		} else {
			writeLog("clearForest(): ERROR: Square ID " . $square_id . " has not been cleared!");
		}
			
	} else {
		writeLog("clearForest(): ERROR: Square ID " . $square_id . " is not a forest square!");
	}	
	
}

function createFarm($square_id) {
	
	# This function will create a farm
	writeLog("createFarm(): Square ID: " . $square_id);
	
	$square_type = getSquareType($square_id);
	writeLog("createFarm(): Square Type: " . $square_type);
	
	if ($square_type == "land") {
	
		$dml = "UPDATE oddworld.square SET square_type = 'farm' WHERE square_id = " . $square_id . ";";
		$status = doInsert($dml);
		if ($status == TRUE) {
			writeLog("createFarm(): Farm created!");
		} else {
			writeLog("createFarm(): ERROR: Farm not created!");
		}
			
	} else {
		writeLog("createFarm(): ERROR: Square ID " . $square_id . " is not a land square!");
	}	
	
	# Create farm feature
	$feature_name = generateFeatureName('farm');
	$dml = "INSERT INTO oddworld.feature (feature_type, feature_name, square_id) VALUES ('farm', '" . $feature_name . "', " . $square_id . ");";
	$status = doInsert($dml);
	if ($status == TRUE) {
		writeLog("createFarm(): Farm feature created!");
	} else {
		writeLog("createFarm(): ERROR: Farm feature not created!");
	}
	
}

function generateFeatureName($feature_type) {
	
	#HEAD:Generates a feature name
	
	writeLog("generateFeatureName()");
	
	srand();
	$syllables = rand(2,4);
	writeLog("generateFeatureName(): Type: " . $feature_type);
	$name = "";
	
	for ($s = 0; $s < $syllables; $s++) {
		
		$consonant = "a";
		while ($consonant == "a" || $consonant == "e"|| $consonant == "i" || $consonant == "o" || $consonant == "u") {
			$consonant = chr(rand(97, 122));
		}
		writeLog("generateFeatureName(): Consonant: " . $consonant);
		
		$vowel = "x";
		while ($vowel != "a" && $vowel != "e"&& $vowel != "i" && $vowel != "o" && $vowel != "u") {
			$vowel = chr(rand(97, 122));
		}
		writeLog("generateFeatureName(): Vowel: " . $vowel);
		
		$name = $name . $consonant . $vowel;
		$consonant = "a";
		$vowel = "x";
		
	}
	
	$name = $name . " Farm";
	
	writeLog("generateFeatureName(): Name: " . $name);
	
	return ucwords($name);
	
}

function getSquareType($square_id) {
	
	# This function will return the type of the provided square
	writeLog("getSquareType(): Square ID: " . $square_id);	
	
	$sql = "SELECT square_type FROM oddworld.square WHERE square_id = " . $square_id . ";";
	$results = doSearch($sql);
	
	return $results[0]['square_type'];
	
}

function getFeatureName($square_id) {
	
	# This function will return the name of the feature at the provided square
	writeLog("getFeatureName(): Square ID: " . $square_id);	
	
	$sql = "SELECT feature_name FROM oddworld.feature WHERE square_id = " . $square_id . ";";
	$results = doSearch($sql);
	
	return $results[0]['feature_name'];	
		
}


?>