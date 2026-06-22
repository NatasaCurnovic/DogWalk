<?php
// AJAX endpoint - saves a new message to the database
// Called by chat.js when user submits the chat form

session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];

$data = json_decode(file_get_contents('php://input'), true);
$requestId = (int) ($data['request_id'] ?? 0);
$body = trim($data['body'] ?? '');

if (empty($body)) {
    echo json_encode(['success' => false, 'message' => 'Empty message']);
    exit;
}

$pdo = getDB();

// Check that the user belongs to this conversation and determine who the recipient is
$stmt = $pdo->prepare("
    SELECT owner_id, walker_id FROM walk_requests
    WHERE id = ? AND (owner_id = ? OR walker_id = ?)
");
$stmt->execute([$requestId, $userId, $userId]);
$request = $stmt->fetch();

if (!$request) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}

$receiverId = ($request['owner_id'] == $userId) ? $request['walker_id'] : $request['owner_id'];

if (!$receiverId) {
    echo json_encode(['success' => false, 'message' => 'No recipient']);
    exit;
}

$insert = $pdo->prepare("
    INSERT INTO messages (request_id, sender_id, receiver_id, body, is_read)
    VALUES (?, ?, ?, ?, 0)
");
$insert->execute([$requestId, $userId, $receiverId, $body]);

echo json_encode(['success' => true, 'message_id' => $pdo->lastInsertId()]);

