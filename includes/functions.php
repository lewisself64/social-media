<?php

	function isLocal()
	{
		return $_SERVER['SERVER_ADDR'] === $_SERVER['REMOTE_ADDR'];
	}

	if(!isLocal())
	{
		// Database connection details
		$db_host = '10.169.0.169';
		$db_user = 'lewissel_uni';
		$db_name = 'lewissel_uni';
		$db_pass = 'P@55w0rd321';
	}
	else
	{
		// Database connection details
		$db_host = 'localhost';
		$db_user = 'root';
		$db_name = 'social_media';
		$db_pass = '';
	}
  
  // Passes variables through connect function
  $login_connect = mysqli_connect($db_host, $db_user, $db_pass);

  // Selects database
  $select_db = mysqli_select_db($login_connect, $db_name);

  // Function to sanatise the user input (Prevent XXS, SQL Injections)
  function sanatize($data)
  {
    $data = stripslashes($data);
    $data = strip_tags($data);
    $data = htmlentities($data);

    return $data;
  }

	function hash_salt_password($password, $salt)
	{
		// Standard salt used in every password
		$fixed_salt = '2C0ai;sNme@+B9YeEhmXRN7gOV/idk{|F|NheCHyL8VgNW9xqfJ08IOw?Z^[6z1e';
		
		return hash('sha512', $fixed_salt . $salt . $password . $salt . $fixed_salt);
	}

?>