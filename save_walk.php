<?php
session_start();
require_once 'db.php';
require_once 'mail_helper.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'walker') {
    header('Location: login.php');
    exit;
}

$request_id = (int)($_POST['request_id'] ?? 0);
$walk_description = trim($_POST['walk_description']  ?? '');
$route = trim($_POST['walk_route'] ?? '');
$duration = (int)($_POST['duration_minutes'] ?? 0);

if ($request_id <= 0 || empty($walk_description) || $duration <= 0) {
    header('Location: walker_request.php?msg=error');
    exit;
}

$pdo = getDB();

// Checking that the request belongs to this setter and that it is in the status 'accepted'
$chk = $pdo->prepare("SELECT id, owner_id FROM walk_requests WHERE id = ? AND walker_id = ? AND status = 'accepted'");
$chk->execute([$request_id, $_SESSION['user_id']]);
$req = $chk->fetch();

if (!$req) {
    header('Location: walker_request.php');
    exit;
}

// Route log entry - column called 'route'
$stmt = $pdo->prepare("
    INSERT INTO walk_logs (request_id, walker_id, walk_description, route, duration_minutes)
    VALUES (?, ?, ?, ?, ?)
");
$stmt->execute([$request_id, $_SESSION['user_id'], $walk_description, $route ?: null, $duration]);

// Generating a secure rating code and updating the request status
$code = bin2hex(random_bytes(32));

$upd = $pdo->prepare("
    UPDATE walk_requests
    SET status = 'completed', rating_code = ?
    WHERE id = ?
");
$upd->execute([$code, $request_id]);

// Send code to owner by email
$owner = $pdo->prepare("SELECT email, first_name FROM users WHERE id = ?");
$owner->execute([$req['owner_id']]);
$ownerData = $owner->fetch();

if ($ownerData) {
    $ratingLink = app_url('rating.php?id=' . $request_id);
    $body = "Zdravo {$ownerData['first_name']},\r\n\r\n"
          . "Šetnja je završena! Možete oceniti šetača unosom sledećeg koda:\r\n\r\n"
          . "KOD: {$code}\r\n\r\n"
          . "Link za ocenjivanje: {$ratingLink}\r\n\r\n"
          . "Hvala što koristite DogWalk!";

    send_app_mail(
        $ownerData['email'],
        $ownerData['first_name'],
        'Vaš kod za ocenjivanje šetača - DogWalk',
        $body
    );
}

header('Location: walker_request.php?msg=walk_saved');
exit;
