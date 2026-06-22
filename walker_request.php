<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'walker') {
    header('Location: login.php');
    exit;
}

$pdo = getDB();

if (isset($_GET['accept'])) {
    $rid = (int)$_GET['accept'];
    $upd = $pdo->prepare("
        UPDATE walk_requests SET status = 'accepted'
        WHERE id = ? AND walker_id = ? AND status = 'pending'
    ");
    $upd->execute([$rid, $_SESSION['user_id']]);
    header('Location: walker_request.php?msg=accepted');
    exit;
}

if (isset($_GET['cancel'])) {
    $rid = (int)$_GET['cancel'];
    $upd = $pdo->prepare("
        UPDATE walk_requests SET status = 'cancelled'
        WHERE id = ? AND walker_id = ? AND status IN ('pending','accepted')
    ");
    $upd->execute([$rid, $_SESSION['user_id']]);
    header('Location: walker_request.php?msg=cancelled');
    exit;
}

$stmt = $pdo->prepare("
    SELECT wr.*, u.first_name, u.last_name, u.phone, u.email
    FROM walk_requests wr
    JOIN users u ON u.id = wr.owner_id
    WHERE wr.walker_id = ?
    ORDER BY wr.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$rows = $stmt->fetchAll();

$statusLabels = [
    'pending'=> ['label' => 'Na čekanju','badge' => 'warning text-dark'],
    'accepted'=> ['label' => 'Prihvaćen','badge' => 'info text-dark'],
    'completed'=> ['label' => 'Završen','badge' => 'success'],
    'cancelled'=> ['label' => 'Otkazan','badge' => 'secondary'],
];
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>DogWalk – Moji zahtevi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<main class="container py-5">
    <h1 class="mb-4 fw-bold">Zahtevi za šetnju</h1>

    <?php if (isset($_GET['msg'])): ?>
        <?php $msgs = ['accepted'=>'Zahtev prihvaćen.','cancelled'=>'Zahtev otkazan.','walk_saved'=>'Šetnja je sačuvana!']; ?>
        <div class="alert alert-success"><?= htmlspecialchars($msgs[$_GET['msg']] ?? '') ?></div>
    <?php endif; ?>

    <?php if (empty($rows)): ?>
        <p class="text-muted">Nema zahteva za šetnju.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                <tr>
                    <th>Vlasnik</th>
                    <th>Kontakt</th>
                    <th>Pas</th>
                    <th>Rasa / Pol / God.</th>
                    <th>Opis</th>
                    <th>Termin</th>
                    <th>Status</th>
                    <th>Akcije</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $r): ?>
                    <?php $s = $statusLabels[$r['status']] ?? ['label'=>$r['status'],'badge'=>'secondary']; ?>
                    <tr>
                        <td><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></td>
                        <td>
                            <small>
                                <?= htmlspecialchars($r['email']) ?><br/>
                                <?= htmlspecialchars($r['phone'] ?? '-') ?>
                            </small>
                        </td>
                        <td><?= htmlspecialchars($r['dog_name']) ?></td>
                        <td>
                            <?= htmlspecialchars($r['dog_breed']) ?><br/>
                            <small class="text-muted">
                                <?= $r['dog_gender'] === 'male' ? 'Muški' : 'Ženski' ?>,
                                <?= (int)$r['dog_age'] ?> god.
                            </small>
                        </td>
                        <td><small><?= htmlspecialchars(mb_strimwidth($r['description'] ?? '', 0, 80, '...')) ?></small></td>
                        <td><small><?= $r['scheduled_at'] ? date('d.m.Y H:i', strtotime($r['scheduled_at'])) : '-' ?></small></td>
                        <td><span class="badge bg-<?= $s['badge'] ?>"><?= $s['label'] ?></span></td>
                        <td>
                            <?php if ($r['status'] === 'pending'): ?>
                                <a href="walker_request.php?accept=<?= $r['id'] ?>" class="btn btn-success btn-sm">
                                    <i class="bi bi-check-lg"></i> Prihvati
                                </a>
                                <a href="walker_request.php?cancel=<?= $r['id'] ?>" class="btn btn-outline-secondary btn-sm"
                                   onclick="return confirm('Otkazati ovaj zahtev?')">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                            <?php elseif ($r['status'] === 'accepted'): ?>
                                <a href="walk_log.php?id=<?= $r['id'] ?>" class="btn btn-primary btn-sm">
                                    <i class="bi bi-journal-check"></i> Završi šetnju
                                </a>
                            <?php elseif ($r['status'] === 'completed'): ?>
                                <span class="text-muted small">Ocena: <?= $r['rating_code_used'] ? '✓' : 'čeka' ?></span>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>