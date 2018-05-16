<?php

	session_start();

	if(!$_SESSION['logged_in_user'])
	{
		header('location:index.php');

		return;
	}

	include_once('includes/functions.php');

	$logged_in_user = $_SESSION['logged_in_user'];

	// If no profile_id is supplied, use the logged in users id.
	if(isset($_GET['profile_id']))
	{
		$profile_id = $_GET['profile_id'];
	}
	else
	{
		$profile_id = $logged_in_user->id;
	}

	$user_sql   	 = "SELECT * FROM users WHERE id = $profile_id";
	$user_query		 = mysqli_query($login_connect, $user_sql);
	$user   		 = mysqli_fetch_object($user_query);

	$user_check_sql = "SELECT * FROM employer WHERE user_id = '$profile_id';";

	$user_check = mysqli_query($login_connect, $user_check_sql);

	if(mysqli_num_rows($user_check) == 1)
	{
		$user_type = 'employer';
	}
	else
	{
		$user_type = 'contractor';
	}

	if($user_type == 'contractor')
	{
		$profile_sql = "SELECT * FROM contractors WHERE user_id = $profile_id";
	}
	else
	{
		$profile_sql = "SELECT * FROM employer WHERE user_id = $profile_id";
	}

	$profile_query = mysqli_query($login_connect, $profile_sql);
	$profile 			 = mysqli_fetch_object($profile_query);

