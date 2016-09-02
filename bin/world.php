<html>

<head>
<title>OddWorld - World <?php echo $_GET['world']; ?></title>
</head>

<body>

<?php

	include('mysql_functions.php');
	include('grid_functions.php');
	include('feature_functions.php');
	include('log_functions.php');
	include('tick_functions.php');

	if (isset($_GET['action']) && isset($_GET['square'])) {
		if ($_GET['action'] == 'clear') {
			clearForest($_GET['square']);
		} elseif ($_GET['action'] == 'farm') {
			createFarm($_GET['square']);
		} elseif ($_GET['action'] == 'mine') {
			createMine($_GET['square']);
		}
	}	
	
	if (isset($_GET['world'])) {
		echo "<table cellpadding=5 cellspacing=0 border=0 align=center>";
		echo "<tr><td valign=top>";
		echo featureList($_GET['world']);
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