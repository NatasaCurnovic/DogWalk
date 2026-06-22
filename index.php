<?php
session_start();
require_once 'db.php';
require_once 'UserManager.php';

$userManager = new UserManager();
$topRatedWalkers = $userManager->getTopRatedWalkers();
$mostActiveWalkers = $userManager->getMostActiveWalkers();
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DogWalk – Pronađi savršenog šetača za svog psa</title>

    <link rel="icon" type="image/png" href="Images/fav_icon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital@0;1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

    <style>
        :root {
            --accent: #3F4014;
            --accent-light: #fdf3e0;
            --green: #5a6a4a;
            --green-light: #e8f5eb;
            --text-dark: #5a6a4a;
            --text-muted: #3F4014;
            --card-shadow: 0 4px 20px rgba(0,0,0,0.08);
            --card-hover-shadow: 0 12px 40px rgba(0,0,0,0.15);
            --radius: 16px;
        }


        .filter-strip {
            background: #fff;
            padding: 1.25rem 0;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }

        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: flex-end;
        }

        .filter-item { display: flex; flex-direction: column; gap: .35rem; }
        .filter-item label { font-size: .78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: .04em; }
        .filter-item input,
        .filter-item select {
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            padding: .45rem .85rem;
            font-family: 'DM Sans', sans-serif;
            font-size: .9rem;
            outline: none;
            transition: border-color .2s;
            background: #fff;
            color: #3a3a3a;
            min-width: 160px;
            appearance: none;
            -webkit-appearance: none;
        }

        .filter-item select option {
            color: #3a3a3a;
            background: #fff;
        }
        .filter-item input:focus,
        .filter-item select:focus { border-color: var(--accent); }

        #clear-filters{
            border: 2px solid var(--green);
            color: var(--green);
            background: transparent;
            transition: all 0.3s ease;
        }

        #clear-filters:hover{
            background: var(--green);
            border-color: var(--green);
            color: white;
        }

        .walker-section {
            padding: 4rem 0 5rem;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 1.75rem;
            display: flex;
            align-items: center;
            gap: .6rem;
        }

        .section-title::before {
            content: '';
            display: inline-block;
            width: 6px;
            height: 30px;
            border-radius: 4px;
            background: var(--accent);
        }

        .section-title.green::before { background: var(--green); }

        .section-divider {
            border: none;
            border-top: 2px dashed #e5e7eb;
            margin: 3rem 0;
        }

        .walker-card {
            background: #fff;
            border: none;
            border-radius: var(--radius);
            box-shadow: var(--card-shadow);
            transition: transform .25s ease, box-shadow .25s ease;
            overflow: hidden;
            height: 100%;
        }

        .walker-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--card-hover-shadow);
        }

        .walker-card .card-img-wrap {
            position: relative;
            height: 160px;
            overflow: hidden;
            background: #f0ede8;
        }

        .walker-card .card-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .4s ease;
        }

        .walker-card:hover .card-img-wrap img {
            transform: scale(1.06);
        }

        .walker-card .badge-top {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--accent);
            color: #fff;
            font-size: .7rem;
            font-weight: 700;
            padding: .25rem .6rem;
            border-radius: 20px;
            letter-spacing: .03em;
            box-shadow: 0 2px 8px rgba(232,168,56,.4);
        }

        .walker-card .badge-top.green {
            background: var(--green);
            box-shadow: 0 2px 8px rgba(58,125,68,.4);
        }

        .walker-card .card-body {
            padding: 1rem 1.1rem 1.2rem;
        }

        .walker-card .walker-name {
            font-family: 'DM Sans', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: .15rem;
            color: var(--text-dark);
        }

        .walker-card .walker-location {
            font-size: .78rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: .25rem;
            margin-bottom: .6rem;
        }

        .walker-card .walker-stars {
            color: var(--accent);
            font-size: .8rem;
            margin-bottom: .5rem;
        }

        .walker-card .walker-stats {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
        }

        .walker-card .stat-pill {
            background: var(--accent-light);
            color: #8a5e10;
            font-size: .7rem;
            font-weight: 600;
            padding: .2rem .6rem;
            border-radius: 20px;
        }

        .walker-card .stat-pill.green {
            background: var(--green-light);
            color: #235229;
        }

        .walker-card .btn-profile {
            margin-top: .85rem;
            width: 100%;
            background: var(--text-dark);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: .45rem;
            font-size: .82rem;
            font-weight: 600;
            letter-spacing: .03em;
            transition: background .2s;
            cursor: pointer;
        }

        .walker-card .btn-profile:hover { background: #3F4014; }
        .walker-card .btn-profile.green { background: var(--green); }
        .walker-card .btn-profile.green:hover { background: #3F4014; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<section class="hero-section" id="hero-section" aria-labelledby="hero-heading">
    <div class="container hero-content">
        <div class="hero-text">
            <h1 class="hero-animate animate__fadeInDown">Pronađi savršenog šetača<br>za svog psa</h1>
            <p class="hero-animate animate__fadeIn animate__delay-1s">
                Bezbedno, brzo i pouzdano – šetnja tvog ljubimca nikad nije bila lakša
            </p>
            <div class="hero-buttons hero-animate animate__fadeInUp animate__delay-2s">
                <a href="#walker-section" class="btn btn-success">Pronađi šetača</a>
                <a href="register.php" class="btn btn-outline-secondary">Postani šetač</a>
            </div>
        </div>
    </div>
</section>

<section class="search-section" id="search-section" aria-labelledby="search-heading">

    <h2 class="visually-hidden" id="search-heading">Pretraga šetača</h2>

    <div class="search-bar" role="search">
        <label for="search-input" class="visually-hidden">Pretraži šetače</label>
        <input
                type="text"
                id="search-input"
                placeholder="Pretraži šetače po imenu..."
                autocomplete="off"
                aria-label="Pretraži šetače po imenu"
        >
        <button id="search-btn" aria-label="Pokreni pretragu">
            <i class="bi bi-search" aria-hidden="true"></i>
        </button>
    </div>

    <div class="filter-strip">
        <div class="container">
            <div class="filters" role="group" aria-label="Filteri pretrage">

                <div class="filter-item">
                    <label for="city-filter">Grad / lokacija</label>
                    <select id="city-filter" class="form-select" aria-label="Filtriraj po gradu">
                        <option value="">Svi gradovi</option>
                    </select>
                </div>

                <div class="filter-item">
                    <label for="breed-filter">Rasa psa</label>
                    <select id="breed-filter" class="form-select" aria-label="Filtriraj po rasi psa">
                        <option value="">Sve rase</option>
                        <option value="labrador">Labrador</option>
                        <option value="pudla">Pudla</option>
                        <option value="zlatni retriver">Zlatni retriver</option>
                        <option value="nemački ovčar">Nemački ovčar</option>
                        <option value="buldog">Buldog</option>
                    </select>
                </div>

                <div class="filter-item">
                    <label for="rating-filter">Minimalna ocena</label>
                    <select id="rating-filter" class="form-select" aria-label="Filtriraj po oceni">
                        <option value="">Sve ocene</option>
                        <option value="3">3+</option>
                        <option value="4">4+</option>
                        <option value="5">5</option>
                    </select>
                </div>

                <div class="filter-item d-flex align-items-end">
                    <button id="clear-filters" class="btn btn-outline-secondary w-100" type="button"
                            aria-label="Očisti sve filtere">
                        <i class="bi bi-x-circle me-1" aria-hidden="true"></i>Očisti
                    </button>
                </div>

            </div>

            <div id="no-results-msg"
                 class="d-none text-center text-muted py-4"
                 role="status"
                 aria-live="polite">
                <i class="bi bi-search fs-3 d-block mb-2" aria-hidden="true"></i>
                Nijedan šetač ne odgovara izabranim filterima.
            </div>

            <div id="search-results-container"
                 class="search-results-container mt-3"
                 aria-live="polite"
                 aria-label="Rezultati pretrage">
            </div>

        </div>
    </div>

</section>

<section class="walker-section" id="walker-section" aria-labelledby="top-rated-heading">
    <div class="container">

        <h2 class="section-title animate-on-scroll">
            Najbolje ocenjeni šetači:
        </h2>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-5 g-4 top-rated-list"
             id="top-rated-list"
             aria-label="Lista najbolje ocenjenih šetača">

            <?php if (!empty($topRatedWalkers)): ?>
                <?php foreach ($topRatedWalkers as $walker): ?>
                    <div class="col">
                        <div class="walker-card card"
                             data-name="<?= htmlspecialchars(strtolower($walker['first_name'] . ' ' . $walker['last_name'])) ?>"
                             data-city="<?= htmlspecialchars(strtolower($walker['city'] ?? '')) ?>"
                             data-rating="<?= $walker['avg_score'] ?>"
                             data-breed="<?= htmlspecialchars(strtolower($walker['favorite_breed'] ?? '')) ?>">
                            <div class="card-img-wrap">
                                <?php if ($walker['photo']): ?>
                                    <?php
                                    $defaultImages = [
                                            "Images/walker1.jpg",
                                            "Images/walker2.jpg",
                                            "Images/walker3.jpg",
                                            "Images/walker4.jpg",
                                            "Images/walker5.jpg",
                                            "Images/walker6.jpg",
                                            "Images/walker7.jpg",
                                            "Images/walker8.jpg",
                                            "Images/walker9.jpg",
                                            "Images/walker10.jpg",
                                            "Images/walker11.jpg"
                                    ];
                                    ?>
                                    <img src="<?= $defaultImages[$walker['id'] % count($defaultImages)] ?>"
                                         alt="<?= htmlspecialchars($walker['first_name']) ?>">
                                <?php else: ?>
                                    <?php
                                    $initials = strtoupper(substr($walker['first_name'], 0, 1) . substr($walker['last_name'], 0, 1));
                                    $colors = ['#5a6a4a','#3F4014','#6b7c59','#8a9a70','#4a5a3a'];
                                    $colorIndex = crc32($walker['first_name']) % count($colors);
                                    $bgColor = $colors[abs($colorIndex)];
                                    ?>
                                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:<?= $bgColor ?>;font-family:'DM Sans',sans-serif;font-size:2rem;font-weight:700;color:rgba(255,255,255,0.9);">
                                        <?= $initials ?>
                                    </div>
                                <?php endif; ?>
                                <span class="badge-top">
                                <?= $walker['avg_score'] > 0 ? $walker['avg_score'] : 'Novo' ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="walker-name"><?= htmlspecialchars($walker['first_name'] . ' ' . $walker['last_name']) ?></div>
                                <div class="walker-location"><i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($walker['city'] ?? 'Nepoznato') ?></div>
                                <div class="walker-stars">
                                    <?php
                                    $score = (float) $walker['avg_score'];
                                    $full = (int) $score;
                                    $half = ($score - $full >= 0.5) ? 1 : 0;
                                    $empty = 5 - $full - $half;
                                    for ($s = 0; $s < $full; $s++) echo '<i class="bi bi-star-fill"></i>';
                                    if ($half) echo '<i class="bi bi-star-half"></i>';
                                    for ($s = 0; $s < $empty; $s++) echo '<i class="bi bi-star"></i>';
                                    ?>
                                    <span class="text-muted ms-1" style="font-size:.75rem;">(<?= $walker['total_ratings'] ?>)</span>
                                </div>
                                <div class="walker-stats">
                                    <?php if ($walker['experience_years']): ?>
                                        <span class="stat-pill"><?= (int)$walker['experience_years'] ?> god. iskustva</span>
                                    <?php endif; ?>
                                    <?php if ($walker['city']): ?>
                                        <span class="stat-pill"><?= htmlspecialchars($walker['city']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <a href="walker_profile.php?id=<?= $walker['id'] ?>" class="btn-profile d-block text-decoration-none text-center">Pogledaj profil</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center text-muted py-4">
                    <i class="bi bi-star fs-3 d-block mb-2"></i>
                    Još nema ocenjenih šetača.
                </div>
            <?php endif; ?>

        </div>

        <p id="top-rated-empty"
           class="d-none text-muted text-center py-3"
           role="status"
           aria-live="polite">
            Nema ocenjenih šetača koji odgovaraju filterima.
        </p>

        <hr class="section-divider" aria-hidden="true">

        <h2 class="section-title green animate-on-scroll animate__fadeInRight">
            Najaktivniji šetači:
        </h2>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-5 g-4 most-active-list"
             id="most-active-list"
             aria-label="Lista najaktivnijih šetača">

            <?php if (!empty($mostActiveWalkers)): ?>
                <?php foreach ($mostActiveWalkers as $walker): ?>
                    <div class="col">
                        <div class="walker-card card"
                             data-name="<?= htmlspecialchars(strtolower($walker['first_name'] . ' ' . $walker['last_name'])) ?>"
                             data-city="<?= htmlspecialchars(strtolower($walker['city'] ?? '')) ?>"
                             data-rating="<?= $walker['avg_score'] ?>"
                             data-breed="">
                            <div class="card-img-wrap">
                                <?php if ($walker['photo']): ?>
                                    <?php
                                    $defaultImages = [
                                            "Images/walker1.jpg",
                                            "Images/walker2.jpg",
                                            "Images/walker3.jpg",
                                            "Images/walker4.jpg",
                                            "Images/walker5.jpg",
                                            "Images/walker6.jpg",
                                            "Images/walker7.jpg",
                                            "Images/walker8.jpg",
                                            "Images/walker9.jpg",
                                            "Images/walker10.jpg"
                                    ];
                                    ?>

                                    <img src="<?= $defaultImages[$walker['id'] % count($defaultImages)] ?>"
                                         alt="<?= htmlspecialchars($walker['first_name']) ?>">
                                <?php else: ?>
                                    <?php
                                    $initials = strtoupper(substr($walker['first_name'], 0, 1) . substr($walker['last_name'], 0, 1));
                                    $colors = ['#5a6a4a','#3F4014','#6b7c59','#8a9a70','#4a5a3a'];
                                    $colorIndex = crc32($walker['first_name']) % count($colors);
                                    $bgColor = $colors[abs($colorIndex)];
                                    ?>
                                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:<?= $bgColor ?>;font-family:'DM Sans',sans-serif;font-size:2rem;font-weight:700;color:rgba(255,255,255,0.9);">
                                        <?= $initials ?>
                                    </div>
                                <?php endif; ?>
                                <span class="badge-top green">
                            <?= (int)$walker['total_walks'] ?> šetnji
                        </span>
                            </div>
                            <div class="card-body">
                                <div class="walker-name"><?= htmlspecialchars($walker['first_name'] . ' ' . $walker['last_name']) ?></div>
                                <div class="walker-location"><i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($walker['city'] ?? 'Nepoznato') ?></div>
                                <div class="walker-stars" style="color: var(--green);">
                                    <?php
                                    $score = (float) $walker['avg_score'];
                                    $full = (int) $score;
                                    $half = ($score - $full >= 0.5) ? 1 : 0;
                                    $empty = 5 - $full - $half;
                                    for ($s = 0; $s < $full; $s++) echo '<i class="bi bi-star-fill"></i>';
                                    if ($half) echo '<i class="bi bi-star-half"></i>';
                                    for ($s = 0; $s < $empty; $s++) echo '<i class="bi bi-star"></i>';
                                    ?>
                                    <span class="text-muted ms-1" style="font-size:.75rem;">(<?= $walker['avg_score'] ?>)</span>
                                </div>
                                <div class="walker-stats">
                                    <span class="stat-pill green">Dostupan</span>
                                    <?php if ($walker['city']): ?>
                                        <span class="stat-pill green"><?= htmlspecialchars($walker['city']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <a href="walker_profile.php?id=<?= $walker['id'] ?>" class="btn-profile green d-block text-decoration-none text-center">Pogledaj profil</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center text-muted py-4">
                    <i class="bi bi-activity fs-3 d-block mb-2"></i>
                    Još nema aktivnih šetača.
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>

<section class="how-section" id="how-section" aria-labelledby="how-heading">
    <div class="container">
        <div class="how-header">
            <h2 class="section-title" id="how-heading">Kako funkcioniše?</h2>
            <p class="how-subtitle">Tri jednostavna koraka do savršene šetnje za vašeg psa</p>
        </div>
        <div class="how-wave" aria-hidden="true">
            <svg viewBox="0 0 900 160" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M 80 130 Q 270 10 450 80 Q 630 150 820 30"
                      fill="none" stroke="#6b7c59" stroke-width="2.5"
                      stroke-dasharray="7 5" opacity="0.4"/>
            </svg>
        </div>
        <ol class="how-step-list" aria-label="Koraci kako funkcioniše DogWalk">
            <li class="how-step animate-on-scroll animate__fadeInUp">
                <div class="how-step__bubble" aria-hidden="true"><i class="bi bi-search"></i></div>
                <div class="how-step__body">
                    <h3 class="how-step__title">Pronađite šetača</h3>
                    <p class="how-step__desc">Pretražite proverene šetače u vašem gradu i filtrirajte po oceni, rasi psa i lokaciji.</p>
                </div>
            </li>
            <li class="how-step how-step--middle animate-on-scroll animate__zoomIn">
                <div class="how-step__bubble" aria-hidden="true"><i class="bi bi-send"></i></div>
                <div class="how-step__body">
                    <h3 class="how-step__title">Pošaljite zahtev</h3>
                    <p class="how-step__desc">Kontaktirajte šetača direktno putem platforme. Unesite podatke o vašem psu i dogovorite termine.</p>
                </div>
            </li>
            <li class="how-step animate-on-scroll animate__fadeInUp">
                <div class="how-step__bubble" aria-hidden="true"><i class="bi bi-star"></i></div>
                <div class="how-step__body">
                    <h3 class="how-step__title">Ocenite iskustvo</h3>
                    <p class="how-step__desc">Nakon šetnje dobijate kod za ocenjivanje. Pomozite zajednici da pronađe najbolje šetače!</p>
                </div>
            </li>
        </ol>
    </div>
</section>

<section class="about-section" id="o-nama">
    <div class="container">
        <div class="row g-4 g-xl-5 align-items-start">

            <div class="col-12 col-lg-5 left-col">
                <div class="about-card animate-on-scroll animate__fadeInLeft">

                    <div class="card-top">
                        <img src="Images/Dog.png" alt="PawWalk štene" class="dog-img"/>
                    </div>

                    <h2 class="about-title">
                        Platforma kojoj vlasnici pasa veruju
                    </h2>

                    <div class="about-body">
                        <p class="about-text">
                            PawWalk je stvorena iz ljubavi prema psima i razumevanja koliko je
                            važno da vaš ljubimac bude u sigurnim rukama. Svaki šetač prolazi
                            kroz verifikaciju i dobija odobrenje administratora pre nego što
                            može da prima zahteve.
                        </p>
                        <button class="btn-walker">Postani šetač</button>
                    </div>

                </div>
            </div>

            <div class="col-12 col-lg-7 right-col">
                <div class="features-list animate-on-scroll animate__fadeInRight">
                    <div class="fi fi-header">O nama:</div>
                    <div class="fi fi-light">Registracija sa verifikacijom e-mail adrese</div>
                    <div class="fi fi-light">Administratorsko odobrenje za šetač</div>
                    <div class="fi fi-light">Dnevnik svake šetnje sa trajanjem i putanjom</div>
                    <div class="fi fi-light">Sistem ocenjivanja sa jedinstvenim kodovima</div>
                    <div class="fi fi-light">Direktna komunikacija vlasnik ↔ šetač</div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="index.js"></script>

</body>
</html>
