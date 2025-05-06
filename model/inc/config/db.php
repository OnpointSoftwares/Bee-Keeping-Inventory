<?php
	// Include constants
	require_once('constants.php');

	// Connect to database
	try {
		$conn = new PDO(DSN, DB_USER, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch(PDOException $e) {
		$errorMessage = $e->getMessage();
		error_log('Database connection error: ' . $errorMessage);
		throw new PDOException('Database connection failed: ' . $errorMessage);
	}

	// Return the connection
	return $conn;
?>