?>

	<!DOCTYPE html>
	<html>
	<title>Profile | <?php echo $user->username; ?></title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="assets/css/profile.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

	<body class="w3-theme-l5">

		<div class="w3-top">
			<div class="w3-bar w3-theme-d2 w3-left-align w3-large">
				<a href="home.php" class="w3-bar-item w3-button w3-padding-large w3-theme-d4"><i class="fa fa-home w3-margin-right"></i>My Profile</a>
				<div class="w3-dropdown-hover w3-hide-small">
					<button class="w3-button w3-padding-large" title="Account Settings"><i class="fa fa-user w3-margin-right"></i>Settings</button>
					<div class="w3-dropdown-content w3-card-4 w3-bar-block" style="width:300px">
						<a href="#" class="w3-bar-item w3-button">Edit Profile</a>
						<a href="logout.php" class="w3-bar-item w3-button">Log out</a>
					</div>
				</div>
			</div>
		</div>

		<div class="w3-container w3-content" style="max-width:1400px;margin-top:80px">
			<div class="w3-row">
				<div class="w3-col m3">
					<div class="w3-card w3-round w3-white">
						<div class="w3-container">


							<?php

																if($user_type == 'contractor')
																{
																		echo '<h4 class="w3-center">' . $user->username . '</h4>';
																}
																elseif($user_type == 'employer')
																{
																		echo '<h4 class="w3-center">' . $profile->company_name . '</h4>';
																}

								if($profile->company_logo)
								{
									echo '<img src="profiles/images/' . $profile->company_logo . '" style="height:106px; display:block; margin:0 auto;" />';
								}

							?>
							<hr>
							<?php

								if($user_type == 'contractor')
								{
									echo '<p><i class="fa fa-address-book fa-fw w3-margin-right w3-text-theme"></i>' . $profile->first_name . ' ' . $profile->last_name . '</p>';
									echo '<p><i class="fa fa-birthday-cake fa-fw w3-margin-right w3-text-theme"></i>' . date("d/m/Y", strtotime($profile->date_of_birth)) . '</p>';
									echo '<p><i class="fa fa-at fa-fw w3-margin-right w3-text-theme"></i><a href="mailto:' . $user->email . '">' . $user->email . '</a></p>';

									if($profile->cv)
									{
										echo '<p><i class="fa fa-pencil fa-fw w3-margin-right w3-text-theme"></i><a href="profiles/cv/' . $profile->cv . '" target="_BLANK">View CV</a></p>';
									}
								}
								elseif($user_type == 'employer')
								{
									echo '<p><i class="fa fa-at fa-fw w3-margin-right w3-text-theme"></i><a href="mailto:' . $user->email . '">' . $user->email . '</a></p>';
									echo '<p><i class="fa fa-birthday-cake fa-fw w3-margin-right w3-text-theme"></i>' . date("d/m/Y", strtotime($profile->company_foundation)) . '</p>';
									echo '<p><i class="fa fa-home fa-fw w3-margin-right w3-text-theme"></i>' . $profile->company_location . '</p>';
								}

						 ?>
						</div>
					</div>
					<br />

					<?php if($user_type == 'contractor') : ?>
						<?php if($profile->skills) : ?>
							<div class="w3-card w3-round w3-white w3-hide-small">
								<div class="w3-container">
									<h4>Skills</h4>
									<?php $skills = explode(",", json_decode($profile->skills)); ?>
										<p>
											<?php foreach($skills as $skill) : ?>
												<span class="w3-tag w3-small w3-theme-d5"><?php echo $skill ?></span>
											<?php endforeach; ?>
										</p>
								</div>
							</div>
							<br />
						<?php endif; ?>

						<?php

							if(!isset($_GET['profile_id'])) :

								$profile_applications_sql   = "SELECT title, company_name, employer_id, jobs.contractor_employed FROM applications LEFT JOIN jobs ON applications.job_id = jobs.id LEFT JOIN users ON users.id = jobs.employer_id LEFT JOIN employer ON jobs.employer_id = employer.user_id WHERE contractor_id = " . $user->id . ";";
								$profile_applications_query = mysqli_query($login_connect, $profile_applications_sql);

								if(mysqli_num_rows($profile_applications_query) > 0) :

						?>

						<div class="w3-card w3-round w3-white w3-hide-small">
							<div class="w3-container">
								<h4>Submitted Applications</h4>

								<?php

									while($row = mysqli_fetch_array($profile_applications_query))
									{
										?><?php echo $row['title']; ?> at <a href="home.php?profile_id=<?php echo $row['employer_id']; ?>"><?php echo $row['company_name']; ?></a><br /><?php

										if($row['contractor_employed'] == $user->id)
										{
											echo '<span class="application-status">(Application successful)</span>';
										}
										elseif($row['contractor_employed'] != 0)
										{
											echo '<span class="application-status">(Application unsuccessful)</span>';
										}
										
										echo '<br /><br />';
									}

								?>

							</div>
							<br />
						</div>
						<br />

					<?php endif; endif; endif; ?>

					<?php

						if(!isset($_GET['profile_id'])) :

							$employer_jobs_sql   = "SELECT * FROM jobs WHERE employer_id = $user->id;";
							$employer_jobs_query = mysqli_query($login_connect, $employer_jobs_sql);

							if(mysqli_num_rows($employer_jobs_query) > 0) :

					?>

					<div class="w3-card w3-round w3-white w3-center">
						<div class="w3-container" id="job-applications">
							<h4>Active Jobs</h4>
							<?php

								while($row = mysqli_fetch_array($employer_jobs_query))
								{
									$applications_sql   = "SELECT user_id, first_name, last_name FROM applications LEFT JOIN contractors ON applications.contractor_id = contractors.user_id WHERE job_id = " . $row['id'] . ";";
									$applications_query = mysqli_query($login_connect, $applications_sql);

									?><hr /><h5><?php echo $row['title'] ?></h5><?php
										
									echo '<p style="font-size:12px; text-align:left;">' . $row['description'] . '</p>';
									echo '<p style="font-size:12px; text-align:left;"><b>' . $row['location'] . '</b>, &pound;' . number_format($row['salary']) . '</p>';

									if(mysqli_num_rows($applications_query))
									{
										if($row['contractor_employed'] == 0)
										{
												echo '<form class="job-application-form" action="employ-contractor.php" method="post">';

												while($application_row = mysqli_fetch_array($applications_query))
												{
														echo '<input type="radio" name="contractor-id" value="' . $application_row['user_id'] . '" class="job-application-selection" /><a href="home.php?profile_id=' . $application_row['user_id'] . '">' . $application_row['first_name'] . '&nbsp;' . $application_row['last_name'] . '</a><br/ >';
												}

												echo '<br /><input type="submit" value="Employ" class="w3-button w3-block w3-theme-l4" style="display:inline-block; width:auto;" />';
												echo '<input type="hidden" name="job-id" value="' . $row['id'] . '" />';
												echo '</form>';
										}
										else
										{
												while($application_row = mysqli_fetch_array($applications_query))
												{
														if($row['contractor_employed'] == $application_row['user_id'])
														{
																echo '<b><a href="home.php?profile_id=' . $application_row['user_id'] . '">' . $application_row['first_name'] . '&nbsp;' . $application_row['last_name'] . '</a> (Job offered)</b>';
														}
														else
														{
																echo '<a href="home.php?profile_id=' . $application_row['user_id'] . '">' . $application_row['first_name'] . '&nbsp;' . $application_row['last_name'] . '</a>';
														}
												}
										}
									}
								}

							?>
							<br />
						</div>
					</div>
					<br />
					<?php endif; endif; ?>

				</div>

				<div class="w3-col m7">
					<?php if($user_type == 'contractor') : ?>
						<div class="w3-container w3-card w3-white w3-round w3-margin work-experience-container">
							<h3 class="work-experience-title">Personal Statement</h3>
							<?php echo nl2br($profile->bio); ?>
								<br />
								<br />
						</div>
					<?php else : ?>
						<div class="w3-container w3-card w3-white w3-round w3-margin work-experience-container">
							<h3 class="work-experience-title">About us</h3>
							<?php echo nl2br($profile->company_description); ?>
								<br />
								<br />
						</div>
					<?php endif; ?>
				</div>

				<?php if(!isset($_GET['profile_id'])) : ?>
					<div class="w3-col m2">
						<?php if($user_type == 'employer') : ?>
							<div class="w3-card w3-round w3-white w3-center">
								<div class="w3-container">
									<h5>Search</h5>
									<form action="#" method="POST" class="search-form">
										<span style="text-align:left; display:block;">Skill:</span>
										<input type="text" name="search" id="search" <?php echo ($_POST['search']) ? 'value="' . $_POST['search'] . '"' : ''; ?> />
										<button type="submit" class="w3-button w3-block w3-theme-l4">Search Contractors</button>
									</form>
									
									<?php if($_POST['search']) : ?>
									<h5 style="text-align:left;">Results</h5>
									
									<?php
									
										$search = $_POST['search'];
									
										$search_sql   = "SELECT user_id, first_name, last_name FROM contractors WHERE skills LIKE '%$search%' ORDER BY rand() LIMIT 10;";
										$search_query = mysqli_query($login_connect, $search_sql);
									
										while($search_row = mysqli_fetch_array($search_query))
										{
												echo '<p style="text-align:left;margin:0 0 0.5em 0; font-size: 12px;"><a href="home.php?profile_id=' . $search_row['user_id'] . '">' . $search_row['first_name'] . '&nbsp;' . $search_row['last_name'] . '</a></p>';
										}

									?>
									
									<?php endif; ?>
									
								</div>
							</div>
							<br />
						
							<div class="w3-card w3-round w3-white w3-center">
								<div class="w3-container">
									<h5>Create job</h5>
									<form action="job-create.php" method="POST">
										<div class="job-inputs">
											<label for="job-title">Title:</label>
											<input type="text" name="job-title" id="job-title">
											<label for="job-location">Location:</label>
											<input type="text" name="job-location" value="<?php echo $profile->company_location; ?>" id="job-location">
											<label for="job-description">Description:</label>
											<textarea name="job-description"></textarea>
											<label for="job-salary">Salary:</label>
											<input type="number" name="job-salary" id="job-salary">
											<input type="hidden" name="employer-id" value="<?php echo $user->id; ?>" />
										</div>
										<button type="submit" class="w3-button w3-block w3-theme-l4">Create job</button>
									</form>
								</div>
							</div>
							<br />

						<?php else : ?>

						<?php

							$job_sql   = "SELECT * from jobs WHERE id NOT IN (SELECT job_id FROM applications WHERE contractor_id = " . $user->id . ") AND contractor_employed = 0 ORDER BY rand() LIMIT 3";
							$job_query = mysqli_query($login_connect, $job_sql);

							while($row = mysqli_fetch_array($job_query))
							{
								?>

								<div class="w3-card w3-round w3-white w3-center">
									<div class="w3-container">
										<h5><strong><?php echo $row['title']; ?></strong></h5>
										<p>
											<?php echo $row['location']; ?>
										</p>
										<p class="job-description">
											<?php echo nl2br($row['description']); ?>
										</p>
										<p>&pound;
											<?php echo number_format($row['salary']); ?>
										</p>
										<p><a href="home.php?profile_id=<?php echo $row['employer_id']; ?>">View Employer</a></p>
										<p><a href="job-application.php?job=<?php echo $row['id']; ?>&contractor=<?php echo $profile_id; ?>" class="w3-button w3-block w3-theme-l4">Apply</a></p>
									</div>
								</div>
								<br />

								<?php
							}

							endif;

						?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</body>
</html>
