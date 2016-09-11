<?php

function worldList() {
	
	# Display Lists of World Grids
	$sql = "SELECT * FROM oddworld.grid ORDER BY grid_id ASC;";
	$results = doSearch($sql);
	
	$text = "";
	
	$text = "<table cellpadding=4 cellspacing=1 border=1>";
	
	foreach ($results as $world) {
		
		$text = $text . "<tr><td align=center>" . $world['grid_id'] . "<td><a href='world.php?world=" . $world['grid_id'] . "'>" . $world['grid_name'] . " (" . $world['grid_size'] . ")</tr>";
		
	}

	# Link to generate new World grid
	$text = $text . "<tr><td colspan=2><a href='select.php?create'>Create New World</a></tr>";

	$text = $text . "</table>";	
	
	return $text;
	
}

function generateNewGrid() {
	
	# This function will generate a new grid, returns grid_id
	writeLog("generateNewGrid()");
	
	$name = generateGridName();
	$type = 'world';
	$size = 50;
	
	# Create Grid Record
	$dml = "INSERT INTO oddworld.grid (grid_type, grid_size, grid_name, grid_money) VALUES ('" . $type . "', " . $size . ", '" . $name . "', 500);";
	$status = doInsert($dml);
	
	# Get Grid ID
	$sql = "SELECT grid_id FROM oddworld.grid ORDER BY grid_id DESC LIMIT 1;";
	$results = doSearch($sql);
	$grid_id = $results[0]['grid_id'];
	writeLog("generateNewGrid(): Grid ID: " . $grid_id);
		
	# Generate Grid (passing grid_id)
	createGrid($grid_id, $size);
	
	# Create New Grid Directory
	mkdir("logs/World" . $grid_id, 0700);
	
	# Create Tick File
	$tickfile = fopen("logs/World" . $grid_id . "/tick.txt", "w");
	$text = "0\n";
	fwrite($tickfile, $text);
	fclose($tickfile);
	
	# Create Price Log File
	$pricelog = fopen("logs/World" . $grid_id . "/prices.log", "w");
	$text = "wool,corn,milk,beef,wheat,potatoes,iron,coal,gold,silver,copper,tin\n";
	fwrite($pricelog, $text);
	fclose($pricelog);
	
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

function generateGridName() {
	
	#HEAD:Generates a feature name
	writeLog("generateGridName()");
	
	srand();
	$syllables = rand(1,2);
	$name = "";
	
	for ($s = 0; $s < $syllables; $s++) {
		
		$consonant = "a";
		while ($consonant == "a" || $consonant == "e"|| $consonant == "i" || $consonant == "o" || $consonant == "u") {
			$consonant = chr(rand(97, 122));
		}
		
		$vowel = "x";
		while ($vowel != "a" && $vowel != "e" && $vowel != "i" && $vowel != "o" && $vowel != "u") {
			$vowel = chr(rand(97, 122));
		}

		$vowel2 = "x";
		while ($vowel2 != "a" && $vowel2 != "e" && $vowel2 != "i" && $vowel2 != "o" && $vowel2 != "u") {
			$vowel2 = chr(rand(97, 122));
		}
		
		$name = $name . $consonant . $vowel . $vowel2;
		$consonant = "a";
		$vowel = "x";
		
	}
	
	$name = $name . "ria";
	
	writeLog("generateGridName(): Name: " . $name);
	
	return ucwords($name);	
	
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
		
		if ($square['square_type'] == 'forest') { # Forest
			echo "<td><a href='world.php?world=" . $_GET['world'] . "&action=clear&square=" . $square['square_id'] . "'><img src='../images/" . $square['square_type'] . ".png' title='ACTION: Clear Forest' width=25px height=25px></a>";
		
		} elseif ($square['square_type'] == 'land') { # Land
			echo "<td><a href='feature.php?world=" . $_GET['world'] . "&type=land&square=" . $square['square_id'] . "'><img src='../images/" . $square['square_type'] . ".png' title='ACTION: Create Farm' width=25px height=25px></a>";
		
		} elseif ($square['square_type'] == 'farm') { # Farm
			$variant = getFeatureVariant($square['square_id']);
			echo "<td><a href='feature.php?world=" . $_GET['world'] . "&type=farm&square=" . $square['square_id'] . "'><img src='../images/" . $variant . ".png' title='ACTION: View Farm' width=25px height=25px></a>";
		
		} elseif ($square['square_type'] == 'mountain') { # Mountain
			echo "<td><a href='feature.php?world=" . $_GET['world'] . "&type=mountain&square=" . $square['square_id'] . "'><img src='../images/" . $square['square_type'] . ".png' title='ACTION: Create Mine' width=25px height=25px></a>";

		} elseif ($square['square_type'] == 'mine') { # Mine
			$variant = getFeatureVariant($square['square_id']);
			echo "<td><a href='feature.php?world=" . $_GET['world'] . "&type=mine&square=" . $square['square_id'] . "'><img src='../images/" . $variant . ".png' title='ACTION: View Mine' width=25px height=25px></a>";
		
		} elseif ($square['square_type'] == 'town') { # Town
			echo "<td><a href='feature.php?world=" . $_GET['world'] . "&type=town&square=" . $square['square_id'] . "'><img src='../images/" . $square['square_type'] . ".png' title='ACTION: View Town' width=25px height=25px></a>";
		
		} elseif ($square['square_type'] == 'sea') { # Sea
			echo "<td><a href='feature.php?world=" . $_GET['world'] . "&type=sea&square=" . $square['square_id'] . "'><img src='../images/" . $square['square_type'] . ".png' title='ACTION: View Sea' width=25px height=25px></a>";
		
		} else { # Nothing special
			echo "<td><a href=''><img src='../images/" . $square['square_type'] . ".png' title='" . $square['square_x'] . "," . $square['square_y'] . ": " . $square['square_type'] . "'></a>";
		}

		if ($square['square_y'] == $columns) {
			echo "</tr>";
		}			
		
	}

	echo "</table>";
	
}

