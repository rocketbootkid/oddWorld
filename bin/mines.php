<html>

<head>
<title>OddWorld - Mines</title>
</head>

<body>

<?php

	include('functions/mysql_functions.php');
	include('functions/grid_functions.php');
	include('functions/feature_functions.php');
	include('functions/log_functions.php');
	
	echo featureList($_GET['world'], 'mine');
		
	displayLog();

?>

</body>

</html>