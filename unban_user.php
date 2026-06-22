<?php
session_start();

// Zastita - samo admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

include 'db.php';
$pdo = getDB();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    $stmt = $pdo->prepare("UPDATE users SET is_banned = 0 WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: admin.php?msg=unbanned');
exit;
?>
