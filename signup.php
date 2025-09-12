<?php
session_start();
include("db.php");

$msg = "";
$popupClass = "";
if (isset($_POST['signup'])) {
    
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $msg = "Email already exists!";
        $popupClass = "error";
    } else {
        if ($conn->query("INSERT INTO users (name, email, password) VALUES ('$name','$email','$password')")) {
            $msg = "Signup successful! <a href='login.php'>Login here</a>";
            $popupClass = "success";
        } else {
            $msg = "Something went wrong. Try again!";
            $popupClass = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SignUp | Complaint Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h2>Complaint Management System</h2>
        <h3>Create Account</h3>  

        <?php if ($msg) echo "<div class='popup $popupClass'>$msg</div>"; ?>

        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit" name="signup">Signup</button>
        </form>

        <p class="links">
            Already have an account? <a href="./index.html">Login here</a>
        </p>
    </div>
</body>
</html>