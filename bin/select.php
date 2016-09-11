<html>

<head>
<title>OddWorld</title>
</head>

<body>

<?php

	include('functions/mysql_functions.php');
	include('functions/grid_functions.php');
	include('functions/feature_functions.php');
	include('functions/log_functions.php');
	
	if (isset($_GET['create'])) {
		generateNewGrid();
	}
	
	echo worldList();
	
	displayLog();

?>

</body>

</html>