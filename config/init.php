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
