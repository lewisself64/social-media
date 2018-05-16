<?php

	// Check all the values required for the job to be created have been filled in
	if(isset($_POST['job-id']) && isset($_POST['contractor-id']))
	{
		// Include functions.php for database connection
		include_once('includes/functions.php');

		// Store job information
		$job_id			   = $_POST['job-id'];
		$contractor_id = $_POST['contractor-id'];

		// Run the SQL to insert the new job into the database
		$job_sql 	  = "UPDATE jobs SET contractor_employed = $contractor_id WHERE id = $job_id";
		mysqli_query($login_connect, $job_sql);
	}

	// Redirect the user back to the home.php file
	header('location:home.php');

?>
