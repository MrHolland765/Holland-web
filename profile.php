<?php
session_start();
include('connection.php');

if (empty($_SESSION['user_email'])) {
    header('Location: login.html');
    exit();
}

$current_email = $_SESSION['user_email'];
$message = '';
$profile_key = hash('sha256', strtolower($current_email));
$profile_directory = __DIR__ . '/image/profiles/';
$avatar_url = '';

foreach (['jpg', 'jpeg', 'png', 'webp'] as $extension) {
    $candidate = $profile_directory . $profile_key . '.' . $extension;
    if (is_file($candidate)) {
        $avatar_url = 'image/profiles/' . $profile_key . '.' . $extension;
        break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($fullname === '' || $username === '' || $address === '' || $phone === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '<div class="alert danger">Please fill in all fields correctly.</div>';
    } else {
        $statement = $conn->prepare('UPDATE users SET Fullname = ?, Username = ?, Address = ?, Phone = ?, Email = ? WHERE Email = ?');
        $statement->bind_param('ssssss', $fullname, $username, $address, $phone, $email, $current_email);

        if ($statement->execute()) {
            $_SESSION['user_email'] = $email;
            $current_email = $email;
            $profile_key = hash('sha256', strtolower($current_email));
            $message = '<div class="alert success">Profile updated successfully.</div>';
        } else {
            $message = '<div class="alert danger">Unable to update profile. Username or email may already exist.</div>';
        }
        $statement->close();
    }

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE) {
        $uploaded_file = $_FILES['profile_picture'];
        $image_info = $uploaded_file['error'] === UPLOAD_ERR_OK ? getimagesize($uploaded_file['tmp_name']) : false;
        $allowed_types = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];

        if ($uploaded_file['error'] !== UPLOAD_ERR_OK || $uploaded_file['size'] > 2 * 1024 * 1024 || !$image_info || !isset($allowed_types[$image_info['mime']])) {
            $message = '<div class="alert danger">Choose a JPG, PNG, or WEBP image up to 2MB.</div>';
        } else {
            $extension = $allowed_types[$image_info['mime']];
            $new_avatar = $profile_directory . $profile_key . '.' . $extension;
            if (!is_dir($profile_directory)) {
                mkdir($profile_directory, 0755, true);
            }

            foreach (['jpg', 'jpeg', 'png', 'webp'] as $old_extension) {
                $old_avatar = $profile_directory . $profile_key . '.' . $old_extension;
                if ($old_avatar !== $new_avatar && is_file($old_avatar)) {
                    unlink($old_avatar);
                }
            }

            if (move_uploaded_file($uploaded_file['tmp_name'], $new_avatar)) {
                $avatar_url = 'image/profiles/' . $profile_key . '.' . $extension . '?v=' . filemtime($new_avatar);
                $message = '<div class="alert success">Profile picture uploaded successfully.</div>';
            } else {
                $message = '<div class="alert danger">Profile picture could not be uploaded.</div>';
            }
        }
    }
}

$statement = $conn->prepare('SELECT Fullname, Username, Address, Phone, Email FROM users WHERE Email = ?');
$statement->bind_param('s', $current_email);
$statement->execute();
$user = $statement->get_result()->fetch_assoc();
$statement->close();

if (!$user) {
    session_unset();
    session_destroy();
    header('Location: login.html');
    exit();
}

function escape_value($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Duka Langu</title>
    <style>
        * { box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; background: linear-gradient(135deg, #b9eee6 0%, #ffd9a8 100%); background-attachment: fixed; }
        .card { width: min(100%, 520px); padding: 30px; background: rgba(255, 250, 240, 0.94); border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,.1); }
        h2 { margin: 0 0 22px; text-align: center; color: #2878c8; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 6px; color: #333; font-weight: bold; }
        input { width: 100%; padding: 11px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px; }
        .btn { width: 100%; padding: 12px; border: 0; border-radius: 5px; background: #2878c8; color: white; font-weight: bold; cursor: pointer; }
        .btn:hover { background: #1f5f9f; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 5px; text-align: center; font-size: 14px; }
        .success { background: #d4edda; color: #155724; }
        .danger { background: #f8d7da; color: #721c24; }
        .links { display: flex; justify-content: space-between; gap: 12px; margin-top: 18px; }
        .links a { color: #2878c8; font-weight: bold; text-decoration: none; }
        .links a:hover { text-decoration: underline; }
        .profile-picture { display: block; width: 110px; height: 110px; margin: 0 auto 18px; border-radius: 50%; object-fit: cover; border: 4px solid #d9e9f8; }
        .picture-placeholder { display: grid; place-items: center; background: #eaf3fb; color: #2878c8; font-size: 42px; }
        .picture-upload { margin-bottom: 20px; padding: 14px; border: 1px dashed #2878c8; border-radius: 5px; background: #f7fbff; }
        .picture-upload label { color: #2878c8; }
        @media (max-width: 420px) { .card { padding: 22px; } .links { flex-direction: column; text-align: center; } }
    </style>
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
    <main class="card">
        <h2>Personal Information</h2>
        <?php if ($avatar_url): ?>
            <img class="profile-picture" src="<?php echo escape_value($avatar_url); ?>" alt="Profile picture">
        <?php else: ?>
            <div class="profile-picture picture-placeholder" aria-label="No profile picture">&#128100;</div>
        <?php endif; ?>
        <?php echo $message; ?>
        <form method="post" action="profile.php" enctype="multipart/form-data">
            <div class="picture-upload">
                <label for="profile_picture">Profile picture</label>
                <input id="profile_picture" type="file" name="profile_picture" accept="image/jpeg,image/png,image/webp">
            </div>
            <div class="form-group">
                <label for="fullname">Full name</label>
                <input id="fullname" type="text" name="fullname" value="<?php echo escape_value($user['Fullname']); ?>" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input id="username" type="text" name="username" value="<?php echo escape_value($user['Username']); ?>" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input id="address" type="text" name="address" value="<?php echo escape_value($user['Address']); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input id="phone" type="text" name="phone" value="<?php echo escape_value($user['Phone']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="<?php echo escape_value($user['Email']); ?>" required>
            </div>
            <button class="btn" type="submit">Save Changes</button>
        </form>
        <div class="links">
            <a href="change_password.php">Change Password</a>
            <a href="home.php">Back to Home</a>
        </div>
    </main>
</body>
</html>
