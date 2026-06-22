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
    // Admin cannot ban himself
    if ($id == $_SESSION['user_id']) {
        header('Location: admin.php');
        exit;
    }

    $stmt = $pdo->prepare("UPDATE users SET is_banned = 1 WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: admin.php?msg=banned');
exit;

