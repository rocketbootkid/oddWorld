<?php

$events = "";

function clearForest($square_id, $world_id) {
	
	# This function will clear a forest square
	writeLog("clearForest(): Square ID: " . $square_id);
	
	$square_type = getSquareType($square_id);
	writeLog("clearForest(): Square Type: " . $square_type);
	
	if ($square_type == "forest") {
	
		# Change square type
		$dml = "UPDATE oddworld.square SET square_type = 'land' WHERE square_id = " . $square_id . ";";
		$status = doInsert($dml);

		# Add funds
		addFunds($world_id, 10);
		
		writeEvent("Forest cleared from Square " . $square_id);
		
	} else {
		writeLog("clearForest(): ERROR: Square ID " . $square_id . " is not a forest square!");
	}	
	
}

function createFarm($square_id, $world_id) {
	
	# This function will create a farm
	writeLog("createFarm(): Square ID: " . $square_id);
	
	$square_type = getSquareType($square_id);
	writeLog("createFarm(): Square Type: " . $square_type);
	
	if ($square_type == "land" && canIAffordIt('farm', $world_id) == 1) {
	
		$dml = "UPDATE oddworld.square SET square_type = 'farm' WHERE square_id = " . $square_id . ";";
		$status = doInsert($dml);
		if ($status == TRUE) {
			writeLog("createFarm(): Farm created!");
		} else {
			writeLog("createFarm(): ERROR: Farm not created!");
		}
		
		# Create farm feature
		$feature_name = generateFeatureName('farm');
		$dml = "INSERT INTO oddworld.feature (feature_type, feature_name, square_id, feature_size) VALUES ('farm', '" . $feature_name . "', " . $square_id . ", 0);";
		$status = doInsert($dml);
		if ($status == TRUE) {
			writeLog("createFarm(): Farm feature created!");
		} else {
			writeLog("createFarm(): ERROR: Farm feature not created!");
		}
		
		# Reduce funds based on number of features of this type already in existence
		buyFeature('farm', $world_id);
			
	} else {
		writeLog("createFarm(): ERROR: Not a land square, or can't afford it.");
		writeEvent("Farm not created! You can't afford it :-(");
	}	
	
}

function createMine($square_id, $world_id) {
	
	# This function will create a mine
	writeLog("createMine(): Square ID: " . $square_id);
	
	$square_type = getSquareType($square_id);
	writeLog("createMine(): Square Type: " . $square_type);
	
	if ($square_type == "mountain" && canIAffordIt('mine', $world_id) == 1) {
	
		$dml = "UPDATE oddworld.square SET square_type = 'mine' WHERE square_id = " . $square_id . ";";
		$status = doInsert($dml);
		
		# Create mine feature
		$feature_name = generateFeatureName('mine');
		$arrVariants = array('gold', 'silver', 'coal', 'iron', 'copper', 'tin');
		srand();
		$variant = $arrVariants[rand(0, 5)];
		$feature_size = rand(100, 400);	
		$dml = "INSERT INTO oddworld.feature (feature_type, feature_name, square_id, feature_variant, feature_size) VALUES ('mine', '" . $feature_name . "', " . $square_id . ", '" . $variant . "', " . $feature_size . ");";
		$status = doInsert($dml);
		
		# Reduce funds based on number of features of this type already in existence
		buyFeature('mine', $world_id);
			
	} else {
		writeLog("createMine(): ERROR: Not a mountain square, or can't afford it!");
		writeEvent("Mine not created! You can't afford it :-(");
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
	$text = $text . "<tr><td>Type<td>" . ucwords($results[0]['feature_variant']) . "</tr>";
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
	
	$link = "farm.php?world=" . $world_id . "&square=" . $square_id;
	
	$text = $text . "<table cellpadding=3 cellspacing=1 border=1 align=center>";
	$text = $text . "<tr><td colspan=2 align=center><img src='../images/" . $results[0]['feature_type'] . ".png' width=50px height=50px></tr>";
	$text = $text . "<tr><td colspan=2 align=center><h2>" . $results[0]['feature_name'] . "</h2></tr>";
	
	if ($results[0]['feature_variant'] == "") {
		$text = $text . "<tr><td>Choose Type<td><a href='" . $link . "&choice=wool'>Sheep</a>, <a href='" . $link . "&choice=corn'>Corn</a>, <a href='" . $link . "&choice=milk'>Dairy</a>, <a href='" . $link . "&choice=beef'>Beef</a>, <a href='" . $link . "&choice=wheat'>Wheat</a>, <a href='" . $link . "&choice=potato'>Potatoes</a>";
	} else {
		$text = $text . "<tr><td>Type<td>" . ucwords($results[0]['feature_variant']) . "</tr>";
	}
	
	if ($results[0]['feature_size'] < 20) {
		$remaining = 20 - $results[0]['feature_size'];
		$text = $text . "<tr><td>Producing?<td>In " . $remaining . " ticks</tr>";
	} else {
		$text = $text . "<tr><td>Producing?<td>Yes</tr>";
	}
	
	$text = $text . "<tr><td colspan=2 align=center><a href='world.php?world=" . $world_id . "'>Back</a>";
	$text = $text . "</table>";

	return $text;
	
}

function chooseFarmType($square_id, $world_id, $choice) {
	
	#HEAD:Chooses farm type
	writeLog("chooseFarmType()");	
	
	$dml = "UPDATE oddworld.feature SET feature_variant = '" . $choice . "' WHERE square_id = " . $square_id . ";";
	$status = doInsert($dml);
	
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
		while ($vowel != "a" && $vowel != "e" && $vowel != "i" && $vowel != "o" && $vowel != "u") {
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
			$text = $text . "<tr><td><a href='" . $feature['feature_type'] . ".php?world=" . $world_id . "&square=" . $feature['square_id'] . "'>" . $feature['feature_name'] . "</a> [" . ucwords($feature['feature_variant']) . "] (" . $feature['square_x'] . ":" . $feature['square_y'] . ")</tr>";		
		}
		
		$text = $text . "</table>";
		
	} else {
		$text = "There are no features to display.";
	}
	
	return $text;
	
}

