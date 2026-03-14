<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/db.php';

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user']) && is_array($_SESSION['user']);
}

function requireAuth(): void
{
    if (!isLoggedIn()) {
        redirect('/login');
    }
}

function requireAdmin(): void
{
    requireAuth();

    if (($_SESSION['user']['role'] ?? '') !== 'admin') {
        http_response_code(403);
        exit('Доступ запрещен. Только для администратора.');
    }
}

function currentUserId(): int
{
    return (int) ($_SESSION['user']['id'] ?? 0);
}

function dbHasColumn(string $table, string $column): bool
{
    static $cache = [];
    $key = $table . '.' . $column;

    if (isset($cache[$key])) {
        return $cache[$key];
    }

    $stmt = getPDO()->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = :schema AND TABLE_NAME = :table_name AND COLUMN_NAME = :column_name');
    $stmt->execute([
        ':schema' => DB_NAME,
        ':table_name' => $table,
        ':column_name' => $column,
    ]);

    $cache[$key] = ((int) $stmt->fetchColumn()) > 0;

    return $cache[$key];
}

function getRecruiterAvailableBalance(int $recruiterId): float
{
    $pdo = getPDO();

    $rewardStmt = $pdo->prepare('SELECT COALESCE(SUM(reward), 0) FROM couriers WHERE recruiter_id = :id');
    $rewardStmt->execute([':id' => $recruiterId]);
    $totalReward = (float) $rewardStmt->fetchColumn();

    if (!dbHasColumn('payout_requests', 'amount')) {
        return $totalReward;
    }

    $paidStmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM payout_requests WHERE recruiter_id = :id AND status = 'approved'");
    $paidStmt->execute([':id' => $recruiterId]);
    $alreadyPaid = (float) $paidStmt->fetchColumn();

    return max(0, $totalReward - $alreadyPaid);
}

function getUnreadNewsCount(int $userId): int
{
    $pdo = getPDO();
    $maxNewsId = (int) $pdo->query('SELECT COALESCE(MAX(id), 0) FROM news')->fetchColumn();

    if ($maxNewsId === 0) {
        return 0;
    }

    if (!dbHasColumn('users', 'last_seen_news_id')) {
        return 0;
    }

    $stmt = $pdo->prepare('SELECT COALESCE(last_seen_news_id, 0) FROM users WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $userId]);
    $lastSeen = (int) $stmt->fetchColumn();

    return max(0, $maxNewsId - $lastSeen);
}
