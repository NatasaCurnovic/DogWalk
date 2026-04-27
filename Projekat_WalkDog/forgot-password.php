<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DogWalk – Zaboravljena lozinka</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="forgot-password.css">

</head>
<body>

<?php include 'navbar.php'; ?>

<!-- ===== MAIN ===== -->
<main>
    <div class="fp-card">

        <!-- STEP 1: forma -->
        <div id="forgot-step-request">

            <div class="fp-icon-circle">
                <i class="bi bi-lock" aria-hidden="true"></i>
            </div>

            <h1 class="fp-title text-center">Zaboravili ste lozinku?</h1>
            <p class="fp-subtitle text-center">
                Unesite e-mail adresu vašeg naloga i poslaćemo vam link za resetovanje lozinke.
            </p>

            <div id="forgot-alert" class="auth-alert d-none mb-3" role="alert" aria-live="assertive"></div>

            <form id="forgot-form" novalidate aria-label="Forma za resetovanje lozinke">

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold" style="font-size:0.85rem;">
                        E-mail adresa <span class="text-danger" aria-hidden="true">*</span>
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="auth-input"
                        placeholder="vasa@email.com"
                        autocomplete="email"
                        required
                        aria-required="true"
                        aria-describedby="email-error"
                    >
                    <span class="field-error" id="email-error" role="alert"></span>
                </div>

                <button type="submit" class="auth-submit-btn" id="forgot-submit-btn">
                    <span class="btn-text">Pošalji link za resetovanje</span>
                    <span class="btn-spinner d-none" aria-hidden="true">
                        <i class="bi bi-arrow-repeat spin"></i>
                    </span>
                </button>

            </form>

            <div class="fp-divider mt-4"><span>ili</span></div>

            <div class="text-center">
                <a href="login.html" class="fp-back-link">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                    Nazad na prijavu
                </a>
            </div>

        </div>

        <!-- STEP 2: uspeh -->
        <div id="forgot-step-success" class="d-none text-center py-2">

            <div class="success-icon mb-3">
                <i class="bi bi-envelope-check" aria-hidden="true"></i>
            </div>

            <h2 class="success-title mb-2">Proverite e-mail</h2>
            <p class="success-desc mb-4">
                Poslali smo vam link za resetovanje lozinke. Link je važeći <strong>24 sata</strong>.
                Ako ne vidite e-mail, proverite spam folder.
            </p>

            <div class="d-flex flex-column gap-2">
                <button type="button" id="resend-btn" class="auth-submit-btn auth-submit-btn-outline">
                    <i class="bi bi-arrow-clockwise" aria-hidden="true"></i>
                    Pošalji ponovo
                </button>

                <a href="login.html" class="fp-back-link justify-content-center mt-2">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                    Nazad na prijavu
                </a>
            </div>

        </div>

    </div>
</main>

<?php include 'footer.php'; ?>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="forgot-passwors.js"></script>

</body>
</html>