function buyFeature($feature_type, $world_id) {
	
	# This function will handle purchasing of features
	writeLog("buyFeature()");

	$cost = calculateFeatureCost($feature_type, $world_id);
	
	# Determine if enough funds exist
	$current_funds = getCurrentFunds($world_id);
	
	if ($current_funds >= $cost) {
		# Update funds
		$dml = "UPDATE oddworld.grid SET grid_money = grid_money - " . $cost . " WHERE grid_id = " . $world_id . ";";
		writeLog("buyFeature(): DML: " . $dml);
		$status = doInsert($dml);	
	}
	
}

function calculateFeatureCost($feature_type, $world_id) {

	# This function will determine the cost of buying next feature
	writeLog("buyFeature()");

	$feature_count = countFeatures($feature_type, $world_id);
	
	if ($feature_type == 'farm') {
		$cost = 400 + (40 * $feature_count);
	} elseif ($feature_type == 'mine') {
		$cost = 1000 + (100 * $feature_count);
	} else {
		
	}
	
	return $cost;
	
}

function countFeatures($feature_type, $world_id) {
	
	# This function will count how many features there are in the world
	writeLog("countFeatures()");

	$sql = "SELECT count(*) FROM oddworld.feature, oddworld.square WHERE feature_type = '" . $feature_type . "' AND feature.square_id = square.square_id AND square.grid_id = " . $world_id . ";";
	$results = doSearch($sql);
	
	return $results[0]['count(*)'];
	
}

function canIAffordIt($feature_type, $world_id) {

	# This function will determine if you can afford something
	writeLog("canIAffordIt()");

	$cost = calculateFeatureCost($feature_type, $world_id);
	writeLog("canIAffordIt(): Cost: " . $cost);
	
	$current_funds = getCurrentFunds($world_id);
	writeLog("canIAffordIt(): Current Funds: " . $current_funds);
	
	if ($current_funds >= $cost) {
		return 1;
	} else {
		return 0;
	}
	
}

function writeEvent($event) {
	
	$GLOBALS['events'] = $GLOBALS['events'] . $event . "<br/>";
	
}

function displayEvents() {
	
	echo "<div align=center>" . $GLOBALS['events'] . "</div><p>";
	
}


?>