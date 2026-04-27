<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DogWalk – Registracija</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="register.css">

    <style>

    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<main>
    <div class="row g-0">

        <!-- ── Left decorative panel ── -->
        <div class="col-lg-4 d-none d-lg-flex auth-panel">
            <div class="position-relative z-1 text-white w-100">
                <div class="auth-panel-logo">DogWalk</div>
                <h2 class="auth-panel-title mb-3">Pronađi pouzdanog šetača za svog psa</h2>
                <p class="auth-panel-subtitle mb-4">
                    Registruj se i povežite sa proverenim ljubiteljima pasa u vašem gradu.
                </p>
                <ul class="list-unstyled auth-panel-features d-flex flex-column gap-3">
                    <li class="d-flex align-items-center gap-2">
                        <i class="bi bi-shield-check fs-6"></i>
                        Provereni i ocenjeni šetači
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <i class="bi bi-geo-alt fs-6"></i>
                        Šetači u vašoj okolini
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <i class="bi bi-star fs-6"></i>
                        Ocene i recenzije vlasnika
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <i class="bi bi-chat-dots fs-6"></i>
                        Direktna komunikacija
                    </li>
                </ul>
                <div class="auth-panel-dog text-center mt-4">🐾</div>
            </div>
        </div>

        <!-- ── Right form panel ── -->
        <div class="col-lg-8 auth-form-panel">
            <div class="auth-form-box">

                <h1 class="auth-form-title mb-1">Kreiraj nalog</h1>
                <p class="text-muted mb-4" style="font-size:0.9rem;">
                    Već imaš nalog?
                    <a href="login.html" class="fw-500 text-decoration-none" style="color:var(--color-primary);">Prijavi se</a>
                </p>

                <!-- Alert banner -->
                <div id="register-alert" class="auth-alert d-none mb-3" role="alert" aria-live="assertive"></div>

                <form id="register-form" novalidate aria-label="Forma za registraciju">

                    <!-- Ime / Prezime -->
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label for="first-name" class="form-label fw-500" style="font-size:0.85rem;">
                                Ime <span class="text-danger" aria-hidden="true">*</span>
                            </label>
                            <input
                                type="text"
                                id="first-name"
                                name="firstName"
                                class="auth-input"
                                placeholder="Vaše ime"
                                autocomplete="given-name"
                                required
                                aria-required="true"
                                aria-describedby="first-name-error"
                            >
                            <span class="field-error" id="first-name-error" role="alert"></span>
                        </div>
                        <div class="col-sm-6">
                            <label for="last-name" class="form-label fw-500" style="font-size:0.85rem;">
                                Prezime <span class="text-danger" aria-hidden="true">*</span>
                            </label>
                            <input
                                type="text"
                                id="last-name"
                                name="lastName"
                                class="auth-input"
                                placeholder="Vaše prezime"
                                autocomplete="family-name"
                                required
                                aria-required="true"
                                aria-describedby="last-name-error"
                            >
                            <span class="field-error" id="last-name-error" role="alert"></span>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-500" style="font-size:0.85rem;">
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

                    <!-- Telefon -->
                    <div class="mb-3">
                        <label for="phone" class="form-label fw-500" style="font-size:0.85rem;">Broj telefona</label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            class="auth-input"
                            placeholder="+381 60 123 4567"
                            autocomplete="tel"
                            aria-describedby="phone-error"
                        >
                        <span class="field-error" id="phone-error" role="alert"></span>
                    </div>

                    <!-- Adresa -->
                    <div class="mb-3">
                        <label for="address" class="form-label fw-500" style="font-size:0.85rem;">Adresa</label>
                        <input
                            type="text"
                            id="address"
                            name="address"
                            class="auth-input"
                            placeholder="Ulica i broj, grad"
                            autocomplete="street-address"
                        >
                    </div>

                    <!-- Lozinka -->
                    <div class="mb-3">
                        <label for="password" class="form-label fw-500" style="font-size:0.85rem;">
                            Lozinka <span class="text-danger" aria-hidden="true">*</span>
                        </label>
                        <div class="input-with-icon">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="auth-input"
                                placeholder="Minimalno 8 karaktera"
                                autocomplete="new-password"
                                required
                                aria-required="true"
                                aria-describedby="password-error password-hint"
                            >
                            <button type="button" class="toggle-password" aria-label="Prikaži/sakrij lozinku" data-target="password">
                                <i class="bi bi-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        <span class="field-hint" id="password-hint">Minimalno 8 karaktera, bar jedno veliko slovo i broj.</span>
                        <span class="field-error" id="password-error" role="alert"></span>
                    </div>

                    <!-- Potvrda lozinke -->
                    <div class="mb-3">
                        <label for="password-confirm" class="form-label fw-500" style="font-size:0.85rem;">
                            Potvrdi lozinku <span class="text-danger" aria-hidden="true">*</span>
                        </label>
                        <div class="input-with-icon">
                            <input
                                type="password"
                                id="password-confirm"
                                name="passwordConfirm"
                                class="auth-input"
                                placeholder="Ponovite lozinku"
                                autocomplete="new-password"
                                required
                                aria-required="true"
                                aria-describedby="password-confirm-error"
                            >
                            <button type="button" class="toggle-password" aria-label="Prikaži/sakrij potvrdu lozinke" data-target="password-confirm">
                                <i class="bi bi-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        <span class="field-error" id="password-confirm-error" role="alert"></span>
                    </div>

                    <!-- Walker toggle -->
                    <div class="walker-toggle-box mb-3" role="group" aria-labelledby="walker-toggle-label">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div>
                                <p class="walker-toggle-title" id="walker-toggle-label">
                                    <i class="bi bi-person-badge me-1" aria-hidden="true"></i>
                                    Registruj se kao šetač pasa
                                </p>
                                <p class="walker-toggle-desc">Zahteva odobrenje administratora nakon e-mail verifikacije.</p>
                            </div>
                            <label class="toggle-switch" aria-label="Postani šetač">
                                <input type="checkbox" id="is-walker" name="isWalker" role="switch" aria-checked="false">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <!-- Extra walker fields -->
                        <div id="walker-fields" class="walker-extra-fields d-none" aria-hidden="true">
                            <div class="mb-3">
                                <label for="walker-description" class="form-label fw-500" style="font-size:0.85rem;">Kratki opis (o sebi)</label>
                                <textarea
                                    id="walker-description"
                                    name="walkerDescription"
                                    class="auth-input"
                                    placeholder="Recite nešto o sebi, iskustvu sa psima..."
                                    rows="3"
                                    aria-describedby="walker-description-error"
                                ></textarea>
                                <span class="field-error" id="walker-description-error" role="alert"></span>
                            </div>

                            <div class="mb-0">
                                <label for="favorite-breed" class="form-label fw-500" style="font-size:0.85rem;">Omiljena rasa pasa</label>
                                <select id="favorite-breed" name="favoriteBreed" class="auth-input">
                                    <option value="">Izaberite rasu</option>
                                    <option value="labrador">Labrador</option>
                                    <option value="pudla">Pudla</option>
                                    <option value="zlatni-retriver">Zlatni retriver</option>
                                    <option value="nemacki-ovar">Nemački ovčar</option>
                                    <option value="buldog">Buldog</option>
                                    <option value="ostalo">Ostalo / Sve rase</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Terms -->
                    <div class="mb-3">
                        <label class="d-flex align-items-start gap-2 cursor-pointer" style="font-size:0.88rem; cursor:pointer;">
                            <input
                                type="checkbox"
                                id="terms"
                                name="terms"
                                class="mt-1 flex-shrink-0"
                                style="accent-color:var(--color-primary); width:16px; height:16px; cursor:pointer;"
                                required
                                aria-required="true"
                                aria-describedby="terms-error"
                            >
                            <span>
                                Prihvatam
                                <a href="#" target="_blank" rel="noopener noreferrer" style="color:var(--color-primary);" class="text-decoration-none">uslove korišćenja</a>
                                i
                                <a href="#" target="_blank" rel="noopener noreferrer" style="color:var(--color-primary);" class="text-decoration-none">politiku privatnosti</a>
                            </span>
                        </label>
                        <span class="field-error" id="terms-error" role="alert"></span>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="auth-submit-btn" id="register-submit-btn">
                        <span class="btn-text">Kreiraj nalog</span>
                        <span class="btn-spinner d-none" aria-hidden="true">
                            <i class="bi bi-arrow-repeat spin"></i>
                        </span>
                    </button>

                </form>
            </div>
        </div>

    </div>

    <?php include 'footer.php'; ?>
</main>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="register.js"></script>
</body>
</html>
