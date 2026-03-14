<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';
requireAdmin();

$recruiterId = (int) ($_GET['recruiter_id'] ?? 0);
$recruiterName = '';

if ($recruiterId > 0) {
    $recruiterStmt = getPDO()->prepare("SELECT name FROM users WHERE id = :id AND role = 'recruiter' LIMIT 1");
    $recruiterStmt->execute([':id' => $recruiterId]);
    $recruiter = $recruiterStmt->fetch();

    if ($recruiter) {
        $recruiterName = (string) $recruiter['name'];
    } else {
        $recruiterId = 0;
    }
}

if ($recruiterId > 0) {
    $stmt = getPDO()->prepare('SELECT c.*, u.name AS recruiter_name FROM couriers c INNER JOIN users u ON u.id = c.recruiter_id WHERE c.recruiter_id = :recruiter_id ORDER BY c.created_at DESC');
    $stmt->execute([':recruiter_id' => $recruiterId]);
    $couriers = $stmt->fetchAll();
} else {
    $stmt = getPDO()->query('SELECT c.*, u.name AS recruiter_name FROM couriers c INNER JOIN users u ON u.id = c.recruiter_id ORDER BY c.created_at DESC');
    $couriers = $stmt->fetchAll();
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Курьеры</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 section-title mb-0">Все курьеры</h1>
            <?php if ($recruiterId > 0): ?>
                <p class="text-muted mb-0">Фильтр по рекрутеру: <?= h($recruiterName) ?></p>
            <?php endif; ?>
        </div>
        <div class="d-flex gap-2">
            <?php if ($recruiterId > 0): ?>
                <a href="/admin/couriers" class="btn btn-outline-secondary">Сбросить фильтр</a>
            <?php endif; ?>
            <a href="/admin" class="btn btn-outline-dark">Назад</a>
        </div>
    </div>

    <div class="table-responsive crm-card p-3">
        <table class="table table-striped align-middle mb-0">
            <thead>
            <tr>
                <th>Фамилия</th><th>Имя</th><th>Город</th><th>Рекрутер</th><th>Дата приглашения</th><th>Заказы</th><th>Вознаграждение</th><th>Статус</th><th>Действия</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!$couriers): ?>
                <tr><td colspan="9" class="text-center">Курьеры не найдены</td></tr>
            <?php endif; ?>
            <?php foreach ($couriers as $courier): ?>
                <tr>
                    <td><?= h($courier['last_name']) ?></td>
                    <td><?= h($courier['first_name']) ?></td>
                    <td><?= h($courier['city']) ?></td>
                    <td><?= h($courier['recruiter_name']) ?></td>
                    <td><?= h($courier['invite_date']) ?></td>
                    <td><?= (int) $courier['orders_count'] ?></td>
                    <td><?= number_format((float) $courier['reward'], 0, ',', ' ') ?> ₽</td>
                    <td><?= h($courier['status']) ?></td>
                    <td><a class="btn btn-sm btn-dark" href="/admin/edit-status?id=<?= (int) $courier['id'] ?>">Редактировать</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
