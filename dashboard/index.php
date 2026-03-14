<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';
requireAuth();

if (($_SESSION['user']['role'] ?? '') === 'admin') {
    redirect('/admin');
}

$pdo = getPDO();
$recruiterId = currentUserId();

$statsStmt = $pdo->prepare('SELECT COUNT(*) AS total_couriers, COALESCE(SUM(reward), 0) AS total_reward FROM couriers WHERE recruiter_id = :id');
$statsStmt->execute([':id' => $recruiterId]);
$stats = $statsStmt->fetch() ?: ['total_couriers' => 0, 'total_reward' => 0];

$couriersStmt = $pdo->prepare('SELECT * FROM couriers WHERE recruiter_id = :id ORDER BY created_at DESC');
$couriersStmt->execute([':id' => $recruiterId]);
$couriers = $couriersStmt->fetchAll();

$partnerLink = 'https://reg.eda.yandex.ru/?advertisement_campaign=forms_for_agents&user_invite_code=f570ca2872604481884bbe72291d8ec5&utm_content=blank';
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-warning">
    <div class="container">
        <span class="navbar-brand">CRM рекрутера</span>
        <a href="/logout" class="btn btn-dark">Выйти</a>
    </div>
</nav>
<div class="container py-4">
    <h1 class="h3 section-title mb-3">Здравствуйте, <?= h($_SESSION['user']['name']) ?></h1>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="crm-card p-3 h-100">
                <div class="text-muted">Всего курьеров</div>
                <div class="h2 mb-0"><?= (int) $stats['total_couriers'] ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="crm-card p-3 h-100">
                <div class="text-muted">Общий заработок</div>
                <div class="h2 mb-0"><?= number_format((float) $stats['total_reward'], 0, ',', ' ') ?> ₽</div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="/dashboard/add-courier" class="btn btn-warning btn-lg">Добавить курьера</a>
        <button
            type="button"
            class="btn btn-outline-dark btn-lg"
            data-copy-link="<?= h($partnerLink) ?>"
            data-copy-message="Партнерская ссылка скопирована"
        >
            Партнерская ссылка
        </button>
        <a href="/news" class="btn btn-outline-dark btn-lg">Новости</a>
        <a href="https://t.me/YaEdaRekrut_bot" target="_blank" class="btn btn-outline-dark btn-lg">Поддержка</a>
    </div>

    <div id="copy-feedback" class="alert alert-success d-none" role="alert"></div>

    <div class="table-responsive crm-card p-3">
        <table class="table table-striped align-middle mb-0">
            <thead>
            <tr>
                <th>Фамилия</th>
                <th>Имя</th>
                <th>Город</th>
                <th>Дата приглашения</th>
                <th>Количество заказов</th>
                <th>Вознаграждение</th>
                <th>Статус</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php if (!$couriers): ?>
                <tr><td colspan="8" class="text-center">Пока нет курьеров</td></tr>
            <?php endif; ?>
            <?php foreach ($couriers as $courier): ?>
                <tr>
                    <td><?= h($courier['last_name']) ?></td>
                    <td><?= h($courier['first_name']) ?></td>
                    <td><?= h($courier['city']) ?></td>
                    <td><?= h($courier['invite_date']) ?></td>
                    <td><?= (int) $courier['orders_count'] ?></td>
                    <td><?= number_format((float) $courier['reward'], 0, ',', ' ') ?> ₽</td>
                    <td><span class="badge text-bg-dark badge-status"><?= h($courier['status']) ?></span></td>
                    <td class="text-nowrap">
                        <a href="/dashboard/edit-courier?id=<?= (int) $courier['id'] ?>" class="btn btn-sm btn-outline-dark">Изменить</a>
                        <a href="/dashboard/delete-courier?id=<?= (int) $courier['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить курьера?')">Удалить</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="/assets/js/app.js"></script>
</body>
</html>
