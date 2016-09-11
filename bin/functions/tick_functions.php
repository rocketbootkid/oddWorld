<?php

$tick = 0;

function tick() {
	
	$world_id = $_COOKIE['world'];
	$time = file('logs/World' . $world_id . '/tick.txt');
	$current_tick = $time[0];
	$new_tick = $current_tick + 1;
	$GLOBALS['tick'] = $new_tick;
	writeLog("tick(): New Tick: " . $new_tick);
	$file = fopen('logs/World' . $world_id . '/tick.txt', 'w');
	fwrite($file, $new_tick);
	fclose($file);
	
}

function currentTick() {
	
	return $GLOBALS['tick'];
	
}

function manageDisasters($world_id) {
	
	# Random Mine cave-ins
	manageMineDisasters($world_id);
	
	# Random farm blight
	manageFarmDisasters($world_id);
	
}

function manageMineDisasters($world_id) {
	
	# This function will manage mine disasters
	writeLog("manageMineDisasters()");	
	
	$sql = "SELECT feature_id FROM oddworld.feature, oddworld.square, oddworld.grid WHERE feature_size > 0 AND feature_type = 'mine' AND feature.square_id = square.square_id AND square.grid_id = grid.grid_id AND grid.grid_id = " . $world_id . ";";
	$results = doSearch($sql);
	writeLog("manageMineDisasters(): Mines: " . count($results));
	
	foreach ($results as $mine) {
		
		srand();
		if (rand(1, 20) == 1) {
		
			$dml = "UPDATE oddworld.feature SET feature_size = feature_size / 2 WHERE feature_id = " . $mine['feature_id'] . ";";
			$status = doInsert($dml);
			if ($status == TRUE) {
				writeLog("manageMineDisasters(): Cave life reduced!");
			} else {
				writeLog("manageMineDisasters(): ERROR: Cave life not reduced!");
			}		
		
		}
		
	}
	
}

function manageFarmDisasters($world_id) {
	
	# This function will manage farm disasters
	writeLog("manageFarmDisasters()");	
	
	$sql = "SELECT feature_id FROM oddworld.feature, oddworld.square, oddworld.grid WHERE feature_type = 'farm' AND feature.square_id = square.square_id AND square.grid_id = grid.grid_id AND grid.grid_id = " . $world_id . ";";
	$results = doSearch($sql);
	writeLog("manageFarmDisasters(): Mines: " . count($results));
	
	foreach ($results as $farm) {
		
		srand();
		if (rand(1, 50) == 1) {
		
			$dml = "UPDATE oddworld.feature SET feature_size = 0 WHERE feature_id = " . $farm['feature_id'] . ";";
			$status = doInsert($dml);
			if ($status == TRUE) {
				writeLog("manageMineDisasters(): Farm life reduced!");
			} else {
				writeLog("manageMineDisasters(): ERROR: Farm life not reduced!");
			}		
		
		}
		
	}
	
}

function manageFeatureLife($world_id) {
	
	# Increase size of towns by 1
	manageTownLife($world_id);
	
	# Age farms until 20
	manageFarmLife($world_id);
	
	# Decrease life of all mines by 1
	manageMineLife($world_id);
	
	# Land randomly turns back to forest
	manageReforestation($world_id);
	
}

function manageReforestation($world_id) {
	
	# This function will manage the reforestation of empty land
	writeLog("manageReforestation()");	
	
	$sql = "SELECT square_id FROM oddworld.square WHERE square_type = 'land' AND grid_id = " . $world_id . ";";
	$results = doSearch($sql);
	writeLog("manageReforestation(): Fallow land: " . count($results));
	
	foreach ($results as $land) {
		
		srand();
		if (rand(1, 60) == 1) {
		
			$dml = "UPDATE oddworld.square SET square_type = 'forest' WHERE square_id = " . $land['square_id'] . ";";
			$status = doInsert($dml);
			if ($status == TRUE) {
				writeLog("manageReforestation(): Forest grows back!");
			} else {
				writeLog("manageReforestation(): ERROR: Forest doesn't grow back!");
			}		
		
		}
		
	}
	
}

function manageFarmLife($world_id) {
	
	# This function will manage the maturing of farms
	writeLog("manageFarmLife()");	
	
	$sql = "SELECT feature_id FROM oddworld.feature, oddworld.square WHERE feature_size < 20 AND feature_type = 'farm' AND square.square_id = feature.square_id AND square.grid_id = " . $world_id . ";";
	$results = doSearch($sql);
	writeLog("manageEconomy(): Farms: " . count($results));
	
	foreach ($results as $farm) {
		
		$dml = "UPDATE oddworld.feature SET feature_size = feature_size + 1 WHERE feature_id = " . $farm['feature_id'] . ";";
		$status = doInsert($dml);
		if ($status == TRUE) {
			writeLog("manageFarmLife(): Farm life updated!");
		} else {
			writeLog("manageFarmLife(): ERROR: Farm life not updated!");
		}		
		
	}
	
}

