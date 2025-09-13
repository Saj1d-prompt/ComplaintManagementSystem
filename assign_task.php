<?php
session_start();
include("db.php");

// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
//     header("Location: index.php");
//     exit();
// }

// Fetch complaints with student + staff names in one query
$complaints = $conn->query("
    SELECT c.id, c.title, c.status, u.name AS student, s.name AS staff
    FROM complaints c
    JOIN users u ON c.user_id = u.id
    LEFT JOIN users s ON c.assigned_to = s.id
    WHERE c.status IN ('pending','in-progress')
    ORDER BY c.created_at DESC
");

// Fetch staff list
$staff = $conn->query("SELECT id, name FROM users WHERE role='staff'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Tasks | CMS</title>
    <link rel="stylesheet" href="assign_task.css">
</head>
<body>
<div class="dashboard-container">
    <header>
            <h1>Complaint Management System</h1>
            <nav>
                <ul>
                    <li><a href="admin_dashboard.php" class="active">Dashboard</a></li>
                    <li><a href="manage_complaints.php">Manage Complaints</a></li>
                    <li><a href="assign_tasks.php">Assign Tasks</a></li>
                    <li><a href="manage_users.php">Manage Users</a></li>
                    <li><a href="reports.php">Reports</a></li>
                    <li><a href="index.php">Logout</a></li>
                </ul>
            </nav>
        </header>

    <main>
        <h2>Assign Complaints to Staff</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Complaint</th>
                    <th>Student</th>
                    <th>Status</th>
                    <th>Assigned To</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $complaints->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['title']; ?></td>
                    <td><?php echo $row['student']; ?></td>
                    <td>
                        <span class="status <?php echo $row['status']; ?>">
                            <?php echo $row['status']; ?>
                        </span>
                    </td>
                    <td><?php echo $row['staff'] ?: "Not Assigned"; ?></td>
                    <td>
                        <form action="assign_task_action.php" method="POST">
                            <input type="hidden" name="complaint_id" value="<?php echo $row['id']; ?>">
                            <select name="staff_id" required>
                                <option value="">Select Staff</option>
                                <?php 
                                $staff->data_seek(0);
                                while ($s = $staff->fetch_assoc()) {
                                    echo "<option value='{$s['id']}'>{$s['name']}</option>";
                                } 
                                ?>
                            </select>
                            <button type="submit">Assign</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </main>
</div>
</body>
</html>
