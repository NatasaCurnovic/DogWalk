<?php
// AJAX endpoint - returns all messages for a request as JSON
// Called repeatedly by chat.js via Fetch API to simulate live chat

session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$userId    = $_SESSION['user_id'];
$requestId = (int) ($_GET['request_id'] ?? 0);

$pdo = getDB();

// Checking that the user belongs to this conversation
$check = $pdo->prepare("
    SELECT id FROM walk_requests
    WHERE id = ? AND (owner_id = ? OR walker_id = ?)
");
$check->execute([$requestId, $userId, $userId]);
if (!$check->fetch()) {
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

// Mark new messages as read
$markRead = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE request_id = ? AND receiver_id = ?");
$markRead->execute([$requestId, $userId]);

// Get all messages for this request, chronologically
$stmt = $pdo->prepare("
    SELECT id, sender_id, receiver_id, body, created_at
    FROM messages
    WHERE request_id = ?
    ORDER BY created_at ASC
");
$stmt->execute([$requestId]);
$messages = $stmt->fetchAll();

echo json_encode(['messages' => $messages]);

