<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';
requireAdmin();

$id = (int) ($_GET['id'] ?? 0);

$stmt = getPDO()->prepare('DELETE FROM news WHERE id = :id');
$stmt->execute([':id' => $id]);

$_SESSION['success'] = 'Новость удалена.';
redirect('/admin/news');