function getSquareAttribute($square_id, $attribute) {
	
	# This function will return the attribute of the provided square
	writeLog("getSquareAttribute()");	
	
	$sql = "SELECT " . $attribute . " FROM oddworld.square WHERE square_id = " . $square_id . ";";
	$results = doSearch($sql);
	
	return $results[0][$attribute];	
	
}

function getSquareType($square_id) {
	
	# This function will return the type of the provided square
	writeLog("getSquareType(): Square ID: " . $square_id);	
		
	return getSquareAttribute($square_id, 'square_type');
	
}

function getSquareCoordinates($square_id) {
	
	# This function will return the coordinates of the provided square
	writeLog("getSquareCoordinates(): Square ID: " . $square_id);	
	$arrCoords['x'] = getSquareAttribute($square_id, 'square_x');
	$arrCoords['y'] = getSquareAttribute($square_id, 'square_y');
		
	return $arrCoords;
	
}

function getCurrentFunds($world_id) {

	# This function will return the current funds for the world
	writeLog("getCurrentFunds()");	
	
	$sql = "SELECT grid_money FROM oddworld.grid WHERE grid_id = " . $world_id . ";";
	$results = doSearch($sql);
	
	return $results[0]['grid_money'];

}

function addFunds($world_id, $amount) {
	
	# This function will adds funds to the world
	writeLog("addFunds()");
	
	$dml = "UPDATE oddworld.grid SET grid_money = grid_money + " . $amount . " WHERE grid_id = " . $world_id . ";";
	$status = doInsert($dml);
	
}

function getFeatureVariant($square_id) {

	# This function will return the current funds for the world
	writeLog("getFeatureVariant()");	
	
	$sql = "SELECT feature_variant FROM oddworld.feature WHERE square_id = " . $square_id . ";";
	$results = doSearch($sql);
	
	return $results[0]['feature_variant'];	
		
}

function getCurrentTickFromFile($world_id) {
	
	return file_get_contents('logs/World' . $world_id . '/tick.txt');
	
}

function getAdjacentSquares($square_id, $grid_id) {

	# This function will return an array of squares adjacent to the provided square
	writeLog("getAdjacentSquares()");	
	
	$coords = getSquareCoordinates($square_id);
	
	$x_min = $coords['x'] - 1;
	$x_max = $coords['x'] + 1;
	$y_min = $coords['y'] - 1;
	$y_max = $coords['y'] + 1;
	
	$sql = "SELECT square_id FROM oddworld.square WHERE (square_x >= " . $x_min . " AND square_x <= " . $x_max . ") AND (square_y >= " . $y_min . " AND square_y <= " . $y_max . ") AND square_id != " . $square_id . " AND grid_id = " . $grid_id . ";";
	$results = doSearch($sql);
	writeLog("getAdjacentSquares(): Squares: " . count($results));
	
	return $results;
	
}


?>