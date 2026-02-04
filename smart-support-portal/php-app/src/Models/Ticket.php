<?php
// php-app/src/Models/Ticket.php
declare(strict_types=1);

class Ticket
{
    public static function create(PDO $pdo, int $userId, string $title, string $description): int
    {
        $stmt = $pdo->prepare('INSERT INTO tickets (user_id, title, description) VALUES (:user_id, :title, :description) RETURNING id');
        $stmt->execute(['user_id' => $userId, 'title' => $title, 'description' => $description]);
        $row = $stmt->fetch();
        return (int)$row['id'];
    }

    public static function getByUserId(PDO $pdo, int $userId): array
    {
        $stmt = $pdo->prepare('SELECT id, title, description, priority, category, confidence, created_at FROM tickets WHERE user_id = :user_id ORDER BY created_at DESC');
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public static function updateAnalysis(PDO $pdo, int $ticketId, ?string $priority, ?string $category, ?float $confidence): bool
    {
        $stmt = $pdo->prepare('UPDATE tickets SET priority = :priority, category = :category, confidence = :confidence WHERE id = :id');
        return $stmt->execute([
            'priority' => $priority,
            'category' => $category,
            'confidence' => $confidence,
            'id' => $ticketId
        ]);
    }
}
