<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$total = $pending = $resolved = $active_staff = 0;

$result = $conn->query("SELECT status, COUNT(*) as count FROM complaints GROUP BY status");
while($row = $result->fetch_assoc()){
    if($row['status'] == 'pending'){
        $pending = $row['count'];
    } elseif($row['status'] == 'resolved'){
        $resolved = $row['count'];
    }
    $total += $row['count'];
}

$staffResult = $conn->query("SELECT COUNT(*) as count FROM users WHERE role='staff'");
$active_staff = ($staffResult) ? $staffResult->fetch_assoc()['count'] : 0;

$complaints = $conn->query("
    SELECT id, title, status, created_at , assigned_to
    FROM complaints 
    ORDER BY created_at DESC 
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Complaint Management System</title>
    <link rel="stylesheet" href="adminDashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Complaint Management System - Admin Dashboard</h1>
            <nav>
                <ul>
                    <li><a href="admin_dashboard.php" class="active">Dashboard</a></li>
                    <li><a href="manage_complaints.php">Manage Complaints</a></li>
                    <li><a href="assign_task.php">Assign Tasks</a></li>
                    <li><a href="manage_users.php">Manage Users</a></li>
                    <li><a href="reports.php">Reports</a></li>
                    <li><a href="index.php">Logout</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <section class="overview">
                <h2>System Overview</h2>
                <div class="cards">
                    <div class="card">
                        <h3>Total Complaints</h3>
                        <p><?php echo $total; ?></p>
                    </div>
                    <div class="card">
                        <h3>Pending Complaints</h3>
                        <p><?php echo $pending; ?></p>
                    </div>
                    <div class="card">
                        <h3>Resolved Complaints</h3>
                        <p><?php echo $resolved; ?></p>
                    </div>
                    <div class="card">
                        <h3>Active Staff</h3>
                        <p><?php echo $active_staff; ?></p>
                    </div>
                </div>
            </section>

            <section class="recent">
                <h2>Recent Complaints</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $complaints->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['title']; ?></td>
                            <td>
                                <?php 
                                if ($row['status'] == 'pending') {
                                    echo "<span class='status pending'>Pending</span>";
                                } elseif ($row['status'] == 'in-progress') {
                                    echo "<span class='status progress'>In Progress</span>";
                                } elseif ($row['status'] == 'resolved') {
                                    echo "<span class='status resolved'>Resolved</span>";
                                }
                                ?>
                            </td>
                            <td><?php echo $row['assigned_to']; ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>
