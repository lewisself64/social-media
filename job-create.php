<?php

	if(isset($_POST['job-title']) && isset($_POST['job-location']) && isset($_POST['job-description']) && isset($_POST['job-salary']))
	{
		include_once('includes/functions.php');

		$job_title			 = $_POST['job-title'];
		$job_location		 = $_POST['job-location'];
		$job_description = $_POST['job-description'];
		$job_salary 		 = $_POST['job-salary'];

		$job_sql 	  = "INSERT INTO jobs VALUES(null, '$job_title', '$job_location', '$job_description', $job_salary);";
		$job_query  = mysqli_query($login_connect, $job_sql);
	}

	header('location:home.php');

?>
