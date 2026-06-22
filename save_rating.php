<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
    header('Location: login.php');
    exit;
}

$request_id = (int)($_POST['request_id'] ?? 0);
$code = trim($_POST['code'] ?? '');
$score = (int)($_POST['score'] ?? 0);
$comment = trim($_POST['comment'] ?? '');

if ($request_id <= 0 || empty($code) || $score < 1 || $score > 5) {
    header('Location: rating.php?id=' . $request_id . '&error=invalid');
    exit;
}

$pdo = getDB();

$stmt = $pdo->prepare("
    SELECT id, walker_id, rating_code, rating_code_used
    FROM walk_requests
    WHERE id = ? AND owner_id = ? AND status = 'completed'
");
$stmt->execute([$request_id, $_SESSION['user_id']]);
$request = $stmt->fetch();

if (!$request) {
    header('Location: index.php');
    exit;
}

if ($request['rating_code_used']) {
    header('Location: index.php?msg=already_rated');
    exit;
}

//Compare the code entered by the user with the code from the database, for timing attack protection
if (!hash_equals($request['rating_code'], $code)) {
    header('Location: rating.php?id=' . $request_id . '&error=wrong_code');
    exit;
}

//Check if there is already a rating for this request
$existing = $pdo->prepare("SELECT id FROM ratings WHERE request_id = ?");
$existing->execute([$request_id]);
if ($existing->fetch()) {
    header('Location: index.php?msg=already_rated');
    exit;
}

$ins = $pdo->prepare("
    INSERT INTO ratings (request_id, owner_id, walker_id, score, comment)
    VALUES (?, ?, ?, ?, ?)
");
$ins->execute([$request_id, $_SESSION['user_id'], $request['walker_id'], $score, $comment ?: null]);

//Lock the code
$upd = $pdo->prepare("UPDATE walk_requests SET rating_code_used = 1 WHERE id = ?");
$upd->execute([$request_id]);

header('Location: index.php?msg=rated');
exit;
