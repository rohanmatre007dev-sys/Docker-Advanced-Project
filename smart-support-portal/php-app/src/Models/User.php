<?php
// php-app/src/Models/User.php
declare(strict_types=1);

class User
{
    public static function findByEmail(PDO $pdo, string $email)
    {
        $stmt = $pdo->prepare('SELECT id, email, password_hash, created_at FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public static function findById(PDO $pdo, int $id)
    {
        $stmt = $pdo->prepare('SELECT id, email, password_hash, created_at FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public static function create(PDO $pdo, string $email, string $passwordHash)
    {
        $stmt = $pdo->prepare('INSERT INTO users (email, password_hash) VALUES (:email, :password_hash) RETURNING id');
        $stmt->execute(['email' => $email, 'password_hash' => $passwordHash]);
        $row = $stmt->fetch();
        return $row['id'] ?? null;
    }
}
