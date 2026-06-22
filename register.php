<?php
session_start();
require_once 'db.php';
require_once 'mail_helper.php';
require_once 'UserManager.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';
$old = ['first_name'=>'','last_name'=>'','email'=>'','phone'=>'','address'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name']  ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password  = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $isWalker = isset($_POST['is_walker']);

    $old = ['first_name'=>$firstName,'last_name'=>$lastName,'email'=>$email,'phone'=>$phone,'address'=>$address];

    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($password2)) {
        $error = "Popunite sva obavezna polja.";
    } elseif (!isset($_POST['terms'])) {
        $error = "Morate prihvatiti uslove koriscenja.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email adresa nije ispravna.";
    } elseif ($password !== $password2) {
        $error = "Lozinke se ne poklapaju.";
    } elseif (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $error = "Lozinka mora imati 8 karaktera, veliko slovo i broj.";
    } else {
        $userManager = new UserManager();
        $activationToken = bin2hex(random_bytes(32));

        if ($userManager->emailExists($email)) {
            $error = "Korisnik sa ovom email adresom vec postoji.";
        } else {
            $userId = $userManager->register([
                    'first_name'=> $firstName,
                    'last_name'=> $lastName,
                    'email'=> $email,
                    'password'=> $password,
                    'phone'=> $phone ?: null,
                    'address'=> $address ?: null,
                    'role'=> $isWalker ? 'walker' : 'owner',
                    'activation_token' => $activationToken,
            ]);

            if ($isWalker && $userId) {
                $bio= trim($_POST['walker_bio'] ?? '');
                $breed = trim($_POST['walker_breed'] ?? '');
                $price = trim($_POST['walker_price'] ?? '');
                $price = ($price !== '' && is_numeric($price) && $price >= 0) ? $price : null;
                $pdo= getDB();
                $pdo->prepare("INSERT INTO walker_profiles (user_id, description, favorite_breed, price_per_hour) VALUES (?,?,?,?)")
                        ->execute([$userId, $bio ?: null, $breed ?: null, $price]);
            }

            $activationLink = app_url('active.php?token=' . urlencode($activationToken));
            send_app_mail(
                    $email,
                    trim($firstName . ' ' . $lastName),
                    'Aktivacija naloga - DogWalk',
                    "Zdravo {$firstName},\r\n\r\n Kliknite na link da aktivirate nalog:\r\n{$activationLink}\r\n\r\nAko niste vi kreirali nalog, ignorišite ovaj email."
            );

            $success = $isWalker
                    ? "Nalog je kreiran! Proverite email za aktivacioni link. Nakon aktivacije cekate odobrenje administratora."
                    : "Nalog je kreiran! Proverite email i kliknite na aktivacioni link.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>DogWalk – Kreiraj nalog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="register.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<main>
    <div class="card-form">
        <h1 class="page-title">Kreiraj nalog</h1>
        <p class="subtitle mb-4" style="font-size:.9rem;color:var(--muted)">
            Već imaš nalog? <a href="login.php">Prijavi se</a>
        </p>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php else: ?>

            <form id="regForm" action="register.php" method="POST" novalidate>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label">Ime<span class="required-star">*</span></label>
                        <input type="text" name="first_name" class="form-control" placeholder="Vaše ime"
                               id="fname" value="<?= htmlspecialchars($old['first_name']) ?>" required/>
                        <div class="invalid-feedback">Unesite ime.</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Prezime<span class="required-star">*</span></label>
                        <input type="text" name="last_name" class="form-control" placeholder="Vaše prezime"
                               id="lname" value="<?= htmlspecialchars($old['last_name']) ?>" required/>
                        <div class="invalid-feedback">Unesite prezime.</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">E-mail adresa<span class="required-star">*</span></label>
                    <input type="email" name="email" class="form-control" placeholder="vasa@email.com"
                           id="email" value="<?= htmlspecialchars($old['email']) ?>" required/>
                    <div class="hint-text" id="emailCheck"></div>
                    <div class="invalid-feedback">Unesite ispravnu e-mail adresu.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Broj telefona</label>
                    <input type="tel" name="phone" class="form-control" placeholder="+381 60 123 4567"
                           id="phone" value="<?= htmlspecialchars($old['phone']) ?>"/>
                </div>

                <div class="mb-3">
                    <label class="form-label">Adresa</label>
                    <input type="text" name="address" class="form-control" placeholder="Ulica i broj, grad"
                           id="address" value="<?= htmlspecialchars($old['address']) ?>"/>
                </div>

                <div class="mb-3">
                    <label class="form-label">Lozinka<span class="required-star">*</span></label>
                    <div class="input-group">
                        <input type="password" name="password" class="form-control"
                               placeholder="Minimalno 8 karaktera" id="pwd" required/>
                        <button class="btn-eye" type="button" onclick="togglePwd('pwd', this)">👁</button>
                    </div>
                    <div class="hint-text">Minimalno 8 karaktera, bar jedno veliko slovo i broj.</div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Potvrdi lozinku<span class="required-star">*</span></label>
                    <div class="input-group">
                        <input type="password" name="password2" class="form-control"
                               placeholder="Ponovite lozinku" id="pwd2" required/>
                        <button class="btn-eye" type="button" onclick="togglePwd('pwd2', this)">👁</button>
                    </div>
                    <div class="hint-text" id="pwd2Msg"></div>
                </div>

                <div class="walker-box mb-4" id="walkerBox">
                    <div class="walker-header" onclick="toggleWalker()">
                        <div>
                            <div class="walker-title">Registruj se kao šetač pasa</div>
                            <div class="walker-subtitle">Zahteva odobrenje administratora nakon e-mail verifikacije.</div>
                        </div>
                        <label class="toggle-switch" onclick="event.stopPropagation()">
                            <input type="checkbox" name="is_walker" id="walkerToggle" onchange="toggleWalker()"/>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="walker-extra" id="walkerExtra">
                        <hr class="walker-divider"/>
                        <div class="mb-3">
                            <label class="form-label">Kratki opis (o sebi)</label>
                            <textarea class="form-control" name="walker_bio" id="walkerBio" rows="3"
                                      placeholder="Recite nešto o sebi, iskustvu sa psima…" style="resize:vertical"></textarea>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Omiljena rasa pasa</label>
                            <select class="form-select" name="walker_breed" id="walkerBreed">
                                <option value="" disabled selected>Izaberite rasu</option>
                                <option>Labrador Retriver</option>
                                <option>Nemačka Ovčarka</option>
                                <option>Zlatni Retriver</option>
                                <option>Bulmastif</option>
                                <option>Pudla</option>
                                <option>Bišon Frize</option>
                                <option>Begel</option>
                                <option>Bokser</option>
                                <option>Sibirski Haski</option>
                                <option>Sve rase</option>
                            </select>
                        </div>
                        <div class="mb-1">
                            <label class="form-label">Cena po šetnji (RSD)</label>
                            <input type="number" class="form-control" name="walker_price" id="walkerPrice"
                                   placeholder="npr. 800" min="0"/>
                        </div>
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="terms" id="terms" required/>
                    <label class="form-check-label" for="terms">
                        Prihvatam <a href="#">uslove korišćenja</a> i <a href="#">politiku privatnosti</a>
                    </label>
                    <div class="invalid-feedback">Morate prihvatiti uslove.</div>
                </div>

                <button type="submit" class="btn-submit">Kreiraj nalog</button>
            </form>

        <?php endif; ?>
    </div>
</main>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="register.js"></script>
</body>
</html>
