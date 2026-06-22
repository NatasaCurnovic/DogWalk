<?php
require_once 'db.php';
header('Content-Type: application/json');

$data  = json_decode(file_get_contents('php://input'), true);
$email = trim($data['email'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['valid' => false, 'exists' => false]);
    exit;
}

$pdo = getDB();
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);

echo json_encode(['valid' => true, 'exists' => (bool)$stmt->fetch()]);
