<?php
session_start();

require_once 'db.php';

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'admin') {
        header('Location: admin.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Server validation
    if (empty($email) || empty($password)) {
        $error = "Unesite e-mail adresu i lozinku.";
    } else {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $error = "Pogrešna e-mail adresa ili lozinka.";
        } elseif ($user['is_active'] == 0) {
            $error = "Nalog nije aktiviran. Proverite e-mail i kliknite na aktivacioni link.";
        } elseif ($user['is_banned'] == 1) {
            $error = "Vaš nalog je blokiran. Kontaktirajte administratora.";
        } elseif ($user['role'] === 'walker' && $user['is_approved'] == 0) {
            $error = "Vaš nalog šetača čeka odobrenje administratora.";
        } else {
            session_regenerate_id(true);

            $_SESSION['user_id']= $user['id'];
            $_SESSION['user_name']= $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['user_role']= $user['role'];
            $_SESSION['user_email']= $user['email'];

            if ($user['role'] === 'admin') {
                header('Location: admin.php');
            } else {
                header('Location: index.php');
            }
            exit;
        }

    }
}
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>DogWalk – Prijavi se</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<main class="d-flex align-items-center justify-content-center py-5">
    <div class="login-card px-3 px-sm-0">

        <h1 class="login-title fade-up">Prijavi se</h1>
        <p class="subtitle mb-4 fade-up delay-1">
            Nemaš nalog? <a href="register.php">Registruj se</a>
        </p>

        <?php if ($error): ?>
            <div class="alert alert-danger fade-up" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form id="loginForm" action="login.php" method="POST" novalidate>

            <div class="mb-3 fade-up delay-2">
                <label for="email" class="form-label">
                    E-mail adresa <span class="required-star">*</span>
                </label>
                <input type="email" id="email" name="email" class="form-control"
                       placeholder="vasa@email.com" autocomplete="email"
                       value="<?= htmlspecialchars($email) ?>"/>
                <div class="invalid-feedback">Unesite ispravnu e-mail adresu.</div>
            </div>

            <div class="mb-3 fade-up delay-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="password" class="form-label mb-0">
                        Lozinka <span class="required-star">*</span>
                    </label>
                    <a href="forgot_password.php" class="forgot-link">Zaboravili ste lozinku?</a>
                </div>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" class="form-control"
                           placeholder="Unesite lozinku" autocomplete="current-password"/>
                    <button class="toggle-pw" type="button" id="togglePw" aria-label="Prikaži lozinku">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
                <div class="invalid-feedback">Lozinka ne može biti prazna.</div>
            </div>

            <div class="form-check mb-4 fade-up delay-3">
                <input class="form-check-input" type="checkbox" name="remember_me" id="rememberMe"/>
                <label class="form-check-label" for="rememberMe">Zapamti me</label>
            </div>

            <div class="fade-up delay-4">
                <button class="btn-primary-green mb-3" id="loginBtn" type="submit">
                    Prijavi se
                </button>
            </div>

            <div class="divider mb-3 fade-up delay-4">ili</div>

            <div class="fade-up delay-5">
                <a href="register.php" class="btn-secondary-outline">
                    <i class="bi bi-person-plus"></i>
                    Kreiraj novi nalog
                </a>
            </div>

        </form>
    </div>
</main>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="login.js?v=3"></script>
</body>
</html>
