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
		$user_type  			= sanatize($_POST['user-type']);
		$email      			= sanatize($_POST['email']);
		$username   			= sanatize($_POST['username']);
		$password   			= sanatize($_POST['password']);
		$confirm_password = sanatize($_POST['confirm-password']);
		$skills						= json_encode(sanatize($_POST['skills']));
		$required 				= false;

		if($user_type == 'contractor')
		{
			if(isset($_POST['first-name']) && isset($_POST['last-name']) && isset($_POST['date-of-birth']) && isset($_POST['bio']) && isset($_POST['gender']))
			{
				$first_name	 	 = sanatize($_POST['first-name']);
				$last_name  	 = sanatize($_POST['last-name']);
				$date_of_birth = sanatize($_POST['date-of-birth']);
				$bio  				 = sanatize($_POST['bio']);
				$gender  			 = sanatize($_POST['gender']);

				$required = true;
			}
		}
		elseif($user_type == 'employer')
		{
			if(isset($_POST['company-name']))
			{
				$company_name = sanatize($_POST['company-name']);

				$required = true;
			}
		}

		// Check all required details are complete
		if($email && $password && $confirm_password && $username && $user_type && $required)
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

						// Check if the user signed up is a contractor or employer
						if($user_type == 'contractor' || $user_type == 'employer')
						{
							// Query to insert user into database
							$insert_user_query = "INSERT INTO users (email, username, salt, password)
																		VALUES ('$email',
																						'$username',
																						'$salt',
																						'$password');";

							// Run query to insert the new contractor into the database
							mysqli_query($login_connect, $insert_user_query);

							$insert_id = mysqli_insert_id($login_connect);
						}

						if($user_type == 'contractor')
						{
							$cv_name  = $_FILES['cv']['name'];
							$cv_type  = $_FILES['cv']['type'];
							$cv_temp  = $_FILES['cv']['tmp_name'];
							$cv_path 	= "profiles/cv/";
							$cv 			= null;

							if($cv_type == 'application/pdf')
							{
								if(is_uploaded_file($cv_temp)) 
								{
									if(move_uploaded_file($cv_temp, $cv_path . $imagename))
									{
										$cv = $imagename;
									}
								}
							}
							else
							{
								$message = 'The CV Must be a PDF.';
							}

							$insert_contractor_query = "INSERT INTO `contractors` (`id`, `user_id`, `first_name`, `last_name`, `date_of_birth`, `bio`, `cv`, `gender`, `skills`) 
																					VALUES (NULL,
																									'$insert_id',
																									'$first_name',
																									'$last_name',
																									'$date_of_birth',
																									'$bio',
																									'$cv',
																									'$gender',
																									'$skills');";

							// Run query to insert the new contractor into the database
							mysqli_query($login_connect, $insert_contractor_query);

							$message = '<span id="success">Contractor created!</span>';
						}
						elseif($user_type == 'employer')
						{
							$imagename  = $_FILES['company-logo']['name'];
							$cv_type  = $_FILES['company-logo']['type'];
							$cv_temp  = $_FILES['company-logo']['tmp_name'];
							$cv_path 	= "profiles/images/";
							$logo 			= null;

							if(is_uploaded_file($cv_temp))
							{
								if(move_uploaded_file($cv_temp, $cv_path . $imagename))
								{
									$logo = $imagename;
								}
							}

							$insert_employer_query = "INSERT INTO `employer` (`id`, `user_id`, `company_name`, `company_logo`) VALUES (NULL, '$insert_id', '$company_name', '$logo');";

							// Run query to insert the new employer into the database
							mysqli_query($login_connect, $insert_employer_query);

							$message = '<span id="success">Employer created!</span>';
						}
						else
						{
							$message = '<span id="error">User type does not exist. Contact the system administrator.</span>';
						}
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

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="assets/js/register.js"></script>
	</head>

	<body>
		<div id="form-container">
			<?php echo $message; ?>
			<h2>Register</h2>
			<form action="#" method="post" enctype="multipart/form-data">
				<p>
					<label class="user-type-label"><input class="user-type-select" type="radio" name="user-type" value="contractor" checked>Contractor</label>
					<label class="user-type-label"><input class="user-type-select" type="radio" name="user-type" value="employer">Employer</label>
				</p>
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
				<!-- Contractor -->
				<p class="contractor required">
					<label>First Name: <span class="required">*</span></label>
					<input type="text" name="first-name" />
				</p>
				<p class="contractor required">
					<label>Last Name: <span class="required">*</span></label>
					<input type="text" name="last-name" />
				</p>
				<p class="contractor required">
					<label>Date of birth: <span class="required">*</span></label>
					<input type="date" name="date-of-birth" />
				</p>
				<p class="contractor required">
					<label>Gender: <span class="required">*</span></label>
					<select name="gender">
						<option value="male">Male</option>
						<option value="female">Female</option>
					</select>
				</p>
				<p class="contractor">
					<label>Bio: </label>
					<textarea name="bio"></textarea>
				</p>
				<p class="contractor">
					<label>Skills: <small>Seperate skills using a comma</small></label>
					<input type="text" name="skills" />
				</p>
				<p class="contractor">
					<label>CV: </label>
					<input type="file" name="cv" />
				</p>
				<!-- Employer -->
				<p class="employer required">
					<label>Company Name: <span class="required">*</span></label>
					<input type="text" name="company-name" />
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