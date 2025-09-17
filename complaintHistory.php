<?php
session_start();
include 'db.php';

// Check if logged in as student
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id']; 

// Fetch all complaints for this student
$sql = "SELECT c.id, cat.name as category, c.title, c.description, c.status, c.created_at 
        FROM complaints c
        JOIN categories cat ON c.category_id = cat.id
        WHERE c.user_id = ?   -- ✅ use user_id instead of student_id
        ORDER BY c.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Complaint History</title>
    <link rel="stylesheet" href="complaintHistory.css">
</head>
<body>
<header>
    <div class="headText">Complaint Management System - Student Dashboard</div>
    <nav>
        <a href="complaintApplication.php">Submit Complaint</a>
        <a href="complaintHistory.php">Complaint History</a>
        <a href="#">Profile</a>
        <a href="index.php">Logout</a>
    </nav>
</header>
<div class="container">
    <h2>All Submitted Complaints</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Category</th>
                <th>Title</th>
                <th>Description</th>
                <th>Status</th>
                <th>Date Submitted</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['category']); ?></td>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td>
                    <?php if ($row['status'] === 'pending'): ?>
                        <span class="status pending">Pending</span>
                    <?php elseif ($row['status'] === 'in-progress'): ?>
                        <span class="status in-progress">In Progress</span>
                    <?php else: ?>
                        <span class="status resolved">Resolved</span>
                    <?php endif; ?>
                </td>
                <td><?php echo $row['created_at']; ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
