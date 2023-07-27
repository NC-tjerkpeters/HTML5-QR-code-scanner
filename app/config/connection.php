<?php
/*
 * Setup your database connection for this web application
 */

$hostdb = "localhost";  // MySQl host
$userdb = "username";  // MySQL username
$passdb = "password";  // MySQL password
$namedb = "databasename";  // MySQL database name

// Establish a connection to the database
$conn = new mysqli($hostdb, $userdb, $passdb, $namedb);

/* Render an error message, to avoid abrupt failure, if the database connection parameters are incorrect */
if ($conn->connect_error) {
	exit("There was an error with your connection: ".$conn->connect_error);
}

?>