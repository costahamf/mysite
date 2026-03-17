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

$monthlyStmt = $pdo->prepare('SELECT COUNT(*) FROM couriers WHERE recruiter_id = :id AND invite_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)');
$monthlyStmt->execute([':id' => $recruiterId]);
$monthlyGrowth = (int) $monthlyStmt->fetchColumn();

$couriersStmt = $pdo->prepare('SELECT * FROM couriers WHERE recruiter_id = :id ORDER BY created_at DESC');
$couriersStmt->execute([':id' => $recruiterId]);
$couriers = $couriersStmt->fetchAll();

$newsStmt = $pdo->query('SELECT id, title, content, image_path, created_at FROM news ORDER BY created_at DESC LIMIT 20');
$newsList = $newsStmt->fetchAll();

$partnerLink = 'https://reg.eda.yandex.ru/?advertisement_campaign=forms_for_agents&user_invite_code=f570ca2872604481884bbe72291d8ec5&utm_content=blank';
$unreadNews = getUnreadNewsCount($recruiterId);
$availableBalance = getRecruiterAvailableBalance($recruiterId);
$userName = trim((string) ($_SESSION['user']['name'] ?? 'Партнёр'));
$userInitial = mb_strtoupper(mb_substr($userName, 0, 1));
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет рекрутера | Яндекс Еда</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="app-bg app-modern">
<nav class="navbar navbar-modern sticky-top">
    <div class="container modern-container d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-4 flex-wrap">
            <a href="/dashboard" class="navbar-brand-modern text-decoration-none">
                <img src="/assets/img/logo.png" alt="Яндекс Еда" class="app-logo" onerror="this.style.display='none'">
                <span>Яндекс Еда</span>
            </a>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="/dashboard" class="top-nav-link active"><i class="fas fa-house"></i>Главная</a>
                <a href="/rates" class="top-nav-link"><i class="fas fa-chart-line"></i>Ставки</a>
                <a href="/info" class="top-nav-link"><i class="fas fa-circle-info"></i>Информация</a>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-light position-relative" type="button" data-bs-toggle="offcanvas" data-bs-target="#newsOffcanvas" aria-controls="newsOffcanvas">
                <i class="far fa-bell"></i>
                <?php if ($unreadNews > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
                        <?= $unreadNews > 9 ? '9+' : $unreadNews ?>
                    </span>
                <?php endif; ?>
            </button>
            <div class="dropdown">
                <button class="user-chip border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="user-avatar"><?= h($userInitial) ?></span>
                    <span class="text-white fw-semibold d-none d-md-inline"><?= h($userName) ?></span>
                    <i class="fas fa-chevron-down text-white small"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#">Профиль</a></li>
                    <li><a class="dropdown-item" href="#">Настройки</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="/logout">Выйти</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<a href="https://t.me/YaEdaRekrut_bot" class="support-floating" target="_blank" title="Поддержка">
    <i class="fab fa-telegram"></i>
</a>

<div class="container modern-container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <h1 class="h3 section-title mb-0">Здравствуйте, <?= h($userName) ?>!</h1>
        <div class="date-chip"><i class="far fa-calendar"></i><?= date('d.m.Y') ?></div>
    </div>

    <div class="stats-grid mb-4">
        <article class="stat-card">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div>
                <div class="stat-label">Всего курьеров</div>
                <div class="stat-value"><?= (int) $stats['total_couriers'] ?></div>
                <div class="stat-trend">+<?= $monthlyGrowth ?> за месяц</div>
            </div>
        </article>
        <article class="stat-card">
            <div class="stat-icon"><i class="fas fa-ruble-sign"></i></div>
            <div>
                <div class="stat-label">Общий заработок</div>
                <div class="stat-value"><?= number_format((float) $stats['total_reward'], 0, ',', ' ') ?> ₽</div>
                <div class="stat-trend">С начала сотрудничества</div>
            </div>
        </article>
        <article class="stat-card">
            <div class="stat-icon"><i class="fas fa-wallet"></i></div>
            <div>
                <div class="stat-label">Доступно к выводу</div>
                <div class="stat-value"><?= number_format($availableBalance, 0, ',', ' ') ?> ₽</div>
                <div class="stat-trend">Минимальная сумма 1000 ₽</div>
            </div>
        </article>
    </div>

    <div class="quick-actions mb-4">
        <div class="quick-actions-title"><i class="fas fa-bolt"></i>Быстрые действия</div>
        <div class="actions-grid">
            <a href="/dashboard/add-courier" class="action-item"><span class="action-icon"><i class="fas fa-user-plus"></i></span><span>Добавить курьера</span></a>
            <a href="/dashboard/withdraw" class="action-item"><span class="action-icon"><i class="fas fa-arrow-up"></i></span><span>Вывод средств</span></a>
            <button type="button" class="action-item" data-copy-link="<?= h($partnerLink) ?>" data-copy-message="🔗 Партнерская ссылка скопирована">
                <span class="action-icon"><i class="fas fa-link"></i></span><span>Партнерская ссылка</span>
            </button>
            <a href="/rates" class="action-item"><span class="action-icon"><i class="fas fa-chart-column"></i></span><span>Ставки</span><span class="action-badge">new</span></a>
            <a href="/info" class="action-item"><span class="action-icon"><i class="fas fa-book"></i></span><span>Информация</span></a>
        </div>
    </div>

    <div id="copy-feedback" class="alert alert-success d-none" role="alert"></div>

    <section class="table-section">
        <div class="table-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="h5 mb-0"><i class="fas fa-truck-fast me-2 text-warning"></i>Список курьеров</h2>
            <div class="table-filters">
                <button type="button" class="filter-btn active">Все</button>
                <button type="button" class="filter-btn">Активные</button>
                <button type="button" class="filter-btn">На рассмотрении</button>
                <button type="button" class="filter-btn">Архив</button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                <tr>
                    <th>Фамилия</th>
                    <th>Имя</th>
                    <th>Город</th>
                    <th>Дата приглашения</th>
                    <th>Заказы</th>
                    <th>Вознаграждение</th>
                    <th>Статус</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!$couriers): ?>
                    <tr><td colspan="7" class="text-center py-4 text-muted">Пока нет приглашенных курьеров</td></tr>
                <?php endif; ?>
                <?php foreach ($couriers as $courier): ?>
                    <tr>
                        <td><?= h($courier['last_name']) ?></td>
                        <td><?= h($courier['first_name']) ?></td>
                        <td><?= h($courier['city']) ?></td>
                        <td><?= date('d.m.Y', strtotime((string) $courier['invite_date'])) ?></td>
                        <td class="fw-semibold"><?= (int) $courier['orders_count'] ?></td>
                        <td class="fw-semibold" style="color:#111827;"><?= number_format((float) $courier['reward'], 0, ',', ' ') ?> ₽</td>
                        <td><span class="badge-status"><?= h($courier['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<div class="offcanvas offcanvas-end news-offcanvas" tabindex="-1" id="newsOffcanvas" aria-labelledby="newsOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="newsOffcanvasLabel"><i class="fas fa-newspaper me-2"></i>Новости</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-3">
        <?php if (!$newsList): ?>
            <div class="alert alert-light border">Пока новостей нет.</div>
        <?php else: ?>
            <div class="row g-2 h-100">
                <div class="col-5 news-list-panel">
                    <?php foreach ($newsList as $i => $item): ?>
                        <button
                            type="button"
                            class="news-select-btn news-select-item <?= $i === 0 ? 'active' : '' ?>"
                            data-news-title="<?= h($item['title']) ?>"
                            data-news-content="<?= h($item['content']) ?>"
                            data-news-date="<?= date('d.m.Y', strtotime((string) $item['created_at'])) ?>"
                            data-news-image="<?= h((string) $item['image_path']) ?>"
                        >
                            <div class="fw-semibold text-truncate"><?= h($item['title']) ?></div>
                            <div class="text-muted small"><?= date('d.m.Y', strtotime((string) $item['created_at'])) ?></div>
                        </button>
                    <?php endforeach; ?>
                </div>
                <div class="col-7">
                    <div class="news-preview-card">
                        <h6 id="news-preview-title" class="fw-bold"><?= h($newsList[0]['title']) ?></h6>
                        <small id="news-preview-date" class="text-muted d-block mb-2"><?= date('d.m.Y', strtotime((string) $newsList[0]['created_at'])) ?></small>
                        <?php if (!empty($newsList[0]['image_path'])): ?>
                            <img id="news-preview-image" src="<?= h($newsList[0]['image_path']) ?>" class="img-fluid news-image-compact mb-2" alt="Фото новости">
                        <?php else: ?>
                            <img id="news-preview-image" src="" class="img-fluid news-image-compact mb-2 d-none" alt="Фото новости">
                        <?php endif; ?>
                        <div id="news-preview-content" class="small" style="white-space: pre-line;"><?= h($newsList[0]['content']) ?></div>
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
