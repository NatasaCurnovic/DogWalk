<?php
require_once __DIR__ . '/db.php';

class UserManager
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getDB();
    }

    public function emailExists($email)
    {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute(array($email));
        return (bool) $stmt->fetch();
    }

    public function findByEmail($email)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute(array($email));
        return $stmt->fetch();
    }

    public function findById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute(array($id));
        return $stmt->fetch();
    }

    public function register($data)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO users
                (first_name, last_name, email, password_hash, phone, address, role, is_active, is_approved, activation_token)
            VALUES (?, ?, ?, ?, ?, ?, ?, 0, 0, ?)
        ");
        $stmt->execute(array(
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            isset($data['phone'])   ? $data['phone'] : null,
            isset($data['address']) ? $data['address'] : null,
            $data['role'],
            $data['activation_token'],
        ));
        return (int) $this->pdo->lastInsertId();
    }

    public function activateByToken($token)
    {
        $stmt = $this->pdo->prepare("SELECT id, is_active FROM users WHERE activation_token = ?");
        $stmt->execute(array($token));
        $user = $stmt->fetch();

        if (!$user || $user['is_active']) {
            return false;
        }

        $update = $this->pdo->prepare("UPDATE users SET is_active = 1, activation_token = NULL WHERE id = ?");
        $update->execute(array($user['id']));
        return true;
    }

    public function updateProfile($userId, $data)
    {
        $stmt = $this->pdo->prepare("
            UPDATE users
            SET first_name = ?, last_name = ?, phone = ?, address = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute(array(
            $data['first_name'],
            $data['last_name'],
            isset($data['phone']) ? $data['phone'] : null,
            isset($data['address']) ? $data['address'] : null,
            $userId,
        ));
    }


    public function updatePassword($userId, $newPassword)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        return $stmt->execute(array(password_hash($newPassword, PASSWORD_BCRYPT), $userId));
    }

    public function setResetToken($userId, $token)
    {
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $stmt = $this->pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE id = ?");
        return $stmt->execute(array($token, $expires, $userId));
    }

    public function findByResetToken($token)
    {
        $stmt = $this->pdo->prepare("
            SELECT id, first_name FROM users
            WHERE reset_token = ? AND reset_token_expires > NOW() AND is_active = 1
        ");
        $stmt->execute(array($token));
        return $stmt->fetch();
    }


    public function clearResetToken($userId)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET reset_token = NULL, reset_token_expires = NULL WHERE id = ?");
        return $stmt->execute(array($userId));
    }

    public function getTopRatedWalkers()
    {
        $stmt = $this->pdo->query("
        SELECT
            u.id,
            u.first_name,
            u.last_name,
            wp.photo,
            wp.description,
            wp.city,
            wp.experience_years,
            wp.favorite_breed,

            COALESCE(ROUND(AVG(r.score),1),0) AS avg_score,
            COUNT(r.id) AS total_ratings

        FROM users u

        JOIN walker_profiles wp
            ON wp.user_id = u.id

        LEFT JOIN ratings r
            ON r.walker_id = u.id

        WHERE u.role = 'walker'
          AND u.is_active = 1
          AND u.is_approved = 1
          AND u.is_banned = 0

        GROUP BY
            u.id,
            u.first_name,
            u.last_name,
            wp.photo,
            wp.description,
            wp.city,
            wp.experience_years,
            wp.favorite_breed

        ORDER BY
            avg_score DESC,
            total_ratings DESC

        LIMIT 5
    ");

        return $stmt->fetchAll();
    }


    public function getMostActiveWalkers()
    {
        $stmt = $this->pdo->query("
        SELECT
            u.id,
            u.first_name,
            u.last_name,
            wp.photo,
            wp.city,
            wp.favorite_breed,

            COUNT(DISTINCT wr.id) AS total_walks,

            COALESCE(
                ROUND(AVG(r.score),1),
                0
            ) AS avg_score

        FROM users u

        JOIN walker_profiles wp
            ON wp.user_id = u.id

        LEFT JOIN walk_requests wr
            ON wr.walker_id = u.id
            AND wr.status = 'completed'

        LEFT JOIN ratings r
            ON r.walker_id = u.id

        WHERE u.role = 'walker'
          AND u.is_active = 1
          AND u.is_approved = 1
          AND u.is_banned = 0

        GROUP BY
            u.id,
            u.first_name,
            u.last_name,
            wp.photo,
            wp.city,
            wp.favorite_breed

        ORDER BY
            total_walks DESC,
            avg_score DESC

        LIMIT 5
    ");

        return $stmt->fetchAll();
    }
}

