<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pdo = getDB();
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$walkerProfile = null;
if ($user['role'] === 'walker') {
    $stmt2 = $pdo->prepare("SELECT * FROM walker_profiles WHERE user_id = ?");
    $stmt2->execute([$_SESSION['user_id']]);
    $walkerProfile = $stmt2->fetch();
}

$success = $_GET['saved'] ?? '';
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>DogWalk – Moj profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<main class="container py-5" style="max-width: 640px;">
    <h1 class="mb-4" style="font-family:'DM Sans',sans-serif;font-weight:700;">Moj profil</h1>

    <?php if ($success === '1'): ?>
        <div class="alert alert-success">Podaci su uspešno sačuvani.</div>
    <?php endif; ?>
    <?php if ($error === '1'): ?>
        <div class="alert alert-danger">Došlo je do greške. Pokušajte ponovo.</div>
    <?php endif; ?>

    <form action="update_profile.php" method="POST" id="profileForm" novalidate>

        <div class="row g-3 mb-3">
            <div class="col-6">
                <label class="form-label fw-semibold">Ime</label>
                <input type="text" name="first_name" class="form-control"
                       value="<?= htmlspecialchars($user['first_name']) ?>" required/>
                <div class="invalid-feedback">Unesite ime.</div>
            </div>
            <div class="col-6">
                <label class="form-label fw-semibold">Prezime</label>
                <input type="text" name="last_name" class="form-control"
                       value="<?= htmlspecialchars($user['last_name']) ?>" required/>
                <div class="invalid-feedback">Unesite prezime.</div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Telefon</label>
            <input type="tel" name="phone" class="form-control"
                   value="<?= htmlspecialchars($user['phone'] ?? '') ?>"/>
        </div>

        <?php if ($user['role'] === 'owner'): ?>
            <div class="mb-3">
                <label class="form-label fw-semibold">Adresa</label>
                <input type="text" name="address" class="form-control"
                       value="<?= htmlspecialchars($user['address'] ?? '') ?>"/>
            </div>
        <?php endif; ?>

        <hr/>
        <h5 class="mb-3">Promena lozinke <span class="text-muted" style="font-size:.85rem;">(opciono)</span></h5>

        <div class="mb-3">
            <label class="form-label fw-semibold">Nova lozinka</label>
            <input type="password" name="password" id="pwd" class="form-control"
                   placeholder="Ostavite prazno ako ne menjate"/>
            <div class="form-text">Min 8 karaktera, bar jedno veliko slovo i broj.</div>
        </div>
        <div class="mb-4">
            <label class="form-label fw-semibold">Potvrdi novu lozinku</label>
            <input type="password" name="password2" id="pwd2" class="form-control"
                   placeholder="Ponovite novu lozinku"/>
            <div id="pwdMsg" class="form-text"></div>
        </div>

        <?php if ($user['role'] === 'walker' && $walkerProfile): ?>
            <hr/>
            <h5 class="mb-3">Podaci šetača</h5>

            <div class="mb-3">
                <label class="form-label fw-semibold">Opis (o sebi)</label>
                <textarea name="walker_bio" class="form-control" rows="4"
                          placeholder="Recite nešto o sebi i iskustvu sa psima..."><?= htmlspecialchars($walkerProfile['description'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Grad</label>
                <input type="text" name="walker_city" class="form-control"
                       value="<?= htmlspecialchars($walkerProfile['city'] ?? '') ?>"/>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Omiljena rasa</label>
                <input type="text" name="walker_breed" class="form-control"
                       value="<?= htmlspecialchars($walkerProfile['favorite_breed'] ?? '') ?>"/>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Godine iskustva</label>
                <input type="number" name="walker_experience" class="form-control" min="0"
                       value="<?= (int)($walkerProfile['experience_years'] ?? 0) ?>"/>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Cena po šetnji (RSD)</label>
                <input type="number" name="walker_price" class="form-control" min="0"
                       value="<?= htmlspecialchars($walkerProfile['price_per_hour'] ?? '') ?>"/>
            </div>
            <div class="mb-4 form-check form-switch">
                <input class="form-check-input" type="checkbox" name="walker_available"
                       id="availSwitch" <?= $walkerProfile['is_available'] ? 'checked' : '' ?>/>
                <label class="form-check-label" for="availSwitch">Dostupan za šetnje</label>
            </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-success w-100 py-2 fw-semibold">Sačuvaj izmene</button>
    </form>
</main>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const p1 = document.getElementById('pwd');
    const p2 = document.getElementById('pwd2');
    const msg = document.getElementById('pwdMsg');

    p2.addEventListener('input', () => {
        if (!p1.value) { msg.textContent = ''; return; }
        if (p1.value === p2.value) {
            msg.textContent = 'Lozinke se poklapaju ✓';
            msg.style.color = '#4a5e3a';
        } else {
            msg.textContent = 'Lozinke se ne poklapaju.';
            msg.style.color = '#c0392b';
        }
    });
</script>
</body>
</html>