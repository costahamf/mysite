<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';
requireAdmin();

$sql = "SELECT u.id, u.name, u.phone, u.email, u.created_at, COUNT(c.id) AS couriers_count
        FROM users u
        LEFT JOIN couriers c ON c.recruiter_id = u.id
        WHERE u.role = 'recruiter'
        GROUP BY u.id
        ORDER BY u.created_at DESC";

$recruiters = getPDO()->query($sql)->fetchAll();
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Рекрутеры</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="app-bg app-modern">
<nav class="navbar navbar-modern sticky-top">
    <div class="container modern-container d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-4 flex-wrap">
            <a href="/admin" class="navbar-brand-modern text-decoration-none">
                <img src="/assets/img/logo.png" alt="Яндекс Еда" class="app-logo" onerror="this.style.display='none'">
                <span>Админ панель</span>
            </a>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="/admin" class="top-nav-link"><i class="fas fa-gauge"></i>Дашборд</a>
                <a href="/admin/recruiters" class="top-nav-link"><i class="fas fa-users"></i>Рекрутеры</a>
                <a href="/admin/couriers" class="top-nav-link"><i class="fas fa-truck"></i>Курьеры</a>
                <a href="/admin/payouts" class="top-nav-link"><i class="fas fa-wallet"></i>Выплаты</a>
                <a href="/admin/news" class="top-nav-link"><i class="fas fa-newspaper"></i>Новости</a>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="/info" class="btn btn-outline-light">Информация</a>
            <a href="/logout" class="btn btn-warning">Выйти</a>
        </div>
    </div>
</nav>

<div class="container modern-container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 section-title mb-0">Все рекрутеры</h1>
        <a href="/admin" class="btn btn-outline-dark">Назад</a>
    </div>

    <div class="table-responsive crm-card p-3">
        <table class="table table-striped align-middle mb-0">
            <thead>
            <tr>
                <th>ID</th>
                <th>ФИО</th>
                <th>Телефон</th>
                <th>Email</th>
                <th>Дата регистрации</th>
                <th>Количество курьеров</th>
                <th>Действие</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($recruiters as $recruiter): ?>
                <tr>
                    <td><?= (int) $recruiter['id'] ?></td>
                    <td><?= h($recruiter['name']) ?></td>
                    <td><?= h($recruiter['phone']) ?></td>
                    <td><?= h($recruiter['email']) ?></td>
                    <td><?= h($recruiter['created_at']) ?></td>
                    <td><?= (int) $recruiter['couriers_count'] ?></td>
                    <td>
                        <a href="/admin/couriers?recruiter_id=<?= (int) $recruiter['id'] ?>" class="btn btn-sm btn-dark">Показать всех курьеров</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
