<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debugging statement to confirm script execution
echo 'Debug: checkLogin.php script executed.' . PHP_EOL;

	session_start();
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	$loginUsername = '';
	$loginPassword = '';
	
	if(isset($_POST['loginUsername'])){
		$loginUsername = $_POST['loginUsername'];
		$loginPassword = $_POST['loginPassword'];
		
		if(!empty($loginUsername) && !empty($loginUsername)){
			
			// Sanitize username
			//$loginUsername = filter_var($loginUsername, FILTER_SANITIZE_STRING);
			
			// Check if username is empty
			if($loginUsername == ''){
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter Username</div>';
				exit();
			}
			
			// Check if password is empty
			if($loginPassword == ''){
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter Password</div>';
				exit();
			}
			
			// Check the given credentials
			$checkUserSql = 'SELECT * FROM users WHERE username = :username';
			$checkUserStatement = $conn->prepare($checkUserSql);
			$checkUserStatement->execute(['username' => $loginUsername]);
			$user = $checkUserStatement->fetch(PDO::FETCH_ASSOC);

			echo 'Debug: Trying to log in with username: ' . $loginUsername . PHP_EOL;
			echo 'Debug: Hashed password from database: ' . $user['password'] . PHP_EOL;
			ob_flush(); flush();

			if ($user && password_verify($loginPassword, $user['password'])) {
				// Valid credentials. Hence, start the session
				$_SESSION['loggedIn'] = '1';
				$_SESSION['fullName'] = $user['fullName'];
				
				echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Login success! Redirecting you to home page...</div>';
				exit();
			} else {
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Incorrect Username / Password</div>';
				exit();
			}
			
			
		} else {
			echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter Username and Password</div>';
			exit();
		}
	}
?>