<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';
requireAdmin();

$pdo = getPDO();
$recruitersCount = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'recruiter'")->fetchColumn();
$couriersCount = (int) $pdo->query('SELECT COUNT(*) FROM couriers')->fetchColumn();
$newsCount = (int) $pdo->query('SELECT COUNT(*) FROM news')->fetchColumn();
$pendingPayouts = 0;
if (dbHasColumn('payout_requests', 'id')) {
    $pendingPayouts = (int) $pdo->query("SELECT COUNT(*) FROM payout_requests WHERE status = 'pending'")->fetchColumn();
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ панель | Яндекс Еда</title>
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
                <a href="/admin" class="top-nav-link active"><i class="fas fa-gauge"></i>Дашборд</a>
                <a href="/admin/recruiters" class="top-nav-link"><i class="fas fa-users"></i>Рекрутеры</a>
                <a href="/admin/couriers" class="top-nav-link"><i class="fas fa-truck"></i>Курьеры</a>
                <a href="/admin/payouts" class="top-nav-link"><i class="fas fa-wallet"></i>Выплаты</a>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="/info" class="btn btn-outline-light"><i class="fas fa-circle-info"></i>Информация</a>
            <a href="/logout" class="btn btn-warning"><i class="fas fa-right-from-bracket"></i>Выйти</a>
        </div>
    </div>
</nav>

<div class="container modern-container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <h1 class="h3 section-title mb-0">Управление CRM</h1>
        <div class="date-chip"><i class="far fa-calendar"></i><?= date('d.m.Y') ?></div>
    </div>

    <div class="stats-grid admin-stats-grid mb-4">
        <article class="stat-card">
            <div class="stat-icon"><i class="fas fa-user-tie"></i></div>
            <div><div class="stat-label">Рекрутеры</div><div class="stat-value"><?= $recruitersCount ?></div></div>
        </article>
        <article class="stat-card">
            <div class="stat-icon"><i class="fas fa-motorcycle"></i></div>
            <div><div class="stat-label">Курьеры</div><div class="stat-value"><?= $couriersCount ?></div></div>
        </article>
        <article class="stat-card">
            <div class="stat-icon"><i class="fas fa-newspaper"></i></div>
            <div><div class="stat-label">Новости</div><div class="stat-value"><?= $newsCount ?></div></div>
        </article>
        <article class="stat-card">
            <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
            <div><div class="stat-label">Выплаты на проверке</div><div class="stat-value"><?= $pendingPayouts ?></div></div>
        </article>
    </div>

    <div class="quick-actions">
        <div class="quick-actions-title"><i class="fas fa-screwdriver-wrench"></i>Разделы управления</div>
        <div class="actions-grid admin-actions-grid">
            <a class="action-item" href="/admin/recruiters"><span class="action-icon"><i class="fas fa-users"></i></span><span>Рекрутеры</span></a>
            <a class="action-item" href="/admin/couriers"><span class="action-icon"><i class="fas fa-truck"></i></span><span>Курьеры</span></a>
            <a class="action-item" href="/admin/payouts"><span class="action-icon"><i class="fas fa-hand-holding-dollar"></i></span><span>Проверка выплат</span></a>
            <a class="action-item" href="/admin/news_create"><span class="action-icon"><i class="fas fa-pen"></i></span><span>Написать новость</span></a>
            <a class="action-item" href="/admin/news"><span class="action-icon"><i class="fas fa-rectangle-list"></i></span><span>Управление новостями</span></a>
            <a class="action-item" href="/admin/rates"><span class="action-icon"><i class="fas fa-percent"></i></span><span>Ставки</span></a>
            <a class="action-item" href="/info"><span class="action-icon"><i class="fas fa-circle-info"></i></span><span>Информация</span></a>
        </div>
    </div>
</div>
</body>
</html>
