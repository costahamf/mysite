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

$newsStmt = $pdo->query('SELECT id, title, content, image_path, created_at FROM news ORDER BY created_at DESC LIMIT 20');
$newsList = $newsStmt->fetchAll();

$partnerLink = 'https://reg.eda.yandex.ru/?advertisement_campaign=forms_for_agents&user_invite_code=f570ca2872604481884bbe72291d8ec5&utm_content=blank';
$unreadNews = getUnreadNewsCount($recruiterId);
$availableBalance = getRecruiterAvailableBalance($recruiterId);
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет рекрутера</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="app-bg">
<nav class="navbar navbar-expand-lg bg-warning shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <img src="/assets/img/logo.png" alt="Логотип" class="app-logo" onerror="this.style.display='none'">
            <span class="navbar-brand mb-0">Личный кабинет рекрутера</span>
        </div>
        <a href="/logout" class="btn btn-dark">Выйти</a>
    </div>
</nav>

<a href="https://t.me/YaEdaRekrut_bot" class="support-floating" target="_blank">Поддержка</a>

<div class="container py-4">
    <h1 class="h3 section-title mb-3">Здравствуйте, <?= h($_SESSION['user']['name']) ?></h1>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="crm-card p-3 h-100 stat-card">
                <div class="text-muted">Всего курьеров</div>
                <div class="h2 mb-0"><?= (int) $stats['total_couriers'] ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="crm-card p-3 h-100 stat-card">
                <div class="text-muted">Общий заработок</div>
                <div class="h2 mb-0"><?= number_format((float) $stats['total_reward'], 0, ',', ' ') ?> ₽</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="crm-card p-3 h-100 stat-card">
                <div class="text-muted">Доступно к выводу</div>
                <div class="h2 mb-0"><?= number_format($availableBalance, 0, ',', ' ') ?> ₽</div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="/dashboard/add-courier" class="btn btn-warning btn-lg">Добавить курьера</a>
        <a href="/dashboard/withdraw" class="btn btn-secondary btn-lg">Вывод средств</a>
        <button
            type="button"
            class="btn btn-outline-dark btn-lg"
            data-copy-link="<?= h($partnerLink) ?>"
            data-copy-message="Партнерская ссылка скопирована"
        >
            Партнерская ссылка
        </button>

        <button class="btn btn-outline-dark btn-lg position-relative" type="button" data-bs-toggle="offcanvas" data-bs-target="#newsOffcanvas" aria-controls="newsOffcanvas">
            🔔 Новости
            <?php if ($unreadNews > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    +<?= $unreadNews > 9 ? '9' : $unreadNews ?>
                </span>
            <?php endif; ?>
        </button>
    </div>

    <div id="copy-feedback" class="alert alert-success d-none" role="alert"></div>

    <div class="table-responsive crm-card p-3 table-clean">
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
            </tr>
            </thead>
            <tbody>
            <?php if (!$couriers): ?>
                <tr><td colspan="7" class="text-center">Пока нет курьеров</td></tr>
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
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="newsOffcanvas" aria-labelledby="newsOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="newsOffcanvasLabel">Новости</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-2">
        <?php if (!$newsList): ?>
            <div class="alert alert-light border">Пока новостей нет.</div>
        <?php else: ?>
            <div class="row g-2 h-100">
                <div class="col-5 border-end pe-2 news-list-panel">
                    <div class="list-group list-group-flush">
                        <?php foreach ($newsList as $i => $item): ?>
                            <button
                                type="button"
                                class="list-group-item list-group-item-action news-select-btn <?= $i === 0 ? 'active' : '' ?>"
                                data-news-title="<?= h($item['title']) ?>"
                                data-news-content="<?= h($item['content']) ?>"
                                data-news-date="<?= h($item['created_at']) ?>"
                                data-news-image="<?= h((string) $item['image_path']) ?>"
                            >
                                <div class="fw-semibold small text-truncate"><?= h($item['title']) ?></div>
                                <div class="text-muted x-small"><?= h($item['created_at']) ?></div>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-7 ps-2">
                    <div id="news-preview" class="news-preview-card">
                        <h6 id="news-preview-title" class="mb-2"><?= h($newsList[0]['title']) ?></h6>
                        <small id="news-preview-date" class="text-muted d-block mb-2"><?= h($newsList[0]['created_at']) ?></small>
                        <?php if (!empty($newsList[0]['image_path'])): ?>
                            <img id="news-preview-image" src="<?= h($newsList[0]['image_path']) ?>" class="img-fluid news-image-compact mb-2" alt="Фото новости">
                        <?php else: ?>
                            <img id="news-preview-image" src="" class="img-fluid news-image-compact mb-2 d-none" alt="Фото новости">
                        <?php endif; ?>
                        <div id="news-preview-content" style="white-space: pre-wrap;"><?= h($newsList[0]['content']) ?></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
