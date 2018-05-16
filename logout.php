<?php

	// Start the sessions
	session_start();
	
	// Destroy all sessions for the website
	session_destroy();
	
	// Redirect user back to the login page
	header('location:index.php');

?>