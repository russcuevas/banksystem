<?php
include '../config/connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM wallet WHERE user_id = ?");
    $stmt->execute([$id]);

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: view_users.php");
    exit();
}
?>

