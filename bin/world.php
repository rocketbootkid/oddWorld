<html>

<head>
<title>OddWorld - World <?php echo $_GET['world']; ?></title>
</head>

<body>

<?php

	include('functions/mysql_functions.php');
	include('functions/grid_functions.php');
	include('functions/feature_functions.php');
	include('functions/log_functions.php');
	include('functions/tick_functions.php');

	if (isset($_GET['action']) && isset($_GET['square'])) {
		if ($_GET['action'] == 'clear') {
			clearForest($_GET['square'], $_GET['world']);
		} elseif ($_GET['action'] == 'farm') {
			createFarm($_GET['square'], $_GET['world']);
		} elseif ($_GET['action'] == 'mine') {
			createMine($_GET['square'], $_GET['world']);
		}
	}
	
	echo displayEvents();
	
	if (isset($_GET['world'])) {
		echo "<table cellpadding=5 cellspacing=0 border=0 align=center>";
		echo "<tr><td valign=top>";
		echo "<p>Current Funds: " . getCurrentFunds($_GET['world']);
		echo "<p><a href='farms.php?world=" . $_GET['world'] . "'>Farms</a>";
		echo "<br/><a href='mines.php?world=" . $_GET['world'] . "'>Mines</a>";
		echo "<br/><a href='towns.php?world=" . $_GET['world'] . "'>Towns</a>";
		echo "<p><a href='world.php?world=" . $_GET['world'] . "'>Refresh</a>";
		echo "<p><a href='tick.php?world=" . $_GET['world'] . "' target='_blank'>Tick Page</a>";
		echo "<td valign=top>";
		echo drawGrid($_GET['world']);
		echo "</tr>";
		echo "</table>";
	} else {
		echo "Select a world to display.";
	}
	
	displayLog();

?>

</body>

</html>