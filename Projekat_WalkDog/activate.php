<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DogWalk – Aktivacija naloga</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="activate.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<!-- ===== MAIN ===== -->
<main>
    <div class="act-card">

        <!-- STATE 1: Učitavanje (default) -->
        <div id="state-loading">
            <div class="act-icon-circle act-icon-circle--loading">
                <i class="bi bi-arrow-repeat spin" aria-hidden="true"></i>
            </div>
            <h1 class="act-title">Aktivacija u toku...</h1>
            <p class="act-desc">Verifikujemo vaš nalog. Molimo sačekajte trenutak.</p>
            <div class="act-dots">
                <div class="act-dot"></div>
                <div class="act-dot"></div>
                <div class="act-dot"></div>
            </div>
        </div>

        <!-- STATE 2: Uspeh -->
        <div id="state-success" class="d-none">
            <div class="act-icon-circle act-icon-circle--success">
                <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
            </div>
            <div class="act-badge">
                <i class="bi bi-shield-check"></i>
                Nalog verifikovan
            </div>
            <h1 class="act-title">Nalog je aktiviran!</h1>
            <p class="act-desc">
                Vaša e-mail adresa je potvrđena. Dobrodošli u DogWalk zajednicu!
                Možete se odmah prijaviti i početi koristiti platformu.
            </p>
            <div class="d-flex flex-column gap-2">
                <a href="login.html" class="act-btn act-btn--primary">
                    <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
                    Prijavi se
                </a>
                <a href="index.html" class="act-btn act-btn--outline">
                    <i class="bi bi-house" aria-hidden="true"></i>
                    Idi na početnu
                </a>
            </div>
        </div>

        <!-- STATE 3: Uspeh – Walker (čeka odobrenje) -->
        <div id="state-success-walker" class="d-none">
            <div class="act-icon-circle act-icon-circle--success">
                <i class="bi bi-person-check-fill" aria-hidden="true"></i>
            </div>
            <div class="act-badge">
                <i class="bi bi-hourglass-split"></i>
                Čeka odobrenje admina
            </div>
            <h1 class="act-title">E-mail potvrđen!</h1>
            <p class="act-desc">
                Vaša e-mail adresa je uspešno potvrđena. Vaš šetač profil je prosleđen
                administratoru na pregled. Obavestićemo vas e-mailom čim bude odobren.
            </p>
            <div class="d-flex flex-column gap-2">
                <a href="index.html" class="act-btn act-btn--primary">
                    <i class="bi bi-house" aria-hidden="true"></i>
                    Idi na početnu
                </a>
                <a href="index.html#contact-section" class="act-btn act-btn--outline">
                    <i class="bi bi-chat-dots" aria-hidden="true"></i>
                    Kontaktiraj podršku
                </a>
            </div>
        </div>

        <!-- STATE 4: Token istekao -->
        <div id="state-expired" class="d-none">
            <div class="act-icon-circle act-icon-circle--expired">
                <i class="bi bi-clock-history" aria-hidden="true"></i>
            </div>
            <h1 class="act-title">Link je istekao</h1>
            <p class="act-desc">
                Ovaj aktivacioni link je važio <strong>24 sata</strong> i više nije aktivan.
                Zatražite novi link ispod.
            </p>
            <div class="d-flex flex-column gap-2">
                <button type="button" id="resend-btn" class="act-btn act-btn--primary">
                    <span class="btn-text">
                        <i class="bi bi-envelope-arrow-up me-1" aria-hidden="true"></i>
                        Pošalji novi link
                    </span>
                    <span class="btn-spinner d-none" aria-hidden="true">
                        <i class="bi bi-arrow-repeat spin"></i>
                    </span>
                </button>
                <a href="register.html" class="act-btn act-btn--outline">
                    <i class="bi bi-person-plus" aria-hidden="true"></i>
                    Registruj se ponovo
                </a>
            </div>
            <div id="resend-alert" class="mt-3 d-none" style="font-size:0.85rem;"></div>
        </div>

        <!-- STATE 5: Greška (neispravan token) -->
        <div id="state-error" class="d-none">
            <div class="act-icon-circle act-icon-circle--error">
                <i class="bi bi-x-circle-fill" aria-hidden="true"></i>
            </div>
            <h1 class="act-title">Neispravan link</h1>
            <p class="act-desc">
                Aktivacioni link nije ispravan ili je već iskorišćen.
                Proverite da li ste kopirali ceo link iz e-maila.
            </p>
            <div class="d-flex flex-column gap-2">
                <a href="register.html" class="act-btn act-btn--primary">
                    <i class="bi bi-person-plus" aria-hidden="true"></i>
                    Registruj se ponovo
                </a>
                <a href="index.html#contact-section" class="act-btn act-btn--outline">
                    <i class="bi bi-chat-dots" aria-hidden="true"></i>
                    Kontaktiraj podršku
                </a>
            </div>
        </div>

        <!-- STATE 6: Već aktiviran -->
        <div id="state-already" class="d-none">
            <div class="act-icon-circle act-icon-circle--success">
                <i class="bi bi-patch-check-fill" aria-hidden="true"></i>
            </div>
            <div class="act-badge">
                <i class="bi bi-check2-all"></i>
                Već aktiviran
            </div>
            <h1 class="act-title">Nalog je već aktivan</h1>
            <p class="act-desc">
                Vaš nalog je već aktiviran. Možete se odmah prijaviti.
            </p>
            <a href="login.html" class="act-btn act-btn--primary">
                <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
                Prijavi se
            </a>
        </div>

    </div>
</main>

<?php include 'footer.php'; ?>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="activate.js"></script>

</body>
</html>
