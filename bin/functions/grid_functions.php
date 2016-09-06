<?php

function generateNewGrid($type, $size, $name) {
	
	# This function will generate a new grid, returns grid_id
	writeLog("generateNewGrid()");
	
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
		
		if ($square['square_type'] == 'forest') { # Forest
			echo "<td><a href='world.php?world=" . $_GET['world'] . "&action=clear&square=" . $square['square_id'] . "'><img src='../images/" . $square['square_type'] . ".png' title='ACTION: Clear Forest'></a>";
		
		} elseif ($square['square_type'] == 'land') { # Land
			echo "<td><a href='feature.php?world=" . $_GET['world'] . "&type=land&square=" . $square['square_id'] . "'><img src='../images/" . $square['square_type'] . ".png' title='ACTION: Create Farm'></a>";
		
		} elseif ($square['square_type'] == 'farm') { # Farm
			echo "<td><a href='feature.php?world=" . $_GET['world'] . "&type=farm&square=" . $square['square_id'] . "'><img src='../images/" . $square['square_type'] . ".png' title='ACTION: View Farm'></a>";
		
		} elseif ($square['square_type'] == 'mountain') { # Mountain
			echo "<td><a href='feature.php?world=" . $_GET['world'] . "&type=mountain&square=" . $square['square_id'] . "'><img src='../images/" . $square['square_type'] . ".png' title='ACTION: Create Mine'></a>";

		} elseif ($square['square_type'] == 'mine') { # Mine
			echo "<td><a href='feature.php?world=" . $_GET['world'] . "&type=mine&square=" . $square['square_id'] . "'><img src='../images/" . $square['square_type'] . ".png' title='ACTION: View Mine'></a>";
		
		} else { # Nothing special
			echo "<td><a href=''><img src='../images/" . $square['square_type'] . ".png' title='" . $square['square_x'] . "," . $square['square_y'] . ": " . $square['square_type'] . "'></a>";
		}

		if ($square['square_y'] == $columns) {
			echo "</tr>";
		}			
		
	}

	echo "</table>";
	
}

function getSquareType($square_id) {
	
	# This function will return the type of the provided square
	writeLog("getSquareType(): Square ID: " . $square_id);	
	
	$sql = "SELECT square_type FROM oddworld.square WHERE square_id = " . $square_id . ";";
	$results = doSearch($sql);
	
	return $results[0]['square_type'];
	
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

?>