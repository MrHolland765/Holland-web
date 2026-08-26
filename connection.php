<?php
// Database configuration
$servername = "localhost";
$username = "root";     //default for XAMPP/WAMP
$password = "";         //default is empty
$dbname = "login";

// create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// check connection
 if ($conn->connect_error) {
   die("connection failed: " . $conn->connect_error);
 }
?>