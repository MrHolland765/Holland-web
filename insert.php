<?php
include ('connection.php');

$id = trim($_POST['ID'] ?? '');
$fullname = trim($_POST['Fullname'] ?? '');
$username = trim($_POST['Username'] ?? '');
$address = trim($_POST['Address'] ?? '');
$phone = trim(($_POST['PhoneCode'] ?? '') . ($_POST['Phone'] ?? ''));
$email = trim($_POST['Email'] ?? '');
$password = $_POST['Password'] ?? '';

if ($id === '' || $fullname === '' || $username === '' || $address === '' || $phone === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
	echo "Please fill in all registration fields correctly. <a href='signup.html'>Go back</a>";
	exit();
}

$statement = $conn->prepare('INSERT INTO users (Id, Fullname, Username, Address, Phone, Email, Password) VALUES (?, ?, ?, ?, ?, ?, ?)');
$statement->bind_param('sssssss', $id, $fullname, $username, $address, $phone, $email, $password);

if ($statement->execute()) {
	echo "New record created successfully . <a href='login.html'>login now</a>";

}else{
	echo "Registration failed: " . $statement->error . "<br><a href='signup.html'>Go back</a>";
}
$statement->close();
$conn->close();
?>