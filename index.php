<?php
session_start();
include("db.php");

$msg = "";
$popupClass = "";

if (isset($_POST['login'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // email: sajidulislam2225@gmail.com
    // password: sajid

    //email: admin@gmail.com
    //password: admin

    //email: staff@gmail.com
    //password: staff

    $result = $conn->query("SELECT * FROM users WHERE email='$email'");

    if ($result && $result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($user['role'] == 'staff') {
                header("Location: staff_dashboard.php");
            } else {
                header("Location: student_dashboard.php");
            }
            exit;
        } else {
            $msg = "Invalid password!";
            $popupClass = "error";
        }
    } else {
        $msg = "User not found!";
        $popupClass = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Complaint Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h2>Complaint Management System</h2>
        <h3>Login</h3>

        <?php if ($msg !== ""): ?>
            <div class="popup <?php echo $popupClass; ?>"><?php echo $msg; ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit" name="login">Login</button>
        </form>

        <p class="links">
            <a href="./signup.php">Create an Account</a> | 
            <a href="#">Forgot Password?</a>
        </p>
    </div>
</body>
</html>