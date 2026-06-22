<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

include 'db.php';
$pdo = getDB();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    // SQL injection protection
    $stmt = $pdo->prepare("UPDATE users SET is_approved = 1 WHERE id = ? AND role = 'walker'");
    $stmt->execute([$id]);
}

header('Location: admin.php?msg=approved');
exit;

