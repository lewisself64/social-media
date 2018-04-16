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
	$user   			 = mysqli_fetch_object($user_query);

	/* Check what type the user is */

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

<!-- Navbar -->
<div class="w3-top">
 <div class="w3-bar w3-theme-d2 w3-left-align w3-large">
	<a href="#" class="w3-bar-item w3-button w3-padding-large w3-theme-d4"><i class="fa fa-home w3-margin-right"></i>Home</a>
	<div class="w3-dropdown-hover w3-hide-small">
		<button class="w3-button w3-padding-large" title="Account Settings"><i class="fa fa-user w3-margin-right"></i>Settings</button>
		<div class="w3-dropdown-content w3-card-4 w3-bar-block" style="width:300px">
			<a href="#" class="w3-bar-item w3-button">Edit Profile</a>
			<a href="logout.php" class="w3-bar-item w3-button">Log out</a>
		</div>
	</div>
 </div>
</div>

<!-- Page Container -->
<div class="w3-container w3-content" style="max-width:1400px;margin-top:80px">
	<!-- The Grid -->
	<div class="w3-row">
		<!-- Left Column -->
		<div class="w3-col m3">
			<!-- Profile -->
			<div class="w3-card w3-round w3-white">
				<div class="w3-container">
				 <h4 class="w3-center"><?php echo $user->username; ?></h4>
				 <p class="w3-center"><img src="/w3images/avatar3.png" class="w3-circle" style="height:106px;width:106px" alt="Avatar"></p>
				 <hr>
				 <?php
						if($user_type == 'contractor')
						{
							echo '<p><i class="fa fa-address-book fa-fw w3-margin-right w3-text-theme"></i>' . $profile->first_name . ' ' . $profile->last_name . '</p>';
							echo '<p><i class="fa fa-birthday-cake fa-fw w3-margin-right w3-text-theme"></i>' . date("d/m/Y", strtotime($profile->date_of_birth)) . '</p>';

							if($profile->cv)
							{
								echo '<p><i class="fa fa-birthday-cake fa-fw w3-margin-right w3-text-theme"></i> <a href="profiles/cv/' . $profile->cv . '" target="_BLANK">View CV</a></p>';
							}
						}
						elseif($user_type == 'employer')
						{
							echo '<p><i class="fa fa-address-book fa-fw w3-margin-right w3-text-theme"></i> ' . $profile->company_name . '</p>';
						}
				 ?>
				</div>
			</div>
			<br>

			<?php if($user_type == 'contractor') : ?>
				<?php if($profile->skills) : ?>
				<div class="w3-card w3-round w3-white w3-hide-small">
					<div class="w3-container">
						<p>Skills</p>
						<?php $skills = explode(",", json_decode($profile->skills)); ?>
						<p>
							<?php foreach($skills as $skill) : ?>
							<span class="w3-tag w3-small w3-theme-d5"><?php echo $skill ?></span>
							<?php endforeach; ?>
						</p>
					</div>
				</div>
				<br>
				<?php endif; ?>
			<?php endif; ?>
		</div>

		<!-- Middle Column -->
		<div class="w3-col m7">

			<div class="w3-container w3-card w3-white w3-round w3-margin work-experience-container">
				<h3 class="work-experience-title">Personal Statement</h3>
				<?php echo nl2br($profile->bio); ?>
				<br>
				<br>
			</div>

		<!-- End Middle Column -->
		</div>

		<div class="w3-col m2">

			<div class="w3-card w3-round w3-white w3-center">
				<div class="w3-container">
					<h5>Create job</h5>
					<form action="job-create.php" method="POST">
						<div class="job-inputs">
							<label for="job-title">Title:</label>
							<input type="text" name="job-title" id="job-title">
							<label for="job-location">Location:</label>
							<input type="text" name="job-location" id="job-location">
							<label for="job-description">Description:</label>
							<textarea name="job-description"></textarea>
							<label for="job-salary">Salary:</label>
							<input type="number" name="job-salary" id="job-salary">
						</div>
						<button type="submit" class="w3-button w3-block w3-theme-l4">Create job</button>
					</form>
				</div>
			</div>
			<br>

		<?php

			$user_sql   = "SELECT * FROM jobs;";
			$user_query = mysqli_query($login_connect, $user_sql);

			while($row = mysqli_fetch_array($user_query))
			{
				?>

				<div class="w3-card w3-round w3-white w3-center">
					<div class="w3-container">
						<h5><strong><?php echo $row['title']; ?></strong></h5>
						<p><?php echo $row['location']; ?></p>
						<p class="job-description"><?php echo $row['description']; ?> </p>
						<p>&pound;<?php echo number_format($row['salary']); ?></p>
						<p><a href="job-application.php?job=<?php echo $row['id']; ?>&contractor=<?php echo $profile_id; ?>" class="w3-button w3-block w3-theme-l4">Apply</a></p>
					</div>
				</div>
				<br>

				<?php
			}

		?>

		<!-- End Right Column -->
		</div>

	<!-- End Grid -->
	</div>

<!-- End Page Container -->
</div>
</body>
</html>
