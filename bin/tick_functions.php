<?php

$tick = 0;

function tick() {
	
	$time = file('tick.txt');
	$current_tick = $time[0];
	$new_tick = $current_tick + 1;
	$GLOBALS['tick'] = $new_tick;
	$file = fopen('tick.txt', 'w');
	fwrite($file, $new_tick);
	fclose($file);
	
}

function currentTick() {
	
	return $GLOBALS['tick'];
	
}

function manageIncome() {
	
}

function manageDisasters() {
	
}

function managePopulation() {
	
}

function manageFeatureLife() {
	
}

function manageEconomy($arrPrices, $world_id) {
	
	# This function will determine income from features
	writeLog("manageEconomy()");
	
	$income = 0;
	
	# Get all farms whose life is at 20
	$sql = "SELECT * FROM oddworld.feature, oddworld.square WHERE feature_size >= 20 AND feature_type = 'farm' AND square.square_id = feature.square_id AND square.grid_id = " . $world_id . ";";
	$results = doSearch($sql);
	
	foreach ($results as $farm) {	
		
		$farm_type = $farm['feature_variant']; # Get the type of farm
		$value = $arrResults[$farm_type]; # Get the current value of that crop
		$income = $income + $value;	# add that to total income
	}
	
	# Get all mines whose life is greater than 0;
	$sql = "SELECT * FROM oddworld.feature, oddworld.square WHERE feature_size > 0 AND feature_type = 'mine' AND square.square_id = feature.square_id AND square.grid_id = " . $world_id . ";";
	$results = doSearch($sql);
	
	foreach ($results as $mine) {	
		
		$mine_type = $mine['feature_variant']; # Get the type of mine
		$value = $arrResults[$mine_type]; # Get the current value of that mineral
		$income = $income + $value;	# add that to total income
	}
	
	# Update income
	addFunds($world_id, $income);
	
}

function generatePrices() {
	
	# This function will generates an array of prices
	writeLog("generatePrices()");
	
	$GLOBALS['tick'] = $GLOBALS['tick'] + 1;
	
	$arrPrices = array('wool' => floor(fluctuateResource(15, 50)),
					'corn' => floor(fluctuateResource(20, 60)),
					'milk' => floor(fluctuateResource(10, 40)),
					'beef' => floor(fluctuateResource(20, 60)),
					'wheat' => floor(fluctuateResource(15, 50)),
					'potatoes' => floor(fluctuateResource(10, 40)),
					'iron' => floor(fluctuateResource(10, 40)),
					'coal' => floor(fluctuateResource(5, 30)),
					'gold' => floor(fluctuateResource(50, 200)),
					'silver' => floor(fluctuateResource(40, 150)),
					'copper' => floor(fluctuateResource(30, 120)),
					'tin' => floor(fluctuateResource(20, 80))
				);
	
	return $arrPrices;
}


function fluctuateResource($min, $max) {
	
	srand();
	$offset = rand(-30, 30);
	
	return (($max - $min)/2 * (round(sin(deg2rad($GLOBALS['tick'] + $offset)), 2))) + ($max + $min)/2;
	
}


?>