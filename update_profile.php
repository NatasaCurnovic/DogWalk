<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pdo = getDB();
$id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$password = $_POST['password']  ?? '';
$password2 = $_POST['password2'] ?? '';

if (empty($first_name) || empty($last_name)) {
    header('Location: profile.php?error=1');
    exit;
}

// Change password - optional
if (!empty($password)) {
    if ($password !== $password2 ||
        strlen($password) < 8 ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[0-9]/', $password)) {
        header('Location: profile.php?error=1');
        exit;
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("
        UPDATE users
        SET first_name = ?, last_name = ?, phone = ?, address = ?, password_hash = ?
        WHERE id = ?
    ");
    $stmt->execute([$first_name, $last_name, $phone ?: null, $address ?: null, $hash, $id]);
} else {
    $stmt = $pdo->prepare("
        UPDATE users
        SET first_name = ?, last_name = ?, phone = ?, address = ?
        WHERE id = ?
    ");
    $stmt->execute([$first_name, $last_name, $phone ?: null, $address ?: null, $id]);
}

$_SESSION['user_name'] = $first_name . ' ' . $last_name;

// If walker, update walker_profile
if ($user['role'] === 'walker') {
    $bio = trim($_POST['walker_bio'] ?? '');
    $city = trim($_POST['walker_city'] ?? '');
    $breed = trim($_POST['walker_breed'] ?? '');
    $experience = (int)($_POST['walker_experience'] ?? 0);
    $price = trim($_POST['walker_price'] ?? '');
    $price = ($price !== '' && is_numeric($price) && $price >= 0) ? $price : null;
    $available  = isset($_POST['walker_available']) ? 1 : 0;

    // Check if walker_profile exists
    $chk = $pdo->prepare("SELECT id FROM walker_profiles WHERE user_id = ?");
    $chk->execute([$id]);
    $exists = $chk->fetch();

    if ($exists) {
        $stmt2 = $pdo->prepare("
            UPDATE walker_profiles
            SET description = ?, city = ?, favorite_breed = ?,
                experience_years = ?, price_per_hour = ?, is_available = ?
            WHERE user_id = ?
        ");
        $stmt2->execute([$bio ?: null, $city ?: null, $breed ?: null,
            $experience, $price, $available, $id]);
    } else {
        $stmt2 = $pdo->prepare("
            INSERT INTO walker_profiles
                (user_id, description, city, favorite_breed, experience_years, price_per_hour, is_available)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt2->execute([$id, $bio ?: null, $city ?: null, $breed ?: null,
            $experience, $price, $available]);
    }
}

header('Location: profile.php?saved=1');
exit;
