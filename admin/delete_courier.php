<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';
requireAdmin();

$id = (int) ($_GET['id'] ?? 0);

$stmt = getPDO()->prepare('DELETE FROM couriers WHERE id = :id');
$stmt->execute([':id' => $id]);

redirect('/admin/couriers');
