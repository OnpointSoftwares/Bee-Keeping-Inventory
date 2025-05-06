<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	$registerFullName = '';
	$registerUsername = '';
	$registerPassword1 = '';
	$registerPassword2 = '';
	$hashedPassword = '';
	
	if(isset($_POST['registerUsername'])){
		$registerFullName = htmlentities($_POST['registerFullName']);
		$registerUsername = htmlentities($_POST['registerUsername']);
		$registerPassword1 = htmlentities($_POST['registerPassword1']);
		$registerPassword2 = htmlentities($_POST['registerPassword2']);
		
		if(!empty($registerFullName) && !empty($registerUsername) && !empty($registerPassword1) && !empty($registerPassword2)){
			
			// Sanitize name
			//$registerFullName = filter_var($registerFullName, FILTER_SANITIZE_STRING);
			
			// Check if name is empty
			if($registerFullName == ''){
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter your name.</div>';
				exit();
			}
			
			// Check if username is empty
			if($registerUsername == ''){
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter your username.</div>';
				exit();
			}
			
			// Check if both passwords are empty
			if($registerPassword1 == '' || $registerPassword2 == ''){
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter both passwords.</div>';
				exit();
			}
			
			// Check if username is available
			$usernameCheckingSql = 'SELECT * FROM users WHERE username = :username';
			$usernameCheckingStatement = $conn->prepare($usernameCheckingSql);
			$usernameCheckingStatement->execute(['username' => $registerUsername]);
			
			if($usernameCheckingStatement->rowCount() > 0){
				// Username already exists. Hence can't create a new user
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Username not available. Please select a different username.</div>';
				exit();
			} else {
				// Check if passwords are equal
				if($registerPassword1 !== $registerPassword2){
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Passwords do not match.</div>';
					exit();
				} else {
					// Hash the password
					$hashedPassword = password_hash($registerPassword1, PASSWORD_DEFAULT);

					// Insert the new user into the database
					$insertUserSql = 'INSERT INTO users (username, password, full_name) VALUES (:username, :password, :full_name)';
					$insertUserStatement = $conn->prepare($insertUserSql);
					$insertUserStatement->execute([
						'username' => $registerUsername,
						'password' => $hashedPassword,
						'full_name' => $registerFullName
					]);

					echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Registration successful!</div>';
					exit();
				}
			}
		} else {
			// One or more mandatory fields are empty. Therefore, display a the error message
			echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter all fields marked with a (*)</div>';
			exit();
		}
	}
?>