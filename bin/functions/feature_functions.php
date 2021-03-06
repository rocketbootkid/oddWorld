<?php

$events = "";

function clearForest($square_id, $world_id) {
	
	# This function will clear a forest square
	writeLog("clearForest(): Square ID: " . $square_id);
	
	# Change square type
	$dml = "UPDATE oddworld.square SET square_type = 'land' WHERE square_id = " . $square_id . ";";
	$status = doInsert($dml);

	# Add funds
	addFunds($world_id, 10);
	
	writeEvent("Forest cleared from Square " . $square_id);
	
}

function fillWater($square_id, $world_id) {
	
	# This function will fill a water square
	writeLog("fillWater(): Square ID: " . $square_id);
	
	# Change square type
	$dml = "UPDATE oddworld.square SET square_type = 'land' WHERE square_id = " . $square_id . ";";
	$status = doInsert($dml);

	# Add funds
	addFunds($world_id, 10);
	
	writeEvent("Water filled in " . $square_id);
	
	buyFeature('sea', $world_id);
	
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
		
		return 1;
			
	} else {
		writeLog("createMine(): ERROR: Not a mountain square, or can't afford it!");
		writeEvent("Mine not created! You can't afford it :-(");
		
		return 0;
	}	

}

function createTown($world_id, $square_id) {
	
	# This function will create a town
	writeLog("createTown(): Square ID: " . $square_id);
	
	$square_type = getSquareType($square_id);
	writeLog("createTown(): Square Type: " . $square_type);
	
	if ($square_type == "land" && canIAffordIt('town', $world_id) == 1) {
	
		$dml = "UPDATE oddworld.square SET square_type = 'town' WHERE square_id = " . $square_id . ";";
		$status = doInsert($dml);
		if ($status == TRUE) {
			writeLog("createTown(): Town created!");
		} else {
			writeLog("createTown(): ERROR: Town not created!");
		}
		
		# Create farm feature
		$feature_name = generateFeatureName('town');
		$dml = "INSERT INTO oddworld.feature (feature_type, feature_name, square_id, feature_size) VALUES ('town', '" . $feature_name . "', " . $square_id . ", 1);";
		$status = doInsert($dml);
		if ($status == TRUE) {
			writeLog("createTown(): Town feature created!");
		} else {
			writeLog("createTown(): ERROR: Town feature not created!");
		}
		
		# Reduce funds based on number of features of this type already in existence
		buyFeature('town', $world_id);
			
	} else {
		writeLog("createTown(): ERROR: Not a land square, or can't afford it.");
		writeEvent("Town not created! You can't afford it :-(");
	}		
	
	
	
}

function displayTown($square_id, $world_id) {

	#HEAD:Displays town information
	
	writeLog("displayTown()");
	$text = "";
	
	$sql = "SELECT * FROM oddworld.feature WHERE square_id = " . $square_id . ";";
	$results = doSearch($sql);
	
	$text = $text . "<table cellpadding=3 cellspacing=1 border=1 align=center>";
	$text = $text . "<tr><td colspan=2 align=center><img src='../images/" . $results[0]['feature_type'] . ".png' width=50px height=50px></tr>";
	$text = $text . "<tr><td colspan=2 align=center><h2>" . $results[0]['feature_name'] . "</h2></tr>";
	$text = $text . "<tr><td>Population<td>" . $results[0]['feature_size'] . "</tr>";
	$text = $text . "<tr><td colspan=2 align=center><a href='world.php?world=" . $world_id . "'>Back</a>";
	$text = $text . "</table>";

	return $text;
	
}

