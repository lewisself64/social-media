<?php

	function generate_salt()
	{
		$characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString     = '';

		for($i = 0; $i < 10; $i++)
		{
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}

		return $randomString;
	}

	// Connects to database
	include_once('includes/functions.php');

	// Default user message empty
	$message = '';

	// Check if the form has been submitted
	if(isset($_POST['form_submission']) && ($_POST['form_submission'] == true))
	{
		// Store all posts as variables. The sanatize function prevents SQL injection and XSS attacks
		$email      			= sanatize($_POST['email']);
		$username   			= sanatize($_POST['username']);
		$password   			= sanatize($_POST['password']);
		$confirm_password = sanatize($_POST['confirm-password']);
		$required 				= false;

		if(isset($_POST['company-name']) && isset($_POST['company-description']) && isset($_POST['company-location']) && isset($_POST['company-foundation']))
		{
			$company_name 			 = sanatize($_POST['company-name']);
			$company_description = sanatize($_POST['company-description']);
			$company_location    = sanatize($_POST['company-location']);
			$company_foundation  = sanatize($_POST['company-foundation']);

			$required = true;
		}

		// Check all required details are complete
		if($email && $password && $confirm_password && $username && $required)
		{
			if($password === $confirm_password)
			{
				// Query to find users with the same username
				$username_check_sql = "SELECT id
															 FROM users
															 WHERE username='$username';";

				$mysql_username_check_data = mysqli_query($login_connect, $username_check_sql);

				// Get the number of rows returned. 0 will mean the username does not exist in the database
				$username_count = mysqli_num_rows($mysql_username_check_data);

				// Make sure username does not exist in the database
				if($username_count == 0)
				{
					// Query to find users with the same username
					$email_check_sql = "SELECT id
															FROM users
															WHERE email='$email';";

					$mysql_email_check_data = mysqli_query($login_connect, $email_check_sql);

					// Get the number of rows returned. 0 will mean the username does not exist in the database
					$email_count = mysqli_num_rows($mysql_email_check_data);

					if($email_count == 0)
					{
						// Create random number to be used for the password salt
						$salt = generate_salt();

						// Hash the password with the random salt generated
						$password = hash_salt_password($password, $salt);

						// Query to insert user into database
						$insert_user_query = "INSERT INTO users (email, username, salt, password)
																	VALUES ('$email',
																					'$username',
																					'$salt',
																					'$password');";

						// Run query to insert the new contractor into the database
						mysqli_query($login_connect, $insert_user_query);

						$insert_id = mysqli_insert_id($login_connect);

						$logo_name = $_FILES['company-logo']['name'];
						$logo_type = $_FILES['company-logo']['type'];
						$logo_temp = $_FILES['company-logo']['tmp_name'];
						$logo_path = "profiles/images/";
						$logo 		 = null;

						if(is_uploaded_file($logo_temp))
						{
							if(move_uploaded_file($logo_temp, $logo_path . $logo_name))
							{
								$logo = $logo_name;
							}
						}

						$insert_employer_query = "INSERT INTO `employer` (`id`, `user_id`, `company_name`, `company_logo`, `company_description`, `company_foundation`, `company_location`) VALUES (NULL, '$insert_id', '$company_name', '$logo', '$company_description', '$company_foundation', '$company_location');";

						// Run query to insert the new employer into the database
						mysqli_query($login_connect, $insert_employer_query);

						$message = '<span id="success">Employer created!</span>';
					}
					else
					{
						$message = '<span id="error">This email has already been used! Please login instead</span>';
					}
				}
				else
				{
					$message = '<span id="error">Username taken! please choose another</span>';
				}
			}
			else
			{
				$message = '<span id="error">Passwords do not match!</span>';
			}
		}
		else
		{
			$message = '<span id="error">Please fill in all required fields</span>';
		}
	}

?>

<!DOCTYPE html>
<html>

	<head>
		<!-- Link style sheets for this page -->
		<link rel="stylesheet" type="text/css" href="assets/css/master.css" />
		<link rel="stylesheet" type="text/css" href="assets/css/register.css" />
	</head>

	<body>
		<div id="form-container">
			<?php echo $message; ?>
			<h2>Register Employer</h2>
			<form action="#" method="post" enctype="multipart/form-data">
				<p>
					<label>Email: <span class="required">*</span></label>
					<input type="email" name="email" required />
				</p>
				<p>
					<label>Username: <span class="required">*</span></label>
					<input type="text" name="username" required />
				</p>
				<p>
					<label>Password: <span class="required">*</span></label>
					<input type="password" name="password" required />
				</p>
				<p>
					<label>Confirm password: <span class="required">*</span></label>
					<input type="password" name="confirm-password" required />
				</p>
				<p class="employer required">
					<label>Company Name: <span class="required">*</span></label>
					<input type="text" name="company-name" />
				</p>
				<p class="employer required">
					<label>Location: <span class="required">*</span></label>
					<input type="text" name="company-location" />
				</p>
				<p class="employer required">
					<label>Foudation Date: <span class="required">*</span></label>
					<input type="date" name="company-foundation" />
				</p>
				<p class="employer">
					<label>Company Description: </label>
					<textarea name="company-description"></textarea>
				</p>
				<p class="employer">
					<label>Company Logo: </label>
					<input type="file" name="company-logo" />
				</p>
				<input type="hidden" name="form_submission" value="true" /> <!-- Used in PHP to check form submission -->
				<input type="submit" class="button" value="Submit" />
				<a href="index.php" class="login-button">Login</a>
			</form>
		</div>
	</body>

</html>