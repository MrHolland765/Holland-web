<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("connection.php");
session_start();

if($_SERVER["REQUEST_METHOD"]=="POST"){
 if (isset($_POST['email']) && isset($_POST['password'])){
$email = trim($_POST['email']);
$password = $_POST['password'];

$statement = $conn->prepare('SELECT * FROM users WHERE Email = ? OR Username = ? OR Phone = ? LIMIT 1');
$statement->bind_param('sss', $email, $email, $email);
$statement->execute();
$result = $statement->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
   
    // Verify hashed password
    if ($password == $row['Password']) {
        $_SESSION['user_email'] = $row['Email'];
        header("Location:home.php");
        exit();
    } else {
        echo "Wrong password!";
    }
} else {
    echo "User not found!";
}
$statement->close();
}
}
$conn->close();
?>