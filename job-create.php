<?php

	// Check all the values required for the job to be created have been filled in
	if(isset($_POST['job-title']) && isset($_POST['job-location']) && isset($_POST['job-description']) && isset($_POST['job-salary']) && isset($_POST['employer-id']))
	{
		// Include functions.php for database connection
		include_once('includes/functions.php');

		// Store job information
		$job_title			 = $_POST['job-title'];
		$job_location		 = $_POST['job-location'];
		$job_description = $_POST['job-description'];
		$job_salary 		 = $_POST['job-salary'];
		$employer_id 		 = $_POST['employer-id'];

		// Run the SQL to insert the new job into the database
		$job_sql 	  = "INSERT INTO jobs VALUES(null, '$job_title', '$job_location', '$job_description', $job_salary, $employer_id, 0);";
		$job_query  = mysqli_query($login_connect, $job_sql);
	}

	// Redirect the user back to the home.php file
	header('location:home.php');

?>
