<?php
session_start();
require_once 'db.php';

$walker_id = (int)($_GET['id'] ?? 0);
if ($walker_id <= 0) {
    header('Location: index.php');
    exit;
}

$pdo = getDB();

$stmt = $pdo->prepare("
    SELECT u.id, u.first_name, u.last_name,
           wp.photo, wp.description, wp.favorite_breed,
           wp.experience_years, wp.city, wp.price_per_hour, wp.is_available,
           COALESCE(ROUND(AVG(r.score), 1), 0) AS avg_score,
           COUNT(DISTINCT r.id) AS total_ratings,
           COUNT(DISTINCT wr.id) AS total_walks
    FROM users u
    JOIN walker_profiles wp ON wp.user_id = u.id
    LEFT JOIN ratings r ON r.walker_id = u.id
    LEFT JOIN walk_requests wr ON wr.walker_id = u.id AND wr.status = 'completed'
    WHERE u.id = ? AND u.role = 'walker' AND u.is_active = 1
      AND u.is_approved = 1 AND u.is_banned = 0
    GROUP BY u.id, u.first_name, u.last_name, wp.photo, wp.description,
             wp.favorite_breed, wp.experience_years, wp.city, wp.price_per_hour, wp.is_available
");
$stmt->execute([$walker_id]);
$walker = $stmt->fetch();

if (!$walker) {
    header('Location: index.php');
    exit;
}

// reviews
$rev = $pdo->prepare("
    SELECT r.score, r.comment, r.created_at,
           u.first_name, u.last_name
    FROM ratings r
    JOIN users u ON u.id = r.owner_id
    WHERE r.walker_id = ?
    ORDER BY r.created_at DESC
    LIMIT 10
");
$rev->execute([$walker_id]);
$reviews = $rev->fetchAll();

$canContact = isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'owner';
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>DogWalk – <?= htmlspecialchars($walker['first_name'] . ' ' . $walker['last_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="style.css"/>
    <style>
        .profile-hero { background: linear-gradient(135deg, #4a5e3a 0%, #6b7c59 100%); color: #fff; padding: 3rem 0 2rem; }
        .avatar-circle { width: 110px; height: 110px; border-radius: 50%; object-fit: cover; border: 4px solid rgba(255,255,255,.4); }
        .avatar-placeholder { width: 110px; height: 110px; border-radius: 50%; background: rgba(255,255,255,.2); display: flex; align-items: center; justify-content: center; font-size: 2.5rem; border: 4px solid rgba(255,255,255,.4); }
        .stat-box { background: rgba(255,255,255,.12); border-radius: 12px; padding: .75rem 1.25rem; text-align: center; }
        .stat-box .num { font-size: 1.6rem; font-weight: 700; }
        .stat-box .lbl { font-size: .75rem; opacity: .8; }
        .badge-avail { font-size: .8rem; }
        .review-card { border-left: 3px solid #6b7c59; }
        .stars-row i { color: #e9a825; font-size: .9rem; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<!-- Hero sekcija -->
<div class="profile-hero">
    <div class="container">
        <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start gap-4">
            <?php if ($walker['photo']): ?>
                <img src="<?= htmlspecialchars($walker['photo']) ?>"
                     alt="<?= htmlspecialchars($walker['first_name']) ?>"
                     class="avatar-circle"/>
            <?php else: ?>
                <div class="avatar-placeholder text-white">
                    <i class="bi bi-person-fill"></i>
                </div>
            <?php endif; ?>

            <div class="flex-grow-1">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <h1 class="mb-0 fw-bold" style="font-family:'DM Sans',sans-serif;">
                        <?= htmlspecialchars($walker['first_name'] . ' ' . $walker['last_name']) ?>
                    </h1>
                    <?php if ($walker['is_available']): ?>
                        <span class="badge bg-success badge-avail">Dostupan/na</span>
                    <?php else: ?>
                        <span class="badge bg-secondary badge-avail">Nije dostupan/na</span>
                    <?php endif; ?>
                </div>

                <?php if ($walker['city']): ?>
                    <p class="mb-2 opacity-75"><i class="bi bi-geo-alt-fill me-1"></i><?= htmlspecialchars($walker['city']) ?></p>
                <?php endif; ?>

                <?php if ($walker['description']): ?>
                    <p class="mb-3" style="max-width:560px;"><?= htmlspecialchars($walker['description']) ?></p>
                <?php endif; ?>

                <div class="d-flex flex-wrap gap-3">
                    <div class="stat-box">
                        <div class="num"><?= number_format($walker['avg_score'], 1) ?> <i class="bi bi-star-fill" style="font-size:1rem;color:#e9a825;"></i></div>
                        <div class="lbl"><?= (int)$walker['total_ratings'] ?> ocena</div>
                    </div>
                    <div class="stat-box">
                        <div class="num"><?= (int)$walker['total_walks'] ?></div>
                        <div class="lbl">završenih šetnji</div>
                    </div>
                    <?php if ($walker['experience_years']): ?>
                    <div class="stat-box">
                        <div class="num"><?= (int)$walker['experience_years'] ?></div>
                        <div class="lbl">god. iskustva</div>
                    </div>
                    <?php endif; ?>
                    <?php if ($walker['price_per_hour']): ?>
                    <div class="stat-box">
                        <div class="num"><?= number_format($walker['price_per_hour'], 0, ',', '.') ?></div>
                        <div class="lbl">RSD / šetnja</div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<main class="container py-5">
    <div class="row g-4">

        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-3 mb-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Detalji</h5>
                    <?php if ($walker['favorite_breed']): ?>
                        <div class="mb-2"><i class="bi bi-heart-fill text-danger me-2"></i>Omiljena rasa: <strong><?= htmlspecialchars($walker['favorite_breed']) ?></strong></div>
                    <?php endif; ?>
                    <?php if ($walker['city']): ?>
                        <div class="mb-2"><i class="bi bi-geo-alt-fill text-success me-2"></i>Grad: <strong><?= htmlspecialchars($walker['city']) ?></strong></div>
                    <?php endif; ?>
                    <?php if ($walker['experience_years']): ?>
                        <div class="mb-2"><i class="bi bi-award-fill text-warning me-2"></i>Iskustvo: <strong><?= (int)$walker['experience_years'] ?> god.</strong></div>
                    <?php endif; ?>
                    <?php if ($walker['price_per_hour']): ?>
                        <div class="mb-2"><i class="bi bi-cash-coin text-success me-2"></i>Cena: <strong><?= number_format($walker['price_per_hour'], 0, ',', '.') ?> RSD</strong></div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($canContact && $walker['is_available']): ?>
                <a href="contact_walker.php?id=<?= $walker['id'] ?>"
                   class="btn btn-success w-100 py-2 fw-semibold">
                    <i class="bi bi-send me-1"></i> Zatraži šetnju
                </a>
            <?php elseif (!isset($_SESSION['user_id'])): ?>
                <a href="login.php" class="btn btn-outline-success w-100 py-2 fw-semibold">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Prijavi se da kontaktiraš
                </a>
            <?php elseif (!$walker['is_available']): ?>
                <div class="alert alert-secondary text-center mb-0">Šetač trenutno nije dostupan.</div>
            <?php endif; ?>
        </div>

        <div class="col-md-8">
            <h4 class="fw-bold mb-3">Recenzije <span class="text-muted fw-normal" style="font-size:.9rem;">(<?= count($reviews) ?>)</span></h4>

            <?php if (empty($reviews)): ?>
                <p class="text-muted">Ovaj šetač još nema ocena.</p>
            <?php else: ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($reviews as $r): ?>
                        <div class="card review-card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <strong><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></strong>
                                    <small class="text-muted"><?= date('d.m.Y', strtotime($r['created_at'])) ?></small>
                                </div>
                                <div class="stars-row mb-2">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="bi bi-star<?= $i <= $r['score'] ? '-fill' : '' ?>"></i>
                                    <?php endfor; ?>
                                    <span class="ms-1 text-muted" style="font-size:.8rem;"><?= $r['score'] ?>/5</span>
                                </div>
                                <?php if ($r['comment']): ?>
                                    <p class="mb-0 text-secondary"><?= htmlspecialchars($r['comment']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</main>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
