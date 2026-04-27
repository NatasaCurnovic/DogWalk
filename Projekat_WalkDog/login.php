<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DogWalk – Prijava</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="style.css">

</head>
<body>

<?php include 'navbar.php'; ?>

<main class="auth-page" aria-label="Stranica za prijavu">
    <div class="auth-form-box">

        <h1 class="auth-form-title">Prijavi se</h1>
        <p class="auth-form-subtitle">
            Nemaš nalog? <a href="register.html">Registruj se</a>
        </p>

        <!-- Inline alert -->
        <div id="login-alert" class="auth-alert d-none" role="alert" aria-live="assertive"></div>

        <form id="login-form" novalidate aria-label="Forma za prijavu">

            <!-- E-mail -->
            <div class="form-group">
                <label for="email">
                    E-mail adresa <span class="required" aria-hidden="true">*</span>
                </label>
                <input type="email" id="email" name="email" class="auth-input" placeholder="vasa@email.com" autocomplete="email" required aria-required="true" aria-describedby="email-error">
                <span class="field-error" id="email-error" role="alert"></span>
            </div>

            <!-- Lozinka -->
            <div class="form-group">
                <div class="label-row">
                    <label for="password">
                        Lozinka <span class="required" aria-hidden="true">*</span>
                    </label>
                    <a href="forgot-password.html" class="forgot-link">Zaboravili ste lozinku?</a>
                </div>
                <div class="input-with-icon">
                    <input type="password" id="password" name="password" class="auth-input" placeholder="Unesite lozinku" autocomplete="current-password" required aria-required="true" aria-describedby="password-error">
                    <button type="button" class="toggle-password" aria-label="Prikaži/sakrij lozinku" data-target="password">
                        <i class="bi bi-eye" aria-hidden="true"></i>
                    </button>
                </div>
                <span class="field-error" id="password-error" role="alert"></span>
            </div>

            <!-- Zapamti me -->
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" id="remember-me" name="rememberMe">
                    <span>Zapamti me</span>
                </label>
            </div>

            <!-- Submit -->
            <button type="submit" class="auth-submit-btn" id="login-submit-btn">
                <span class="btn-text">Prijavi se</span>
                <span class="btn-spinner d-none" aria-hidden="true">
          <i class="bi bi-arrow-repeat spin"></i>
        </span>
            </button>

        </form>

        <!-- Divider -->
        <p class="auth-divider-text"><span>ili</span></p>

        <!-- Registracija link -->
        <a href="register.html" class="auth-register-btn">
            <i class="bi bi-person-plus" aria-hidden="true"></i>
            Kreiraj novi nalog
        </a>

    </div>
</main>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="login.js"></script>

</body>
</html>

