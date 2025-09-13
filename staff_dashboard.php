<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'staff') {
    header("Location: index.php");
    exit();
}

$staff_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['complaint_id']);
    $status = $_POST['status'];
    $conn->query("UPDATE complaints SET status='$status' WHERE id=$id AND assigned_to=$staff_id");
}

$counts = ["total"=>0,"pending"=>0,"in-progress"=>0,"resolved"=>0];
$res = $conn->query("SELECT status, COUNT(*) as c FROM complaints WHERE assigned_to=$staff_id GROUP BY status");
while($row = $res->fetch_assoc()){ $counts[$row['status']] = $row['c']; }
$counts["total"] = array_sum($counts);

$complaints = $conn->query("
    SELECT c.id, u.name AS student, cat.name AS category, c.title, c.status, c.created_at
    FROM complaints c
    JOIN users u ON c.user_id = u.id
    JOIN categories cat ON c.category_id = cat.id
    WHERE c.assigned_to = $staff_id
    ORDER BY c.created_at DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Staff Dashboard | Complaint Management System</title>
    <link rel="stylesheet" href="staff_dashboard.css">
</head>
<body>
<header>
    <div>Complaint Management System - Staff Dashboard</div>
    <nav>
        <a href="staff_dashboard.php">Dashboard</a>
        <a href="index.php">Logout</a>
    </nav>
</header>

<div class="container">
    <div class="cards">
        <div class="card total">Total<br><?php echo $counts['total']; ?></div>
        <div class="card pending">Pending<br><?php echo $counts['pending']; ?></div>
        <div class="card in-progress">In-Progress<br><?php echo $counts['in-progress']; ?></div>
        <div class="card resolved">Resolved<br><?php echo $counts['resolved']; ?></div>
    </div>

    <h3>Assigned Complaints</h3>
    <table>
        <tr><th>ID</th><th>Student</th><th>Category</th><th>Title</th><th>Status</th><th>Date</th><th>Action</th></tr>
        <?php while($row=$complaints->fetch_assoc()): ?>
        <tr>
            <td>#<?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['student']); ?></td>
            <td><?php echo htmlspecialchars($row['category']); ?></td>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo ucfirst($row['status']); ?></td>
            <td><?php echo $row['created_at']; ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="complaint_id" value="<?php echo $row['id']; ?>">
                    <select name="status" onchange="this.form.submit()">
                        <option value="pending" <?php if($row['status']=='pending') echo 'selected'; ?>>Pending</option>
                        <option value="in-progress" <?php if($row['status']=='in-progress') echo 'selected'; ?>>In Progress</option>
                        <option value="resolved" <?php if($row['status']=='resolved') echo 'selected'; ?>>Resolved</option>
                    </select>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
