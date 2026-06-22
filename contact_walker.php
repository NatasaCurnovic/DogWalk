<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['user_role'] !== 'owner') {
    header('Location: index.php');
    exit;
}

$walker_id = (int)($_GET['id'] ?? 0);
if ($walker_id <= 0) {
    header('Location: index.php');
    exit;
}

//  Check if the walker exists and is active/approved
$pdo = getDB();
$stmt = $pdo->prepare("
    SELECT u.id, u.first_name, u.last_name, wp.city, wp.price_per_hour, wp.description
    FROM users u
    JOIN walker_profiles wp ON wp.user_id = u.id
    WHERE u.id = ? AND u.role = 'walker' AND u.is_active = 1
      AND u.is_approved = 1 AND u.is_banned = 0 AND wp.is_available = 1
");
$stmt->execute([$walker_id]);
$walker = $stmt->fetch();

if (!$walker) {
    header('Location: index.php');
    exit;
}

$error = '';
$old = ['dog_name'=>'','dog_breed'=>'','dog_age'=>'','description'=>'','dog_gender'=>'male','scheduled_at'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dog_name = trim($_POST['dog_name'] ?? '');
    $dog_breed = trim($_POST['dog_breed'] ?? '');
    $dog_gender = $_POST['dog_gender'] ?? '';
    $dog_age = (int)($_POST['dog_age'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $scheduled = trim($_POST['scheduled_at'] ?? '');
    $scheduled_at = $scheduled !== '' ? str_replace('T', ' ', $scheduled) : '';

    $old = compact('dog_name','dog_breed','dog_gender','dog_age','description','scheduled_at');

    if (empty($dog_name) || empty($dog_breed) || !in_array($dog_gender, ['male','female']) || $dog_age <= 0) {
        $error = "Popunite sva obavezna polja ispravno.";
    } elseif ($dog_age > 30) {
        $error = "Unesite realnu starost psa.";
    } else {
        $ins = $pdo->prepare("
            INSERT INTO walk_requests
                (owner_id, walker_id, dog_name, dog_breed, dog_gender, dog_age, description, scheduled_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $ins->execute([
            $_SESSION['user_id'],
            $walker_id,
            $dog_name,
            $dog_breed,
            $dog_gender,
            $dog_age,
            $description ?: null,
            $scheduled_at ?: null
        ]);

        header('Location: index.php?sent=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>DogWalk – Kontaktiraj šetača</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<main class="container py-5" style="max-width:600px;">
    <h1 class="mb-1" style="font-family:'DM Sans',sans-serif;font-weight:700;">Zatraži šetnju</h1>
    <p class="text-muted mb-4">
        Šetač: <strong><?= htmlspecialchars($walker['first_name'] . ' ' . $walker['last_name']) ?></strong>
        <?php if ($walker['city']): ?>
            &nbsp;·&nbsp; <i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($walker['city']) ?>
        <?php endif; ?>
        <?php if ($walker['price_per_hour']): ?>
            &nbsp;·&nbsp; <?= number_format($walker['price_per_hour'], 0, ',', '.') ?> RSD
        <?php endif; ?>
    </p>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="contact_walker.php?id=<?= $walker_id ?>" method="POST" id="contactForm" novalidate>
        <input type="hidden" name="walker_id" value="<?= $walker_id ?>"/>

        <div class="row g-3 mb-3">
            <div class="col-7">
                <label class="form-label fw-semibold">Ime psa <span class="text-danger">*</span></label>
                <input type="text" name="dog_name" class="form-control"
                       value="<?= htmlspecialchars($old['dog_name']) ?>" required placeholder="npr. Miki"/>
                <div class="invalid-feedback">Unesite ime psa.</div>
            </div>
            <div class="col-5">
                <label class="form-label fw-semibold">Starost (god.) <span class="text-danger">*</span></label>
                <input type="number" name="dog_age" class="form-control" min="0" max="30"
                       value="<?= htmlspecialchars($old['dog_age']) ?>" required placeholder="npr. 3"/>
                <div class="invalid-feedback">Unesite starost.</div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-8">
                <label class="form-label fw-semibold">Rasa <span class="text-danger">*</span></label>
                <input type="text" name="dog_breed" class="form-control"
                       value="<?= htmlspecialchars($old['dog_breed']) ?>" required placeholder="npr. Labrador"/>
                <div class="invalid-feedback">Unesite rasu.</div>
            </div>
            <div class="col-4">
                <label class="form-label fw-semibold">Pol <span class="text-danger">*</span></label>
                <select name="dog_gender" class="form-select" required>
                    <option value="male"   <?= ($old['dog_gender'] === 'male')   ? 'selected' : '' ?>>Muški</option>
                    <option value="female" <?= ($old['dog_gender'] === 'female') ? 'selected' : '' ?>>Ženski</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Željeni termin (opciono)</label>
            <input type="datetime-local" name="scheduled_at" class="form-control"
                   value="<?= htmlspecialchars($old['scheduled_at'] ?? '') ?>"/>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Opis i specifičnosti</label>
            <textarea name="description" class="form-control" rows="4"
                      placeholder="Posebne napomene o psu, specifičnosti u ponašanju..."><?= htmlspecialchars($old['description']) ?></textarea>
        </div>

        <button type="submit" class="btn btn-success w-100 py-2 fw-semibold">
            <i class="bi bi-send me-1"></i> Pošalji zahtev
        </button>
    </form>
</main>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        this.classList.add('was-validated');
    });
</script>
</body>
</html>
