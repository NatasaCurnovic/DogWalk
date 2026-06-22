<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'walker') {
    header('Location: login.php');
    exit;
}

$request_id = (int)($_GET['id'] ?? 0);
if ($request_id <= 0) {
    header('Location: walker_request.php');
    exit;
}

$pdo = getDB();

// Check that the request belongs to this setter and that it is in the status 'accepted'
$stmt = $pdo->prepare("
    SELECT wr.id, wr.dog_name, wr.dog_breed, u.first_name, u.last_name
    FROM walk_requests wr
    JOIN users u ON u.id = wr.owner_id
    WHERE wr.id = ? AND wr.walker_id = ? AND wr.status = 'accepted'
");
$stmt->execute([$request_id, $_SESSION['user_id']]);
$req = $stmt->fetch();

if (!$req) {
    header('Location: walker_request.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>DogWalk – Unos šetnje</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
<?php include 'navbar.php'; ?>

<main class="container py-5" style="max-width:560px;">
    <h1 class="mb-1 fw-bold">Unos dnevnika šetnje</h1>
    <p class="text-muted mb-4">
        Pas: <strong><?= htmlspecialchars($req['dog_name']) ?></strong>
        (<?= htmlspecialchars($req['dog_breed']) ?>)
        &nbsp;·&nbsp; Vlasnik: <?= htmlspecialchars($req['first_name'] . ' ' . $req['last_name']) ?>
    </p>

    <form action="save_walk.php" method="POST" id="walkForm" novalidate>
        <input type="hidden" name="request_id" value="<?= $request_id ?>"/>

        <div class="mb-3">
            <label class="form-label fw-semibold">Opis šetnje <span class="text-danger">*</span></label>
            <textarea name="walk_description" class="form-control" rows="5"
                      placeholder="Opišite kako je prošla šetnja, ponašanje psa, posebne napomene..."
                      required></textarea>
            <div class="invalid-feedback">Unesite opis šetnje.</div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Putanja šetnje (opciono)</label>
            <input type="text" name="walk_route" class="form-control"
                   placeholder="npr. Park Pionir → Bulevar Oslobođenja → nazad"/>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Trajanje (minuti) <span class="text-danger">*</span></label>
            <input type="number" name="duration_minutes" class="form-control"
                   min="1" max="480" placeholder="npr. 45" required/>
            <div class="invalid-feedback">Unesite trajanje šetnje u minutima.</div>
        </div>

        <button type="submit" class="btn btn-success w-100 py-2 fw-semibold">
            <i class="bi bi-journal-check me-1"></i> Završi šetnju i sačuvaj dnevnik
        </button>
    </form>
</main>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('walkForm').addEventListener('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        this.classList.add('was-validated');
    });
</script>
</body>
</html>
