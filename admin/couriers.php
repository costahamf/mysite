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
                    <td class="text-nowrap">
                        <a class="btn btn-sm btn-dark" href="/admin/edit-status?id=<?= (int) $courier['id'] ?>">Изменить</a>
                        <a class="btn btn-sm btn-outline-danger" href="/admin/delete-courier?id=<?= (int) $courier['id'] ?>" onclick="return confirm('Удалить курьера?')">Удалить</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
