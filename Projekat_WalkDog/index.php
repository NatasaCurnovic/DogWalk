<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DogWalk – Pronađi savršenog šetača za svog psa</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<!-- ===== HERO ===== -->
<section class="hero-section" id="hero-section" aria-labelledby="hero-heading">
    <div class="container hero-content">
        <div class="hero-text">
            <h1 id="hero-heading">Pronađi savršenog šetača<br>za svog psa</h1>
            <p>Bezbedno, brzo i pouzdano – šetnja tvog ljubimca nikad nije bila lakša</p>
            <div class="hero-buttons">
                <a href="#walker-section" class="btn btn-success">Pronađi šetača</a>
                <a href="register.html" class="btn btn-outline-secondary">Postani šetač</a>
            </div>
        </div>
    </div>
</section>

<!-- ===== SEARCH ===== -->
<section class="search-section" id="search-section" aria-labelledby="search-heading">

    <h2 class="visually-hidden" id="search-heading">Pretraga šetača</h2>

    <div class="search-bar" role="search">
        <label for="search-input" class="visually-hidden">Pretraži šetače</label>
        <input
            type="text"
            id="search-input"
            placeholder="Pretraži šetače..."
            autocomplete="off"
            aria-label="Pretraži šetače po imenu ili opisu"
        >
        <button id="search-btn" aria-label="Pokreni pretragu">
            <i class="bi bi-search" aria-hidden="true"></i>S
        </button>
    </div>

    <!-- White strip with filters -->
    <div class="filter-strip">
        <div class="container">
            <div class="filters" role="group" aria-label="Filteri pretrage">

                <div class="filter-item">
                    <label for="city-filter">Grad / lokacija</label>
                    <input type="text" id="city-filter" placeholder="npr. Subotica" autocomplete="off">
                </div>

                <div class="filter-item">
                    <label for="breed-filter">Rasa psa</label>
                    <select id="breed-filter">
                        <option value="">Sve rase</option>
                        <option value="labrador">Labrador</option>
                        <option value="pudla">Pudla</option>
                        <option value="zlatni-retriver">Zlatni retriver</option>
                        <option value="nemacki-ovar">Nemački ovčar</option>
                        <option value="buldog">Buldog</option>
                    </select>
                </div>

                <div class="filter-item">
                    <label for="rating-filter">Minimalna ocena</label>
                    <select id="rating-filter">
                        <option value="">Sve ocene</option>
                        <option value="3">3+</option>
                        <option value="4">4+</option>
                        <option value="5">5</option>
                    </select>
                </div>

            </div>

            <!-- Search results container -->
            <div id="search-results-container" class="search-results-container" aria-live="polite" aria-label="Rezultati pretrage">
            </div>
        </div>
    </div>

</section>

<!-- ===== WALKERS SECTION ===== -->
<section class="walker-section" id="walker-section" aria-labelledby="top-rated-heading">
    <div class="container">

        <!-- Top Rated -->
        <h2 class="section-title" id="top-rated-heading">Najbolje ocenjeni šetači:</h2>
        <div
            class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-5 g-4 top-rated-list"
            id="top-rated-list"
            aria-label="Lista najbolje ocenjenih šetača"
        >
            <!-- Injected by main.js -->
        </div>

        <hr class="section-divider" aria-hidden="true">

        <!-- Most Active -->
        <h2 class="section-title" id="most-active-heading">Najaktivniji šetači:</h2>
        <div
            class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-5 g-4 most-active-list"
            id="most-active-list"
            aria-label="Lista najaktivnijih šetača">
        </div>

    </div>
</section>

<!-- ===== HOW IT WORKS ===== -->
<section class="how-section" id="how-section" aria-labelledby="how-heading">
    <div class="container">

        <div class="how-header">
            <h2 class="section-title" id="how-heading">Kako funkcioniše?</h2>
            <p class="how-subtitle">Tri jednostavna koraka do savršene šetnje za vašeg psa</p>
        </div>

        <!-- Decorative SVG wave connecting the three steps -->
        <div class="how-wave" aria-hidden="true">
            <svg viewBox="0 0 900 160" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M 80 130 Q 270 10 450 80 Q 630 150 820 30"
                    fill="none"
                    stroke="#6b7c59"
                    stroke-width="2.5"
                    stroke-dasharray="7 5"
                    opacity="0.4"
                />
            </svg>
        </div>

        <ol class="how-step-list" aria-label="Koraci kako funkcioniše DogWalk">

            <!-- Step 1 -->
            <li class="how-step">
                <div class="how-step__bubble" aria-hidden="true">
                    <i class="bi bi-search"></i>
                </div>
                <div class="how-step__body">
                    <h3 class="how-step__title">Pronađite šetača</h3>
                    <p class="how-step__desc">
                        Pretražite proverene šetače u vašem gradu i filtrirajte po oceni, rasi psa i lokaciji.
                    </p>
                </div>
            </li>

            <!-- Step 2 – vertically offset to follow wave -->
            <li class="how-step how-step--middle">
                <div class="how-step__bubble" aria-hidden="true">
                    <i class="bi bi-send"></i>
                </div>
                <div class="how-step__body">
                    <h3 class="how-step__title">Pošaljite zahtev</h3>
                    <p class="how-step__desc">
                        Kontaktirajte šetača direktno putem platforme. Unesite podatke o vašem psu i dogovorite termine.
                    </p>
                </div>
            </li>

            <!-- Step 3 -->
            <li class="how-step">
                <div class="how-step__bubble" aria-hidden="true">
                    <i class="bi bi-star"></i>
                </div>
                <div class="how-step__body">
                    <h3 class="how-step__title">Ocenite iskustvo</h3>
                    <p class="how-step__desc">
                        Nakon šetnje dobijate kod za ocenjivanje. Pomozite zajednici da pronađe najbolje šetače!
                    </p>
                </div>
            </li>

        </ol>

    </div>
</section>

<!-- ===== ABOUT US ===== -->

<section class="about-section py-5">
    <div class="container pt-5">
        <div class="row align-items-center g-5">

            <div class="col-lg-6">
                <div class="about-card position-relative p-5 shadow">

                    <img src="Images/Dog.png" class="dog-img" alt="dog">

                    <h2 class="mt-5">
                        Platforma kojoj vlasnici pasa veruju
                    </h2>

                    <p class="mt-3">
                        PawWalk je stvorena iz ljubavi prema psima i razumevanja koliko je važno
                        da vaš ljubimac bude u sigurnim rukama. Svaki šetač prolazi kroz verifikaciju
                        i dobija odobrenje administratora pre nego što može da prima zahteve.
                    </p>

                    <button class="btn btn-custom mt-2">Postani šetač</button>

                </div>
            </div>

            <div class="col-lg-6">
                <div class="d-flex flex-column gap-3">

                    <div class="feature-title">O nama:</div>

                    <div class="feature">Registracija sa verifikacijom e-mail adrese</div>
                    <div class="feature">Administratorsko odobrenje za šetače</div>
                    <div class="feature">Dnevnik svake šetnje sa trajanjem i putanjom</div>
                    <div class="feature">Sistem ocenjivanja sa jedinstvenim kodovima</div>

                </div>
            </div>

        </div>
    </div>
</section>

<?php include 'footer.php'; ?>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- External script -->
<script src="main.js"></script>

</body>
</html>
