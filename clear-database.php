<?php

	include_once('includes/functions.php');

	if(isset($_GET['table']) && $_GET['table'] == 'users')
	{
		$users_sql = "DELETE FROM users;";

		if(mysqli_query($login_connect, $users_sql))
		{
			echo 'Cleared user table. <br />';
		}
		else
		{
			echo 'Error clearing user table. <br />';
		}
	}

	if(isset($_GET['table']) && $_GET['table'] == 'contractors')
	{
		$contractors_sql = "DELETE FROM contractors;";

		if(mysqli_query($login_connect, $contractors_sql))
		{
			echo 'Cleared contractors table. <br />';
		}
		else
		{
			echo 'Error clearing contractors table. <br />';
		}
	}

	if(isset($_GET['table']) && $_GET['table'] == 'employers')
	{
		$employer_sql = "DELETE FROM employer;";

		if(mysqli_query($login_connect, $employer_sql))
		{
			echo 'Cleared employer table. <br />';
		}
		else
		{
			echo 'Error clearing employer table. <br />';
		}
	}

	if(isset($_GET['table']) && $_GET['table'] == 'applications')
	{
		$applications_sql = "DELETE FROM applications;";

		if(mysqli_query($login_connect, $applications_sql))
		{
			echo 'Cleared applications table. <br />';
		}
		else
		{
			echo 'Error clearing applications table. <br />';
		}
	}

	if(isset($_GET['table']) && $_GET['table'] == 'jobs')
	{
		$jobs_sql = "DELETE FROM jobs;";
		if(mysqli_query($login_connect, $jobs_sql))
		{
			echo 'Cleared jobs table. <br />';
		}
		else
		{
			echo 'Error clearing jobs table. <br />';
		}
	}

?>

<a href="?table=users">Clear users</a><br />
<a href="?table=contractors">Clear contractors</a><br />
<a href="?table=employers">Clear employers</a><br />
<a href="?table=applications">Clear applications</a><br />
<a href="?table=jobs">Clear jobs</a><br />
