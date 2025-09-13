<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $complaint_id = intval($_POST['complaint_id']);
    $staff_id = intval($_POST['staff_id']);

    $sql = "UPDATE complaints 
            SET assigned_to = $staff_id, status = 'in-progress' 
            WHERE id = $complaint_id";

    if ($conn->query($sql)) {
        header("Location: assign_task.php?success=1");
    } else {
        header("Location: assign_task.php?error=1");
    }
    exit();
}
?>
