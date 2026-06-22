<?php
session_start();
require_once '../db.php';
require_once '../mail_helper.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'mail_sent' => false, 'message' => 'Invalid method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$email = trim($data['email'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => true,
        'mail_sent' => false,
        'message' => 'Email adresa nije ispravna.'
    ]);
    exit;
}

$pdo = getDB();
$stmt = $pdo->prepare("SELECT id, first_name, is_active FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

$mailSent = false;
$message = 'Nalog sa ovom email adresom ne postoji u bazi.';

if ($user && (int)$user['is_active'] === 1) {
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+30 minutes'));

    $upd = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE id = ?");
    $upd->execute([$token, $expires, $user['id']]);

    $link = app_url('reset_password.php?token=' . urlencode($token));
    $body = "Zdravo {$user['first_name']},\r\n\r\n"
          . "Primili smo zahtev za reset vase lozinke.\r\n"
          . "Kliknite na link ispod (vazi 30 minuta):\r\n\r\n"
          . "{$link}\r\n\r\n"
          . "Ako niste vi trazili reset, ignorisite ovaj email.";

    $mailSent = send_app_mail($email, $user['first_name'], 'Reset lozinke - DogWalk', $body);
    $message = $mailSent
        ? 'Link za reset lozinke je poslat.'
        : 'Nalog postoji, ali slanje maila nije uspelo. ';
} elseif ($user) {
    $message = 'Nalog postoji, ali nije aktiviran. Reset mail se ne salje za neaktivan nalog.';
}

echo json_encode([
    'success' => true,
    'mail_sent' => $mailSent,
    'message' => $message
]);

