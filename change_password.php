<?php
include('connection.php');
session_start();

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';

    if (!empty($email) && !empty($old_password) && !empty($new_password)) {
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($old_password == $row['Password']) {
                $update_sql = "UPDATE users SET Password = '$new_password' WHERE Email = '$email'";
                if ($conn->query($update_sql) === TRUE) {
                    $message = "<div class='alert success'>Password imebadilishwa kikamilifu!</div>";
                } else {
                    $message = "<div class='alert danger'>Kosa: " . $conn->error . "</div>";
                }
            } else {
                $message = "<div class='alert danger'> Old Password is Wrong!</div>";
            }
        } else {
            $message = "<div class='alert danger'>User mwenye email hii hakupatikana!</div>";
        }
    } else {
        $message = "<div class='alert danger'>Tafadhali jaza nafasi zote!</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Badili Password - Duka Langu</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: linear-gradient(135deg, #b9eee6 0%, #ffd9a8 100%); background-attachment: fixed; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .card { background: rgba(255, 250, 240, 0.94); padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h2 { color: #11b0b0; margin-bottom: 20px; text-align: center; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; color: #333; font-weight: bold; }
        input[type="email"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px; }
        .btn { background-color: #11b0b0; color: white; border: none; padding: 12px; width: 100%; border-radius: 5px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn:hover { background-color: #0e8f8f; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 5px; font-size: 14px; text-align: center; }
        .success { background-color: #d4edda; color: #155724; }
        .danger { background-color: #f8d7da; color: #721c24; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #11b0b0; text-decoration: none; font-weight: bold; }
    </style>
    <link rel="stylesheet" href="css/change_password.css">
</head>
<body>
    <div class="card">
        <h2>Change Password</h2>
        <?php echo $message; ?>
        <form action="change_password.php" method="POST">
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required placeholder="Email">
            </div>
            <div class="form-group">
                <label>Old Password:</label>
                <input type="password" name="old_password" required placeholder="Enter old Password">
            </div>
            <div class="form-group">
                <label>New Password:</label>
                <input type="password" name="new_password" required placeholder="Enter New Password">
            </div>
            <button type="submit" class="btn">Done</button>
        </form>
        <a href="home.php" class="back-link">← Back to Home page</a>
    </div>
</body>
</html>
