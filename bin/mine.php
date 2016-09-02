<html>

<head>
<title>OddWorld - Mine</title>
</head>

<body>

<?php

	include('mysql_functions.php');
	include('grid_functions.php');
	include('feature_functions.php');
	include('log_functions.php');
	
	if (isset($_GET['square'])) {
		echo displayMine($_GET['square'], $_GET['world']);
	} else {
		echo "Select a mine to display.";
	}
	
	displayLog();

?>

</body>

</html>