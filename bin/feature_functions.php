<?php

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

function createMine($square_id) {
	
	# This function will create a mine
	writeLog("createMine(): Square ID: " . $square_id);
	
	$square_type = getSquareType($square_id);
	writeLog("createMine(): Square Type: " . $square_type);
	
	if ($square_type == "mountain") {
	
		$dml = "UPDATE oddworld.square SET square_type = 'mine' WHERE square_id = " . $square_id . ";";
		$status = doInsert($dml);
		if ($status == TRUE) {
			writeLog("createMine(): mine created!");
		} else {
			writeLog("createMine(): ERROR: mine not created!");
		}
			
	} else {
		writeLog("createMine(): ERROR: Square ID " . $square_id . " is not a mountain square!");
	}	
	
	# Create mine feature
	$feature_name = generateFeatureName('mine');
	$arrVariants = array('Gold', 'Silver', 'Coal', 'Iron', 'Copper', 'Tin');
	srand();
	$variant = $arrVariants[rand(0, 5)];
	$feature_size = rand(200, 800);
		
	$dml = "INSERT INTO oddworld.feature (feature_type, feature_name, square_id, feature_variant, feature_size) VALUES ('mine', '" . $feature_name . "', " . $square_id . ", '" . $variant . "', " . $feature_size . ");";
	$status = doInsert($dml);
	if ($status == TRUE) {
		writeLog("createMine(): mine feature created!");
	} else {
		writeLog("createMine(): ERROR: mine feature not created!");
	}
	
}

function displayMine($square_id, $world_id) {

	#HEAD:Displays mine information
	
	writeLog("displayMine()");
	$text = "";
	
	$sql = "SELECT * FROM oddworld.feature WHERE square_id = " . $square_id . ";";
	$results = doSearch($sql);
	
	$text = $text . "<table cellpadding=3 cellspacing=1 border=1 align=center>";
	$text = $text . "<tr><td colspan=2 align=center><img src='../images/" . $results[0]['feature_type'] . ".png' width=50px height=50px></tr>";
	$text = $text . "<tr><td colspan=2 align=center><h2>" . $results[0]['feature_name'] . "</h2></tr>";
	$text = $text . "<tr><td>Type<td>" . $results[0]['feature_variant'] . "</tr>";
	$text = $text . "<tr><td>Remaining<td>" . $results[0]['feature_size'] . "</tr>";
	$text = $text . "<tr><td colspan=2 align=center><a href='world.php?world=" . $world_id . "'>Back</a>";
	$text = $text . "</table>";

	return $text;
	
}

function displayFarm($square_id, $world_id) {

	#HEAD:Displays farm information
	
	writeLog("displayFarm()");
	$text = "";
	
	$sql = "SELECT * FROM oddworld.feature WHERE square_id = " . $square_id . ";";
	$results = doSearch($sql);
	
	$text = $text . "<table cellpadding=3 cellspacing=1 border=1 align=center>";
	$text = $text . "<tr><td colspan=2 align=center><img src='../images/" . $results[0]['feature_type'] . ".png' width=50px height=50px></tr>";
	$text = $text . "<tr><td colspan=2 align=center><h2>" . $results[0]['feature_name'] . "</h2></tr>";
	$text = $text . "<tr><td>Type<td>" . $results[0]['feature_variant'] . "</tr>";
	$text = $text . "<tr><td>Income<td>" . $results[0]['feature_size'] . "</tr>";
	$text = $text . "<tr><td colspan=2 align=center><a href='world.php?world=" . $world_id . "'>Back</a>";
	$text = $text . "</table>";

	return $text;
	
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
	
	$name = $name . " " . $feature_type;
	
	writeLog("generateFeatureName(): Name: " . $name);
	
	return ucwords($name);
	
}

function getFeatureName($square_id) {
	
	# This function will return the name of the feature at the provided square
	writeLog("getFeatureName(): Square ID: " . $square_id);	
	
	$sql = "SELECT feature_name FROM oddworld.feature WHERE square_id = " . $square_id . ";";
	$results = doSearch($sql);
	
	return $results[0]['feature_name'];	
		
}

function featureList($world_id) {
	
	# This function will return the name of the feature at the provided square
	writeLog("featureList()");	
	
	$text = "";
	
	$sql = "SELECT feature_name, feature.square_id, square_x, square_y, feature_type, feature_variant FROM oddworld.feature, oddworld.square, oddworld.grid WHERE feature.square_id = square.square_id AND square.grid_id = grid.grid_id AND grid.grid_id = " . $world_id . ";";
	$results = doSearch($sql);
	
	if (count($results) > 0) {
		
		$text = "<table cellpadding=3 cellspacing=0 border=1>";
		
		foreach ($results as $feature) {		
			$text = $text . "<tr><td><a href='" . $feature['feature_type'] . ".php?world=" . $world_id . "&square=" . $feature['square_id'] . "'>" . $feature['feature_name'] . "</a> [" . $feature['feature_variant'] . "] (" . $feature['square_x'] . ":" . $feature['square_y'] . ")</tr>";		
		}
		
		$text = $text . "</table>";
		
	} else {
		$text = "There are no features to display.";
	}
	
	return $text;
	
}

?>