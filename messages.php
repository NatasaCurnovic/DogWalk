<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['owner', 'walker'])) {
    header('Location: login.php');
    exit;
}

$pdo    = getDB();
$userId = $_SESSION['user_id'];
$role   = $_SESSION['user_role'];

if ($role === 'owner') {
    $stmt = $pdo->prepare("
        SELECT
            wr.id AS request_id,
            wr.dog_name,
            wr.status,
            u.id AS other_id,
            u.first_name,
            u.last_name,
            (SELECT body FROM messages WHERE request_id = wr.id ORDER BY created_at DESC LIMIT 1) AS last_message,
            (SELECT created_at FROM messages WHERE request_id = wr.id ORDER BY created_at DESC LIMIT 1) AS last_time,
            (SELECT COUNT(*) FROM messages WHERE request_id = wr.id AND receiver_id = ? AND is_read = 0) AS unread_count
        FROM walk_requests wr
        JOIN users u ON u.id = wr.walker_id
        WHERE wr.owner_id = ? AND wr.walker_id IS NOT NULL
        ORDER BY last_time IS NULL, last_time DESC, wr.created_at DESC
    ");
    $stmt->execute([$userId, $userId]);
} else {
    $stmt = $pdo->prepare("
        SELECT
            wr.id AS request_id,
            wr.dog_name,
            wr.status,
            u.id AS other_id,
            u.first_name,
            u.last_name,
            (SELECT body FROM messages WHERE request_id = wr.id ORDER BY created_at DESC LIMIT 1) AS last_message,
            (SELECT created_at FROM messages WHERE request_id = wr.id ORDER BY created_at DESC LIMIT 1) AS last_time,
            (SELECT COUNT(*) FROM messages WHERE request_id = wr.id AND receiver_id = ? AND is_read = 0) AS unread_count
        FROM walk_requests wr
        JOIN users u ON u.id = wr.owner_id
        WHERE wr.walker_id = ?
        ORDER BY last_time IS NULL, last_time DESC, wr.created_at DESC
    ");
    $stmt->execute([$userId, $userId]);
}

$conversations = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poruke – DogWalk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .conv-list { max-width: 700px; margin: 0 auto; }
        .conv-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.25rem;
            background: #fff;
            border-radius: 12px;
            margin-bottom: .75rem;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            transition: transform .15s, box-shadow .15s;
        }
        .conv-item:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,0,0,0.1); color: inherit; }
        .conv-avatar {
            width: 50px; height: 50px; border-radius: 50%;
            background: #5a6a4a; color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; flex-shrink: 0;
        }
        .conv-name { font-weight: 700; font-size: .95rem; }
        .conv-preview { font-size: .85rem; color: #777; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 350px; }
        .conv-unread {
            background: #c0392b; color: #fff; border-radius: 50%;
            width: 22px; height: 22px; display: flex; align-items: center; justify-content: center;
            font-size: .7rem; font-weight: 700; flex-shrink: 0;
        }
    </style>
</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>

<div class="container py-5">
    <h2 class="mb-4">Poruke</h2>

    <div class="conv-list">
        <?php if (empty($conversations)): ?>
            <div class="text-center text-muted py-5">
                <i class="bi bi-chat-dots fs-1 d-block mb-2"></i>
                Nemate još nijedan razgovor.
            </div>
        <?php else: ?>
            <?php foreach ($conversations as $c): ?>
                <a href="chat.php?request_id=<?= $c['request_id'] ?>" class="conv-item">
                    <div class="conv-avatar">
                        <?= strtoupper(substr($c['first_name'], 0, 1) . substr($c['last_name'], 0, 1)) ?>
                    </div>
                    <div class="flex-grow-1">
                        <div class="conv-name">
                            <?= htmlspecialchars($c['first_name'] . ' ' . $c['last_name']) ?>
                            <span class="text-muted fw-normal small">— pas: <?= htmlspecialchars($c['dog_name']) ?></span>
                        </div>
                        <div class="conv-preview">
                            <?= $c['last_message'] ? htmlspecialchars($c['last_message']) : '<i>Nema poruka još</i>' ?>
                        </div>
                    </div>
                    <?php if ($c['unread_count'] > 0): ?>
                        <div class="conv-unread"><?= $c['unread_count'] ?></div>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
