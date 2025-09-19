<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];
    $user_id = $_SESSION['user_id'];

    if (!empty($title) && !empty($description) && !empty($category_id)) {
        $sql = "INSERT INTO complaints (user_id, category_id, title, description, status) 
                VALUES ('$user_id', '$category_id', '$title', '$description', 'pending')";
        if ($conn->query($sql) === TRUE) {
            $message = "<p class='success'>Complaint submitted successfully!</p>";
        } else {
            $message = "<p class='error'>Error: " . $conn->error . "</p>";
        }
    } else {
        $message = "<p class='error'>All fields are required.</p>";
    }
}

$categories = $conn->query("SELECT id, name FROM categories");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Submit Complaint</title>
    <link rel="stylesheet" href="complaintApplication.css">
</head>
<body>

        <header>
            <div class="headText">Complaint Management System</div>
            <nav>
                <a href="complaintApplication.php">Submit Complaint</a>
                <a href="#">Complaint History</a>
                <a href="#">Profile</a>
                <a href="index.php">Logout</a>
            </nav>
        </header>

    <div class="container">
        
        <main>
            <h2>Submit a New Complaint</h2>
            <?php echo $message; ?>
            <form method="POST" class="complaint-form">
                <label>Complaint Title:</label>
                <input type="text" name="title" required>

                <label>Description:</label>
                <textarea name="description" rows="5" required></textarea>

                <label>Category:</label>
                <select name="category_id" required>
                    <option value="">-- Select Category --</option>
                    <?php while($cat = $categories->fetch_assoc()) { ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                    <?php } ?>
                </select>

                <button type="submit">Submit Complaint</button>
            </form>
        </main>
    </div>
</body>
</html>