function displayMine($square_id, $world_id) {

	#HEAD:Displays mine information
	
	writeLog("displayMine()");
	$text = "";
	
	$sql = "SELECT * FROM oddworld.feature WHERE square_id = " . $square_id . ";";
	$results = doSearch($sql);
	
	$text = $text . "<table cellpadding=3 cellspacing=1 border=1 align=center>";
	$text = $text . "<tr><td colspan=2 align=center><img src='../images/" . $results[0]['feature_variant'] . ".png' width=50px height=50px></tr>";
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
	$text = $text . "<tr><td colspan=2 align=center><img src='../images/" . $results[0]['feature_variant'] . ".png' width=50px height=50px></tr>";
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

function chooseFarmOrTown($world_id, $square_id) {
	
	#HEAD:Chooses farm type
	writeLog("chooseFarmOrTown()");
	
	$text = "";
	
	$link = "feature.php?world=" . $world_id . "&square=" . $square_id;
	
	$text = $text . "<table cellpadding=3 cellspacing=1 border=1 align=center>";
	$text = $text . "<h2 align=center>Choose to build a Farm or a Town</h2>";
	$text = $text . "<tr><td colspan=2 align=center><a href='" . $link . "&found=town'>Found Town</a></tr>";
	$text = $text . "<tr><td colspan=2 align=center><strong>OR</strong></tr>";
	$text = $text . "<tr><td valign=top colspan=2 align=center>Choose a Farm Type</tr>";
	$text = $text . "<tr><td align=right><a href='" . $link . "&choice=beef'>Beef</a><br/><a href='" . $link . "&choice=wool'>Sheep</a><br/><a href='" . $link . "&choice=milk'>Dairy</a><td><a href='" . $link . "&choice=wheat'>Wheat</a><br/><a href='" . $link . "&choice=potato'>Potatoes</a><br/><a href='" . $link . "&choice=corn'>Corn</a><br/></tr>";
	$text = $text . "</table>";

	return $text;
	
}

function chooseSeaAction($world_id, $square_id) {
	
	#HEAD:Chooses farm type
	writeLog("chooseSeaAction()");
	
	$text = "";
	
	$link = "feature.php?world=" . $world_id . "&square=" . $square_id;
	
	$text = $text . "<table cellpadding=3 cellspacing=1 border=1 align=center>";
	$text = $text . "<h2 align=center>Choose to Create Land or build Fish Farm</h2>";
	$text = $text . "<tr><td colspan=2 align=center><a href='" . $link . "&option=land'>Create Land</a></tr>";
	$text = $text . "<tr><td colspan=2 align=center><a href='" . $link . "&option=fish'>Create Fish Farm</a></tr>";
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

function getFeatureAttribute($square_id, $attribute) {
	
	# This function will return the desired attribute of the feature at the provided square
	writeLog("getFeatureAttribute(): Square ID: " . $square_id);	
	
	$sql = "SELECT " . $attribute . " FROM oddworld.feature WHERE square_id = " . $square_id . ";";
	$results = doSearch($sql);
	$count = count($results);
	writeLog("getFeatureAttribute(): Results: " . count($results));
	if ($count != 0) {
		writeLog("getFeatureAttribute(): " . $attribute . ": " . $results[0][$attribute]);
		return $results[0][$attribute];	
	} else {
		return "";
	}
		
}

function getFeatureName($square_id) {
	
	# This function will return the name of the feature at the provided square
	writeLog("getFeatureName(): Square ID: " . $square_id);	
	
	return getFeatureAttribute($square_id, "name");
		
}

function featureList($world_id, $type) {
	
	# This function will return the name of the feature at the provided square
	writeLog("featureList()");	
	
	$text = "";
	
	$sql = "SELECT feature_name, feature.square_id, square_x, square_y, feature_type, feature_variant, feature_size FROM oddworld.feature, oddworld.square, oddworld.grid WHERE feature.square_id = square.square_id AND square.grid_id = grid.grid_id AND grid.grid_id = " . $world_id . " AND feature_type = '" . $type . "' ORDER BY feature_variant ASC, feature_size DESC;";
	$results = doSearch($sql);
	
	if (count($results) > 0) {
		
		$text = "<table cellpadding=3 cellspacing=0 border=1>";
		$current_type = "";
		
		foreach ($results as $feature) {
			if ($current_type <> strtolower($feature['feature_variant'])) {
				$text = $text . "<tr bgcolor=#bbb><td>" . ucwords($feature['feature_variant']) . " " . ucwords($feature['feature_type']) . "(s)";
				$text = $text . "<td colspan=3>" . commodityTrend($feature['feature_variant'], $world_id) . "</tr>";
				$text = $text . "</tr>";
				$current_type = strtolower($feature['feature_variant']);
			}
			$text = $text . "<tr><td>" . $feature['feature_name'] . "<td>(" . $feature['square_x'] . ":" . $feature['square_y'] . ")";
			if ($feature['feature_type'] == "mine") {			
				if ($feature['feature_size'] > 0) {
					$text = $text . "<td>Working (" . $feature['feature_size'] . ")";
				} else {
					$text = $text . "<td bgcolor=#ccc align=center><font color=#fff>Abandoned</font>";
				}
			} elseif ($feature['feature_type'] == "town") {			
				$text = $text . "<td>Population: " . $feature['feature_size'];
			} else {
				if ($feature['feature_size'] == 20) {
					$text = $text . "<td align=center>Working";
				} else {
					$remaining = 20 - $feature['feature_size'];
					$text = $text . "<td bgcolor=#ccc><font color=#fff>Ready in " . $remaining . "</font>";
				}
			}
			$text = $text . "<td>+" . countAdjacentFeatures($feature['square_id'], 'variant', $feature['feature_variant'], $world_id);
			$text = $text . "</tr>";
		}
		
		$text = $text . "</table>";
		
	} else {
		$text = "There are no features to display.";
	}
	
	$text = $text . "<p><a href='world.php?world=" . $world_id . "'>Back</a>";
	
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
		$cost = 10000 + (1000 * $feature_count);
	} elseif ($feature_type == 'town') {
		$cost = 50000 + (10000 * $feature_count);
	} elseif ($feature_type == 'sea') {
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

function countAdjacentFeatures($square_id, $TypeVariant, $varortype, $grid_id) {
	
	# This function will determine how many adjacent squares contain the desired feature type or variant
	writeLog("countAdjacentFeatures()");
	
	$arrSquares = getAdjacentSquares($square_id, $grid_id);
	$count = 0;
	
	foreach ($arrSquares as $square) {
		if ($TypeVariant == "type") { # Type
			$type = getFeatureAttribute($square['square_id'], "feature_type");
			if ($type == $varortype) { $count++; };
		} else { # Variant
			$variant = getFeatureAttribute($square['square_id'], "feature_variant");
			if ($variant == $varortype) { $count++; };	
		}
		
	}
	
	return $count;
	
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

function commodityTrend($commodity, $world_id) {
	
	# This function will return the most recent price of the commodity, and the recent trend
	# Trend = (Average of last ten prices) - Current Price
	writeLog("commodityTrend()");	
	if (!file_exists('logs/World_' . $world_id . '_prices.log')) {
		$file = fopen('logs/World_' . $world_id . '_prices.log', 'w+');
	}
	$arrPrices = file('logs/World_' . $world_id . '_prices.log');
	$rows = count($arrPrices);
	writeLog("commodityTrend(): Rows: " . $rows);	
	
	# Need to convert commodity into an index
	$arrCommodities = array('wool','corn','milk','beef','wheat','potato','iron','coal','gold','silver','copper','tin');
	$commodityIndex = array_search($commodity, $arrCommodities);
	writeLog("commodityTrend(): Index: " . $commodityIndex);	

	$total_price = 0;
	$current_price = 0;
	
	for ($p = $rows - 10; $p < $rows; $p++) {
		$arrCurrentRow = explode(",", $arrPrices[$p]);
		$current_price = $arrCurrentRow[$commodityIndex];
		$total_price = $total_price + $current_price;
	}
	writeLog("commodityTrend(): Current Price for " . ucwords($commodity) . ": " . $current_price);
	
	$average = floor($total_price / 10);
	writeLog("commodityTrend(): Average: " . $average);	
	$trend = $current_price - $average;
	writeLog("commodityTrend(): Trend: " . $trend);	
	
	$text = $current_price . " (" . $trend . ")";
	
	return $text;
	
}

function writeEvent($event) {
	
	$GLOBALS['events'] = $GLOBALS['events'] . $event . "<br/>";
	
}

function displayEvents() {
	
	echo "<div align=center>" . $GLOBALS['events'] . "</div><p>";
	
}


?>