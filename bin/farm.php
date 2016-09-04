<html>

<head>
<title>OddWorld - Farm</title>
</head>

<body>

<?php

	include('mysql_functions.php');
	include('grid_functions.php');
	include('feature_functions.php');
	include('log_functions.php');
	
	if (isset($_GET['choice'])) {
		chooseFarmType($_GET['square'], $_GET['world'], $_GET['choice']);
	}
	
	if (isset($_GET['square'])) {
		echo displayFarm($_GET['square'], $_GET['world']);
	} else {
		echo "Select a farm to display.";
	}
	
	displayLog();

?>

</body>

</html>