<?php
session_start();
require_once 'db.php';

$success = false;
$message = '';
$role = '';

$token = trim($_GET['token'] ?? '');

if (empty($token)) {
    $message = "Nevažeći aktivacioni link.";
} else {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT id, is_active, role FROM users WHERE activation_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        $message = "Aktivacioni link nije ispravan ili je već iskorišćen.";
    } elseif ($user['is_active'] == 1) {
        $message = "Nalog je već aktiviran. Možete se prijaviti.";
        $success = true;
    } else {
        $stmt = $pdo->prepare("UPDATE users SET is_active = 1, activation_token = NULL WHERE id = ?");
        $stmt->execute([$user['id']]);

        $success = true;
        $role = $user['role'];

        if ($role === 'walker') {
            $message = "Nalog je aktiviran! Vaš nalog šetača čeka odobrenje administratora.";
        } else {
            $message = "Nalog je uspešno aktiviran! Možete se prijaviti.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Aktivacija naloga – DogWalk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<main class="d-flex align-items-center justify-content-center py-5">
    <div class="text-center" style="max-width:440px">
        <?php if ($success): ?>
            <div style="font-size:64px">✅</div>
            <h3 class="mt-3">Aktivacija uspešna!</h3>
            <p class="text-muted"><?= htmlspecialchars($message) ?></p>
            <a href="login.php" class="btn btn-success mt-2">Prijavi se</a>
        <?php else: ?>
            <div style="font-size:64px">❌</div>
            <h3 class="mt-3">Greška pri aktivaciji</h3>
            <p class="text-muted"><?= htmlspecialchars($message) ?></p>
            <a href="register.php" class="btn btn-secondary mt-2">Registruj se ponovo</a>
        <?php endif; ?>
    </div>
</main>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
