<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

include 'db.php';
$pdo = getDB();

$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role != 'admin'")->fetchColumn();
$totalWalkers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'walker'")->fetchColumn();
$pendingWalkers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'walker' AND is_approved = 0 AND is_active = 1")->fetchColumn();
$completedWalks = $pdo->query("SELECT COUNT(*) FROM walk_requests WHERE status = 'completed'")->fetchColumn();

$walkers = $pdo->query("
    SELECT u.id, u.first_name, u.last_name, u.email, u.is_approved, u.is_banned, u.is_active,
           ROUND((SELECT AVG(score) FROM ratings WHERE walker_id = u.id), 1) as rating,
           (SELECT COUNT(*) FROM walk_requests WHERE walker_id = u.id AND status = 'completed') as walks
    FROM users u
    WHERE u.role = 'walker'
    ORDER BY u.is_approved ASC, u.created_at DESC
")->fetchAll();

$users = $pdo->query("
    SELECT id, first_name, last_name, email, role, is_active, is_banned, created_at
    FROM users
    WHERE role != 'admin'
    ORDER BY created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DogWalk – Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="admin.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">DogWalk Admin</a>
        <div class="d-flex align-items-center gap-3 text-white">
            <span><?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right"></i> Odjava
            </a>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">

        <div class="col-md-2 bg-white border-end min-vh-100 p-3">
            <ul class="nav flex-column nav-pills">
                <li class="nav-item">
                    <a class="nav-link active" href="#overview">
                        <i class="bi bi-grid"></i> Pregled
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#walkers">
                        <i class="bi bi-person-badge"></i> Šetači
                        <?php if ($pendingWalkers > 0): ?>
                            <span class="badge bg-danger ms-1"><?= $pendingWalkers ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#users">
                        <i class="bi bi-people"></i> Korisnici
                    </a>
                </li>
            </ul>
        </div>

        <div class="col-md-10 p-4">

            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-success alert-dismissible">
                    <?php
                    $msgs = [
                            'approved' => 'Šetač je odobren.',
                            'rejected' => 'Šetač je odbijen.',
                            'banned' => 'Korisnik je zaključan.',
                            'unbanned' => 'Korisnik je otključan.',
                    ];
                    echo htmlspecialchars($msgs[$_GET['msg']] ?? 'Akcija izvrsena.');
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <h3 class="mb-4" id="overview">Pregled sistema</h3>
            <div class="row g-3 mb-5">
                <div class="col-md-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-people fs-2 text-primary"></i>
                            <h4 class="mt-2"><?= $totalUsers ?></h4>
                            <p class="text-muted mb-0">Korisnici</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-person-badge fs-2 text-success"></i>
                            <h4 class="mt-2"><?= $totalWalkers ?></h4>
                            <p class="text-muted mb-0">Šetači</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-hourglass-split fs-2 text-warning"></i>
                            <h4 class="mt-2"><?= $pendingWalkers ?></h4>
                            <p class="text-muted mb-0">Na čekanju</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-activity fs-2 text-info"></i>
                            <h4 class="mt-2"><?= $completedWalks ?></h4>
                            <p class="text-muted mb-0">Završene šetnje</p>
                        </div>
                    </div>
                </div>
            </div>

            <h4 class="mb-3" id="walkers">Upravljanje šetačima</h4>
            <div class="table-responsive mb-5">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                    <tr>
                        <th>Šetač</th><th>Email</th><th>Ocena</th>
                        <th>Šetnje</th><th>Status</th><th>Akcije</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($walkers as $w): ?>
                        <tr>
                            <td><?= htmlspecialchars($w['first_name'] . ' ' . $w['last_name']) ?></td>
                            <td><?= htmlspecialchars($w['email']) ?></td>
                            <td><?= $w['rating'] ?? '-' ?></td>
                            <td><?= $w['walks'] ?></td>
                            <td>
                                <?php if (!$w['is_active']): ?>
                                    <span class="badge bg-secondary">Nije aktiviran</span>
                                <?php elseif ($w['is_banned']): ?>
                                    <span class="badge bg-danger">Zaključan</span>
                                <?php elseif (!$w['is_approved']): ?>
                                    <span class="badge bg-warning text-dark">Na čekanju</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Aktivan</span>
                                <?php endif; ?>
                            </td>
                            <td class="d-flex gap-1">
                                <?php if (!$w['is_approved'] && !$w['is_banned'] && $w['is_active']): ?>
                                    <a href="approve.php?id=<?= $w['id'] ?>" class="btn btn-success btn-sm">
                                        <i class="bi bi-check-lg"></i> Odobri
                                    </a>
                                    <a href="reject.php?id=<?= $w['id'] ?>" class="btn btn-danger btn-sm"
                                       onclick="return confirm('Sigurno zelite odbiti ovog setaca?')">
                                        <i class="bi bi-x-lg"></i> Odbij
                                    </a>
                                <?php elseif ($w['is_banned']): ?>
                                    <a href="unban_user.php?id=<?= $w['id'] ?>" class="btn btn-success btn-sm">
                                        <i class="bi bi-unlock"></i> Otključaj
                                    </a>
                                <?php else: ?>
                                    <a href="ban_user.php?id=<?= $w['id'] ?>" class="btn btn-warning btn-sm"
                                       onclick="return confirm('Sigurno zelite zakljucati ovog korisnika?')">
                                        <i class="bi bi-lock"></i> Zaključaj
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($walkers)): ?>
                        <tr><td colspan="6" class="text-center text-muted">Nema šetača.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <h4 class="mb-3" id="users">Upravljanje korisnicima</h4>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-dark">
                    <tr>
                        <th>Korisnik</th><th>Email</th><th>Uloga</th>
                        <th>Registrovan</th><th>Status</th><th>Akcije</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr class="<?= $u['is_banned'] ? 'table-secondary' : '' ?>">
                            <td><?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= $u['role'] === 'walker' ? 'Šetač' : 'Vlasnik' ?></td>
                            <td><?= date('d.m.Y', strtotime($u['created_at'])) ?></td>
                            <td>
                                <?php if ($u['is_banned']): ?>
                                    <span class="badge bg-danger">Zaključan</span>
                                <?php elseif (!$u['is_active']): ?>
                                    <span class="badge bg-secondary">Nije aktiviran</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Aktivan</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($u['is_banned']): ?>
                                    <a href="unban_user.php?id=<?= $u['id'] ?>" class="btn btn-success btn-sm">
                                        <i class="bi bi-unlock"></i> Otključaj
                                    </a>
                                <?php else: ?>
                                    <a href="ban_user.php?id=<?= $u['id'] ?>" class="btn btn-warning btn-sm"
                                       onclick="return confirm('Sigurno zelite zakljucati ovog korisnika?')">
                                        <i class="bi bi-lock"></i> Zaključaj
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
