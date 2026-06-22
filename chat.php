<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['owner', 'walker'])) {
    header('Location: login.php');
    exit;
}

$pdo        = getDB();
$userId     = $_SESSION['user_id'];
$requestId  = (int) ($_GET['request_id'] ?? 0);

// Proveri da korisnik ima pravo da vidi ovaj razgovor (mora biti owner ili walker tog zahteva)
$stmt = $pdo->prepare("
    SELECT wr.*, 
           o.first_name AS owner_first, o.last_name AS owner_last,
           w.first_name AS walker_first, w.last_name AS walker_last
    FROM walk_requests wr
    JOIN users o ON o.id = wr.owner_id
    LEFT JOIN users w ON w.id = wr.walker_id
    WHERE wr.id = ? AND (wr.owner_id = ? OR wr.walker_id = ?)
");
$stmt->execute([$requestId, $userId, $userId]);
$request = $stmt->fetch();

if (!$request) {
    header('Location: messages.php');
    exit;
}

// Odredi ko je "druga strana" u razgovoru
if ($_SESSION['user_role'] === 'owner') {
    $otherId   = $request['walker_id'];
    $otherName = $request['walker_first'] . ' ' . $request['walker_last'];
} else {
    $otherId   = $request['owner_id'];
    $otherName = $request['owner_first'] . ' ' . $request['owner_last'];
}

// Oznaci sve poruke upucene meni kao procitane
$markRead = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE request_id = ? AND receiver_id = ?");
$markRead->execute([$requestId, $userId]);
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Razgovor sa <?= htmlspecialchars($otherName) ?> – DogWalk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .chat-wrap {
            max-width: 700px;
            margin: 0 auto;
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .chat-header {
            background: #5a6a4a;
            color: #fff;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: .75rem;
        }
        .chat-header a { color: #fff; }
        .chat-box {
            height: 420px;
            overflow-y: auto;
            padding: 1.25rem;
            background: #f7f6f2;
            display: flex;
            flex-direction: column;
            gap: .6rem;
        }
        .msg-bubble {
            max-width: 70%;
            padding: .55rem .9rem;
            border-radius: 14px;
            font-size: .9rem;
            line-height: 1.4;
        }
        .msg-bubble.mine {
            align-self: flex-end;
            background: #5a6a4a;
            color: #fff;
            border-bottom-right-radius: 4px;
        }
        .msg-bubble.theirs {
            align-self: flex-start;
            background: #fff;
            border: 1px solid #e5e5e5;
            border-bottom-left-radius: 4px;
        }
        .msg-time {
            font-size: .68rem;
            opacity: .65;
            margin-top: 2px;
            display: block;
        }
        .chat-form {
            display: flex;
            gap: .6rem;
            padding: 1rem;
            border-top: 1px solid #eee;
            background: #fff;
        }
        .chat-form input {
            flex: 1;
            border: 1.5px solid #ddd;
            border-radius: 24px;
            padding: .6rem 1.1rem;
            font-size: .9rem;
            outline: none;
        }
        .chat-form input:focus { border-color: #5a6a4a; }
        .chat-form button {
            background: #5a6a4a;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 42px; height: 42px;
            flex-shrink: 0;
        }
    </style>
</head>
<body class="bg-light py-4">
<?php include 'navbar.php'; ?>

<div class="chat-wrap mt-4">

    <div class="chat-header">
        <a href="messages.php"><i class="bi bi-arrow-left fs-5"></i></a>
        <div class="conv-avatar" style="width:38px;height:38px;border-radius:50%;background:rgba(255,255,255,.25);display:flex;align-items:center;justify-content:center;font-weight:700;">
            <?= strtoupper(substr($otherName, 0, 1)) ?>
        </div>
        <div>
            <div style="font-weight:700;"><?= htmlspecialchars($otherName) ?></div>
            <div style="font-size:.78rem;opacity:.85;">Pas: <?= htmlspecialchars($request['dog_name']) ?></div>
        </div>
    </div>

    <!-- Poruke se ucitavaju i osvezavaju preko Fetch API u chat.js -->
    <div class="chat-box" id="chatBox" data-request-id="<?= $requestId ?>" data-user-id="<?= $userId ?>">
        <div class="text-center text-muted small py-3">Učitavanje poruka...</div>
    </div>

    <form class="chat-form" id="chatForm">
        <input type="text" id="messageInput" placeholder="Napišite poruku..." autocomplete="off" required maxlength="1000">
        <button type="submit"><i class="bi bi-send-fill"></i></button>
    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="chat.js"></script>
</body>
</html>
