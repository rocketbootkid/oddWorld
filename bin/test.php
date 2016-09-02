<?php

$tick = 0;

var_dump(alterPrices());

function alterPrices() {
	
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