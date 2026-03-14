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
    <title>Админ панель</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="app-bg">
<nav class="navbar navbar-expand-lg bg-warning shadow-sm">
    <div class="container">
        <span class="navbar-brand">Админ панель</span>
        <a href="/logout" class="btn btn-dark">Выйти</a>
    </div>
</nav>
<div class="container py-4">
    <h1 class="h3 section-title mb-4">Управление CRM</h1>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="crm-card p-3 stat-card"><div class="text-muted">Рекрутеры</div><div class="h2 mb-0"><?= $recruitersCount ?></div></div></div>
        <div class="col-md-3"><div class="crm-card p-3 stat-card"><div class="text-muted">Курьеры</div><div class="h2 mb-0"><?= $couriersCount ?></div></div></div>
        <div class="col-md-3"><div class="crm-card p-3 stat-card"><div class="text-muted">Новости</div><div class="h2 mb-0"><?= $newsCount ?></div></div></div>
        <div class="col-md-3"><div class="crm-card p-3 stat-card"><div class="text-muted">Выплаты на проверке</div><div class="h2 mb-0"><?= $pendingPayouts ?></div></div></div>
    </div>

    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-warning btn-lg" href="/admin/recruiters">Рекрутеры</a>
        <a class="btn btn-outline-dark btn-lg" href="/admin/couriers">Курьеры</a>
        <a class="btn btn-outline-dark btn-lg" href="/admin/payouts">Проверка выплат</a>
        <a class="btn btn-outline-dark btn-lg" href="/admin/news_create">Написать новость</a>
        <a class="btn btn-outline-dark btn-lg" href="/admin/news">Управление новостями</a>
    </div>
</div>
</body>
</html>
