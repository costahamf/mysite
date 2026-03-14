<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';
requireAuth();

if (($_SESSION['user']['role'] ?? '') !== 'recruiter') {
    redirect('/admin');
}

$id = (int) ($_GET['id'] ?? 0);

$stmt = getPDO()->prepare('DELETE FROM couriers WHERE id = :id AND recruiter_id = :recruiter_id');
$stmt->execute([
    ':id' => $id,
    ':recruiter_id' => currentUserId(),
]);

redirect('/dashboard');
