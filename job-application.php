<?php

	// Check that the contractor and job values have been set
	if(isset($_GET['contractor']) && isset($_GET['job']))
	{
		// Include functions.php for database connection
		include_once('includes/functions.php');
		
		// Get the job_id and the contractor_id
		$job_id 			 = $_GET['job'];
		$contractor_id = $_GET['contractor'];
    
    $application_check_sql   = "SELECT * FROM `applications` WHERE contractor_id = $contractor_id AND job_id = $job_id";
    $application_check_query = mysqli_query($login_connect, $application_check_sql);
    
    if(mysqli_num_rows($application_check_query) == 0)
    {
      // Use SQL to insert the application into the database
      $application_sql = "INSERT INTO applications VALUES(null, $contractor_id, $job_id);";
      mysqli_query($login_connect, $application_sql);
    }
	}

	// Redirect users back to the home.php file
	header('location:home.php');

?>