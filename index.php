<?php

	// Create sessions to store logged in user's id
	session_start();

	// Redirect user if the logged_in_user session already exists
	if(isset($_SESSION['logged_in_user']))
	{
		header('location:home.php');

		return;
	}

	// Connects to database
	include_once('includes/functions.php');

	// Default user message empty
	$message = '';

	// Check if the form has been submitted
	if(isset($_POST['form_submission']) && ($_POST['form_submission'] == true))
	{
		$username = sanatize($_POST['username']);
		$password = sanatize($_POST['password']);

		// Catch anyone who disables client-side scripting or is using an old IE browser
		if($username && $password)
		{
			$logged_in = 0;
			
			// Get the salt related to the account
			$salt_sql = "SELECT salt
									 FROM users
									 WHERE username='$username'";

			$salt = mysqli_query($login_connect, $salt_sql);

			if(mysqli_num_rows($salt) > 0)
			{
				while($row = mysqli_fetch_assoc($salt)) 
				{
					$password = hash_salt_password($password, $row["salt"]);
				}

				// Query to find users with the same username and password
				$login_sql = "SELECT *
											FROM users
											WHERE username='$username' AND password='$password';";

				$logged_in_check = mysqli_query($login_connect, $login_sql);

				// Get the number of rows returned. 1 indicates the password and username match, 0 indicates they are incorrect
				$logged_in = mysqli_num_rows($logged_in_check);
			}

			// Check if the login details are correct
			if($logged_in == 1)
			{
				$login_object = mysqli_fetch_object($logged_in_check);

				// Store the user object inside of the session
				$_SESSION['logged_in_user'] = $login_object;

				$contractor_sql = "SELECT * FROM employer WHERE user_id = '$login_object->id';";

				$contractor_check = mysqli_query($login_connect, $contractor_sql);

				if(mysqli_num_rows($contractor_check) == 1)
				{
					$_SESSION['logged_in_user_type'] = 'employer';
				}
				else
				{
					$_SESSION['logged_in_user_type'] = 'contractor';
				}

				// Redirect to admin page
				header('location:home.php');
			}
			else
			{
				$message = '<span id="error">Incorrect Login details</span>';
			}
		}
		else
		{
			$message = '<span id="error">Please complete all required fields</span>';
		}
	}

?>

<!DOCTYPE html>
<html>

	<head>
		<link rel="stylesheet" type="text/css" href="assets/css/master.css" />
		<link rel="stylesheet" type="text/css" href="assets/css/login.css" />
	</head>

	<body>
		<div id="form-container">
			<h2>Login</h2>
			<form action="#" method="post">
				<p>
					<label>Username: <span class="required">*</span></label>
					<input type="text" name="username" required />
				</p>
				<p>
					<label>Password: <span class="required">*</span></label>
					<input type="password" name="password" required />
				</p>
				<input type="hidden" name="form_submission" value="true" />  <!-- Used in PHP to check form submission -->
				<input type="submit" class="button" value="Submit" />
				<a href="register.php" class="register-button">Register</a>
			</form>
			<?php echo $message; ?>
		</div>
	</body>

</html>