<?php

declare(strict_types=1);

require_once __DIR__ . '/config/init.php';
requireAuth();

$stmt = getPDO()->query('SELECT * FROM rates ORDER BY city, registered_from DESC');
$rates = $stmt->fetchAll();
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ставки</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="app-bg">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 section-title mb-0">Ставки</h1>
        <div class="d-flex gap-2">
            <a href="/info" class="btn btn-outline-dark">Информация</a>
            <?php if (($_SESSION['user']['role'] ?? '') === 'admin'): ?>
                <a href="/admin/rates" class="btn btn-warning">Управление ставками</a>
                <a href="/admin" class="btn btn-dark">Админ</a>
            <?php else: ?>
                <a href="/dashboard" class="btn btn-dark">Кабинет</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="table-responsive crm-card p-3">
        <table class="table table-striped align-middle mb-0">
            <thead>
            <tr>
                <th>Город</th>
                <th>Тип лида</th>
                <th>Зарегистрирован с</th>
                <th>Зарегистрирован до</th>
                <th>ЦД</th>
                <th>Ставка за заказ (сверх ЦД)</th>
                <th>Макс. доход за 1 курьера</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!$rates): ?>
                <tr><td colspan="7" class="text-center">Ставки пока не заполнены</td></tr>
            <?php endif; ?>
            <?php foreach ($rates as $row): ?>
                <tr>
                    <td><?= h($row['city']) ?></td>
                    <td><?= h($row['lead_type']) ?></td>
                    <td><?= h($row['registered_from']) ?></td>
                    <td><?= h($row['registered_to']) ?></td>
                    <td><?= number_format((float)$row['cd_threshold'], 0, ',', ' ') ?></td>
                    <td><?= number_format((float)$row['order_rate_over_cd'], 2, '.', ' ') ?></td>
                    <td><?= number_format((float)$row['max_income_per_courier'], 2, '.', ' ') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
