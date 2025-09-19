<?php

include "db.php";

$msg = "";
$popupClass = "";

if (isset($_POST['update'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];

    $result = $conn->query("SELECT * FROM users WHERE email='$email'");

    if ($result && $result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($oldPassword, $user['password'])) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $conn->query("UPDATE users SET password='$hashedPassword' WHERE email='$email'");

            $msg = "Password updated successfully!";
            $popupClass = "success";
        } else {
            $msg = "Old password is incorrect!";
            $popupClass = "error";
        }
    } else {
        $msg = "No account found with that email!";
        $popupClass = "error";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password | Complaint Management System</title>
    <link rel="stylesheet" href="forgetPassword.css">
</head>
<body>
    <div class="login-container">
        <h2>Forget Password</h2>

        <?php if ($msg !== ""): ?>
            <div class="popup <?php echo $popupClass; ?>"><?php echo $msg; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="name">Email</label>
            <input type="email" name="email" placeholder="Enter Your Email" required>

            <label for="password">Old Password</label>
            <input type="password" name="old_password" placeholder="Enter Old Password" required>

            <label for="password">New Password</label>
            <input type="password" name="new_password" placeholder="Enter New Password" required>

            <button type="submit" name="update">Update</button>

            <p class="links">
                <a href="./index.php">Back to Login</a>
            </p>
        </form>
        
    </div>
    
</body>
</html>