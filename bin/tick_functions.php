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

function manageEconomy() {
	
}

function generatePrices() {
	
	# This function will generates an array of prices
	writeLog("generatePrices()");
	
	$GLOBALS['tick']++;
	
	$arrPrices = array('wool' => floor(fluctuateResource(15, 50, $GLOBALS['tick'])),
					'corn' => floor(fluctuateResource(20, 60, $GLOBALS['tick'])),
					'milk' => floor(fluctuateResource(10, 40, $GLOBALS['tick'])),
					'beef' => floor(fluctuateResource(20, 60, $GLOBALS['tick'])),
					'wheat' => floor(fluctuateResource(15, 50, $GLOBALS['tick'])),
					'potatoes' => floor(fluctuateResource(10, 40, $GLOBALS['tick'])),
					'iron' => floor(fluctuateResource(10, 40, $GLOBALS['tick'])),
					'coal' => floor(fluctuateResource(5, 30, $GLOBALS['tick'])),
					'gold' => floor(fluctuateResource(50, 200, $GLOBALS['tick'])),
					'silver' => floor(fluctuateResource(40, 150, $GLOBALS['tick'])),
					'copper' => floor(fluctuateResource(30, 120, $GLOBALS['tick'])),
					'tin' => floor(fluctuateResource(20, 80, $GLOBALS['tick']))
				);
	
	return $arrPrices;
}


function fluctuateResource($min, $max, $tick) {
	
	srand();
	$offset = rand(-30, 30);
	
	return (($max - $min)/2 * (round(sin(deg2rad($tick + $offset)), 2))) + ($max + $min)/2;
	
}


?>