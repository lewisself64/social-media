<?php

	if(isset($_GET['contractor']) && isset($_GET['job']))
	{
		include_once('includes/functions.php');
		
		$job_id 			 = $_GET['job'];
		$contractor_id = $_GET['contractor'];
		
		$application_sql = "INSERT INTO applications VALUES(null, $contractor_id, $job_id);";
		$user_query 		 = mysqli_query($login_connect, $application_sql);
	}

	header('location:home.php');

?>