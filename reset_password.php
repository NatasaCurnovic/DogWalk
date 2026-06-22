<?php
session_start();
require_once 'db.php';

$errors  = [];
$success = false;
$token = trim($_GET['token'] ?? '');
$user = null;

if (empty($token)) {
    $errors[] = "Nevažeći link za reset lozinke.";
} else {
    $pdo = getDB();
    $stmt = $pdo->prepare("
        SELECT id, first_name FROM users
        WHERE reset_token = ?
          AND reset_token_expires > NOW()
          AND is_active = 1
    ");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        $errors[] = "Link je nevažeći ili je istekao. Zatražite novi.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user) {
    $password  = $_POST['password']  ?? '';
    $password2 = $_POST['password2'] ?? '';

    if (strlen($password) < 8) {
        $errors[] = "Lozinka mora imati najmanje 8 karaktera.";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Lozinka mora sadržati bar jedno veliko slovo.";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Lozinka mora sadržati bar jedan broj.";
    } elseif ($password !== $password2) {
        $errors[] = "Lozinke se ne poklapaju.";
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("
            UPDATE users
            SET password_hash = ?, reset_token = NULL, reset_token_expires = NULL
            WHERE id = ?
        ");
        $stmt->execute([$hash, $user['id']]);
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>DogWalk – Nova lozinka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet"/>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="forgot-password.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<main>
    <div class="fp-card">
        <?php if ($success): ?>
            <div class="text-center py-2">
                <div class="success-icon mb-3">
                    <i class="bi bi-check-circle-fill" style="font-size:3rem;color:#4a5e3a"></i>
                </div>
                <h2 class="success-title mb-2">Lozinka je promenjena!</h2>
                <p class="success-desc mb-4">Možete se prijaviti sa novom lozinkom.</p>
                <a href="login.php" class="auth-submit-btn d-inline-block text-decoration-none text-center">
                    Prijavi se
                </a>
            </div>

        <?php elseif (!empty($errors) && !$user): ?>
            <div class="text-center py-2">
                <i class="bi bi-x-circle-fill text-danger" style="font-size:3rem"></i>
                <h2 class="mt-3">Nevažeći link</h2>
                <p class="text-muted"><?= htmlspecialchars($errors[0]) ?></p>
                <a href="forgot-password.php" class="auth-submit-btn d-inline-block text-decoration-none text-center mt-2">
                    Zatraži novi link
                </a>
            </div>

        <?php else: ?>
            <div class="fp-icon-circle">
                <i class="bi bi-key" aria-hidden="true"></i>
            </div>
            <h1 class="fp-title text-center">Nova lozinka</h1>
            <p class="fp-subtitle text-center">Unesite novu lozinku za vaš nalog.</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger mb-3">
                    <?php foreach ($errors as $e): ?>
                        <div><?= htmlspecialchars($e) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="reset_password.php?token=<?= htmlspecialchars($token) ?>" id="resetForm">
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.85rem">
                        Nova lozinka <span class="text-danger">*</span>
                    </label>
                    <input type="password" name="password" id="pwd" class="auth-input"
                           placeholder="Minimalno 8 karaktera" required/>
                    <div class="hint-text mt-1" style="font-size:0.77rem;color:#7a7a6e">
                        Minimalno 8 karaktera, bar jedno veliko slovo i broj.
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold" style="font-size:0.85rem">
                        Potvrdi lozinku <span class="text-danger">*</span>
                    </label>
                    <input type="password" name="password2" id="pwd2" class="auth-input"
                           placeholder="Ponovite lozinku" required/>
                    <div id="pwd2Msg" style="font-size:0.77rem;margin-top:4px"></div>
                </div>
                <button type="submit" class="auth-submit-btn">Sačuvaj novu lozinku</button>
            </form>
        <?php endif; ?>
    </div>
</main>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const p1  = document.getElementById('pwd');
    const p2  = document.getElementById('pwd2');
    const msg = document.getElementById('pwd2Msg');
    if (p2) {
        p2.addEventListener('input', function () {
            if (!p1.value || !p2.value) { msg.textContent = ''; return; }
            if (p1.value === p2.value) {
                msg.textContent = 'Lozinke se poklapaju ✓';
                msg.style.color = '#4a5e3a';
            } else {
                msg.textContent = 'Lozinke se ne poklapaju.';
                msg.style.color = '#c0392b';
            }
        });
    }
</script>
</body>
</html>
