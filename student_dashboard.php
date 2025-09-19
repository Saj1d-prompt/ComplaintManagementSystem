<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$total = $pending = $in_progress = $resolved = 0;
$result = $conn->query("SELECT status, COUNT(*) as count FROM complaints WHERE user_id=$user_id GROUP BY status");
while($row = $result->fetch_assoc()){
    if($row['status']=='pending'){
        $pending = $row['count'];
    }  
    elseif($row['status']=='in-progress') {
        $in_progress = $row['count'];
    }
    elseif($row['status']=='resolved'){
        $resolved = $row['count'];
    } 
    
}
$total = $pending + $in_progress + $resolved;

$complaints = $conn->query("SELECT c.id, c.title, c.description, c.status, c.created_at, cat.name AS category 
                            FROM complaints c 
                            JOIN categories cat ON c.category_id = cat.id 
                            WHERE c.user_id=$user_id 
                            ORDER BY c.created_at DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Dashboard | Complaint Management System</title>
<link rel="stylesheet" href="studentDashboard.css">
</head>
<body>

<header>
    <div class="headText">Complaint Management System - Student Dashboard</div>
    <nav>
        <a href="student_dashboard.php">Home</a>
        <a href="complaintApplication.php">Submit Complaint</a>
        <a href="complaintHistory.php">Complaint History</a>
        <a href="updateProfile.php">Update Profile</a>
        <a href="index.php">Logout</a>
    </nav>
</header>

<div class="dashboard-container">

    <div class="summary-cards">
        <div class="card total">
            <h3>Total Complaints</h3>
            <p><?php echo $total; ?></p>
        </div>
        <div class="card pending">
            <h3>Pending</h3>
            <p><?php echo $pending; ?></p>
        </div>
        <div class="card in-progress">
            <h3>In-Progress</h3>
            <p><?php echo $in_progress; ?></p>
        </div>
        <div class="card resolved">
            <h3>Resolved</h3>
            <p><?php echo $resolved; ?></p>
        </div>
    </div>

    <div class="features-overview">
        
        <div class="feature-card">
            <a href="#">
                <h4>Submit Complaint</h4>
                <p>Report a new issue by providing title, description, and category.</p>
            </a>
        </div>
        <div class="feature-card">
            <a href="#">
                <h4>Complaint History</h4>
                <p>Track your submitted complaints and check their current status.</p>
            </a>
        </div>
        <div class="feature-card">
            <a href="#">
                <h4>Profile</h4>
                <p>Update your personal information securely, like name or email.</p>
            </a>
        </div>
    </div>

    <div class="recent-complaints">
        <h3>Recent Complaints</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Submitted At</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $complaints->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['category']; ?></td>
                    <td><?php echo $row['title']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                    <td>
                        <?php 
                        if($row['status']=='pending') echo "<span class='status-pending'>Pending</span>";
                        elseif($row['status']=='in-progress') echo "<span class='status-in-progress'>In-Progress</span>";
                        else echo "<span class='status-resolved'>Resolved</span>";
                        ?>
                    </td>
                    <td><?php echo $row['created_at']; ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
