<?php
session_start();
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DogWalk – Zaboravljena lozinka</title>

    <link rel="icon" type="image/png" href="Images/fav_icon.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="style.css">

    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .forgot-wrapper {
            min-height: calc(100vh - 90px);
        }

        .forgot-card {
            border: none;
            border-radius: 20px;
        }

        .forgot-icon {
            font-size: 3.5rem;
            color: #6f7f5f;
        }

        .btn-dogwalk {
            background-color: #6f7f5f;
            border-color: #6f7f5f;
        }

        .btn-dogwalk:hover {
            background-color: #5d6b4f;
            border-color: #5d6b4f;
        }

        .back-link {
            color: #6f7f5f;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<main class="forgot-wrapper d-flex align-items-center py-5">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">

                <div class="card forgot-card shadow-sm">

                    <div class="card-body p-4 p-lg-5">

                        <div class="text-center mb-4">

                            <div class="forgot-icon mb-3">
                                <i class="bi bi-key"></i>
                            </div>

                            <h1 class="h3 fw-bold">
                                Zaboravili ste lozinku?
                            </h1>

                            <p class="text-muted mb-0">
                                Unesite e-mail adresu vašeg naloga i poslaćemo vam
                                link za resetovanje lozinke.
                            </p>

                        </div>

                        <div id="forgot-alert"
                             class="alert alert-danger d-none"
                             role="alert">
                        </div>

                        <div id="forgot-step-request">

                            <form id="forgot-form">

                                <div class="mb-3">

                                    <label for="email" class="form-label">
                                        E-mail adresa
                                    </label>

                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        class="form-control"
                                        placeholder="vasa@email.com"
                                        required
                                    >

                                    <div id="email-error"
                                         class="invalid-feedback">
                                    </div>

                                </div>

                                <button
                                    type="submit"
                                    class="btn btn-dogwalk text-white w-100 py-2">

                                    <span class="btn-text">
                                        Pošalji link za resetovanje
                                    </span>

                                    <span class="btn-spinner d-none">
                                        <span class="spinner-border spinner-border-sm"></span>
                                    </span>

                                </button>

                            </form>

                        </div>

                        <div id="forgot-step-success" class="d-none">

                            <div class="text-center py-3">

                                <div class="display-3 text-success mb-3">
                                    <i class="bi bi-envelope-check"></i>
                                </div>

                                <h2 class="h4">
                                    Link je poslat!
                                </h2>

                                <p class="text-muted">
                                    Proverite vaš inbox.
                                    Link za resetovanje lozinke važi 30 minuta.
                                </p>

                            </div>

                        </div>

                        <div class="text-center mt-4">

                            <a href="login.php" class="back-link">
                                Nazad na prijavu
                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</main>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="auth.js"></script>

</body>
</html>