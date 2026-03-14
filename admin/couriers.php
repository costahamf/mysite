<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';
requireAdmin();

$sql = "SELECT c.*, u.name AS recruiter_name
        FROM couriers c
        INNER JOIN users u ON u.id = c.recruiter_id
        ORDER BY c.created_at DESC";
$couriers = getPDO()->query($sql)->fetchAll();
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
        <h1 class="h3 section-title mb-0">Все курьеры</h1>
        <a href="/admin" class="btn btn-outline-dark">Назад</a>
    </div>

    <div class="table-responsive crm-card p-3">
        <table class="table table-striped align-middle mb-0">
            <thead>
            <tr>
                <th>Фамилия</th><th>Имя</th><th>Город</th><th>Рекрутер</th><th>Дата приглашения</th><th>Заказы</th><th>Вознаграждение</th><th>Статус</th><th>Действия</th>
            </tr>
            </thead>
            <tbody>
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