function manageMineLife($world_id) {
	
	# This function will manage the running out of mines
	writeLog("manageMineLife()");	
	$sql = "SELECT feature_id FROM oddworld.feature, oddworld.square WHERE feature_size > 0 AND feature_type = 'mine' AND square.square_id = feature.square_id AND square.grid_id = " . $world_id . ";";
	$results = doSearch($sql);
	writeLog("manageMineLife(): Current Mines: " . count($results));
	
	foreach ($results as $farm) {
		
		$dml = "UPDATE oddworld.feature SET feature_size = feature_size - 1 WHERE feature_id = " . $farm['feature_id'] . ";";
		$status = doInsert($dml);
		if ($status == TRUE) {
			writeLog("manageMineLife(): Mine life updated!");
		} else {
			writeLog("manageMineLife(): ERROR: Mine life not updated!");
		}		
		
	}
	
}

function manageTownLife($world_id) {
	
	# This function will manage the maturing of Towns
	writeLog("manageTownLife()");	
	
	$sql = "SELECT feature_id FROM oddworld.feature, oddworld.square WHERE feature_size < 100 AND feature_type = 'town' AND square.square_id = feature.square_id AND square.grid_id = " . $world_id . ";";
	$results = doSearch($sql);
	writeLog("manageEconomy(): Towns: " . count($results));
	
	foreach ($results as $town) {
		
		$dml = "UPDATE oddworld.feature SET feature_size = feature_size + 1 WHERE feature_id = " . $town['feature_id'] . ";";
		$status = doInsert($dml);
		if ($status == TRUE) {
			writeLog("manageTownLife(): Town population updated!");
		} else {
			writeLog("manageTownLife(): ERROR: Town population not updated!");
		}		
		
	}
	
}

function manageEconomy($arrPrices, $world_id) {
	
	# This function will determine income from features
	writeLog("manageEconomy()");
	
	$income = 0;
	
	# Get all farms whose life is at 20
	$sql = "SELECT * FROM oddworld.feature, oddworld.square WHERE feature_size >= 20 AND feature_type = 'farm' AND square.square_id = feature.square_id AND square.grid_id = " . $world_id . ";";
	$results = doSearch($sql);
	writeLog("manageEconomy(): Working Farms: " . count($results));
	
	foreach ($results as $farm) {	
		
		$farm_type = strtolower($farm['feature_variant']); # Get the type of farm
		$value = $arrPrices[$farm_type]; # Get the current value of that crop
		$adjacent = countAdjacentFeatures($farm['square_id'], 'variant', $farm['feature_variant'], $world_id);
		if ($adjacent > 0) {
			$value = $value * ($adjacent / 10);
		}
		writeLog("manageEconomy(): " . $farm_type . ": " . $value);
		
		$income = $income + $value;	# add that to total income
	}
	
	# Get all mines whose life is greater than 0;
	$sql = "SELECT * FROM oddworld.feature, oddworld.square WHERE feature_size > 0 AND feature_type = 'mine' AND square.square_id = feature.square_id AND square.grid_id = " . $world_id . ";";
	$results = doSearch($sql);
	writeLog("manageEconomy(): Working Mines: " . count($results));
	
	foreach ($results as $mine) {	
		
		$mine_type = strtolower($mine['feature_variant']); # Get the type of mine
		$value = $arrPrices[$mine_type]; # Get the current value of that mineral
		writeLog("manageEconomy(): " . $mine_type . ": " . $value);
		$adjacent = countAdjacentFeatures($mine['square_id'], 'variant', $mine['feature_variant'], $world_id);
		if ($adjacent > 0) {
			$value = $value * ($adjacent / 10);
		}
		
		$income = $income + $value;	# add that to total income
	}
	
	# Update income
	addFunds($world_id, $income);
	
}

function generatePrices() {
	
	# This function will generates an array of prices
	writeLog("generatePrices()");
	
	$GLOBALS['tick'] = $GLOBALS['tick'] + 1;
	
	$arrPrices = array('wool' => floor(fluctuateResource(15, 50, 30)),
					'corn' => floor(fluctuateResource(20, 60, 60)),
					'milk' => floor(fluctuateResource(10, 40, 90)),
					'beef' => floor(fluctuateResource(20, 60, 120)),
					'wheat' => floor(fluctuateResource(15, 50, 150)),
					'potato' => floor(fluctuateResource(10, 40, 180)),
					'iron' => floor(fluctuateResource(10, 40, 210)),
					'coal' => floor(fluctuateResource(5, 30, 240)),
					'gold' => floor(fluctuateResource(50, 200, 270)),
					'silver' => floor(fluctuateResource(40, 150, 300)),
					'copper' => floor(fluctuateResource(30, 120, 330)),
					'tin' => floor(fluctuateResource(20, 80, 0))
				);
	
	return $arrPrices;
}


function fluctuateResource($min, $max, $phase) {
	
	srand();
	$offset = rand(-30, 30);
	
	return (($max - $min)/2 * (round(sin(deg2rad($GLOBALS['tick'] + $offset + $phase)), 2))) + ($max + $min)/2;
	
}

function logPrices($world_id, $arrPrices) {
	
	$text = "";
	
	foreach ($arrPrices as $price) {
		$text = $text . $price . ",";	
	}
	$text = substr($text, 0, strlen($text)-1);
	$text = "\n" . $text;
	
	$file = fopen('logs/World_' . $world_id . '_prices.log', 'a+');
	fwrite($file, $text);
	fclose($file);
		
}


?>