<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
    header('Location: login.php');
    exit;
}

$request_id = (int)($_GET['id'] ?? 0);
if ($request_id <= 0) {
    header('Location: index.php');
    exit;
}

$pdo = getDB();

//Check that the request belongs to this owner and that it is completed
$stmt = $pdo->prepare("
    SELECT wr.id, wr.rating_code_used, wr.walker_id, u.first_name, u.last_name
    FROM walk_requests wr
    JOIN users u ON u.id = wr.walker_id
    WHERE wr.id = ? AND wr.owner_id = ? AND wr.status = 'completed'
");
$stmt->execute([$request_id, $_SESSION['user_id']]);
$req = $stmt->fetch();

if (!$req) {
    header('Location: index.php');
    exit;
}
$msg = '';
if (isset($_GET['msg'])) {
    $msg = $_GET['msg'] === 'code_sent'
            ? 'Kod je poslat na vaš email!'
            : 'Greška pri slanju. Pokušajte ponovo.';
}
$error = '';
if (isset($_GET['error'])) {
    $error = $_GET['error'] === 'wrong_code'
        ? 'Uneti kod nije ispravan. Proverite email i pokušajte ponovo.'
        : 'Podaci nisu ispravni. Pokušajte ponovo.';
}
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>DogWalk – Oceni šetača</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
<?php include 'navbar.php'; ?>

<main class="container py-5" style="max-width:520px;">
    <h1 class="mb-1 fw-bold">Oceni šetača</h1>
    <p class="text-muted mb-4">
        Šetač: <strong><?= htmlspecialchars($req['first_name'] . ' ' . $req['last_name']) ?></strong>
    </p>

    <?php if ($req['rating_code_used']): ?>
        <div class="alert alert-info">Ovaj šetač je već ocenjen za ovu šetnju.</div>
        <a href="index.php" class="btn btn-secondary">Nazad na početnu</a>
    <?php else: ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($msg): ?>
            <div class="alert alert-<?= strpos($msg, 'Greška') !== false ? 'danger' : 'success' ?>">
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <div class="alert alert-info">
            <i class="bi bi-envelope-fill me-1"></i>
            Unesi kod koji si dobio/la emailom nakon završene šetnje.
            <hr class="my-2"/>
            Nisi dobio/la kod?
            <a href="resend_rating_code.php?id=<?= $request_id ?>" class="alert-link">
                Pošalji mi kod ponovo
            </a>
        </div>

        <form action="save_rating.php" method="POST" id="ratingForm" novalidate>
            <input type="hidden" name="request_id" value="<?= $request_id ?>"/>

            <div class="mb-3">
                <label class="form-label fw-semibold">Kod za verifikaciju <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control font-monospace"
                       placeholder="Unesi kod iz emaila" required autocomplete="off"/>
                <div class="invalid-feedback">Unesite kod iz emaila.</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Ocena <span class="text-danger">*</span></label>
                <div class="d-flex gap-2" id="starRow">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <button type="button" class="btn btn-outline-warning btn-lg star-btn" data-val="<?= $i ?>">
                            <i class="bi bi-star<?= $i <= 3 ? '-fill' : '' ?>"></i>
                        </button>
                    <?php endfor; ?>
                </div>
                <input type="hidden" name="score" id="scoreInput" value="3" required/>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Komentar (opciono)</label>
                <textarea name="comment" class="form-control" rows="4"
                          placeholder="Podelite vaše iskustvo sa šetačem..."></textarea>
            </div>

            <button type="submit" class="btn btn-success w-100 py-2 fw-semibold">
                <i class="bi bi-star-fill me-1"></i> Pošalji ocenu
            </button>
        </form>

    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const stars = document.querySelectorAll('.star-btn');
    const input = document.getElementById('scoreInput');
    let selected = 3;

    function renderStars(val) {
        stars.forEach(btn => {
            const v = parseInt(btn.dataset.val);
            btn.classList.toggle('btn-warning', v <= val);
            btn.classList.toggle('btn-outline-warning', v > val);
        });
    }

    renderStars(selected);

    stars.forEach(btn => {
        btn.addEventListener('click', () => {
            selected = parseInt(btn.dataset.val);
            input.value = selected;
            renderStars(selected);
        });
        btn.addEventListener('mouseenter', () => renderStars(parseInt(btn.dataset.val)));
        btn.addEventListener('mouseleave', () => renderStars(selected));
    });

    document.getElementById('ratingForm').addEventListener('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        this.classList.add('was-validated');
    });
</script>
</body>
</html>
