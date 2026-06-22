<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

$query = trim($_GET['q'] ?? '');
$city = trim($_GET['city'] ?? '');
$breed = trim($_GET['breed'] ?? '');
$rating = (float) ($_GET['rating'] ?? 0);

$pdo = getDB();

$sql = "
    SELECT u.id, u.first_name, u.last_name,
           wp.photo, wp.city, wp.experience_years, wp.favorite_breed,
           COALESCE(ROUND(AVG(r.score), 1), 0) AS avg_score,
           COUNT(DISTINCT r.id) AS total_ratings
    FROM users u
    JOIN walker_profiles wp ON wp.user_id = u.id
    LEFT JOIN ratings r  ON r.walker_id = u.id
    WHERE u.role = 'walker'
      AND u.is_active  = 1
      AND u.is_approved = 1
      AND u.is_banned  = 0
";

$params = [];

if (!empty($query)) {
    $sql .= " AND CONCAT(u.first_name, ' ', u.last_name) LIKE ?";
    $params[] = '%' . $query . '%';
}

if (!empty($city)) {
    $sql .= " AND LOWER(wp.city) = ?";
    $params[] = strtolower($city);
}

if (!empty($breed)) {
    $sql .= " AND LOWER(wp.favorite_breed) LIKE ?";
    $params[] = '%' . strtolower($breed) . '%';
}

$sql .= " GROUP BY u.id, u.first_name, u.last_name,
                   wp.photo, wp.city, wp.experience_years, wp.favorite_breed";

if ($rating > 0) {
    $sql .= " HAVING avg_score >= ?";
    $params[] = $rating;
}

$sql .= " ORDER BY avg_score DESC LIMIT 20";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$walkers = $stmt->fetchAll();

echo json_encode(['walkers' => $walkers]);

