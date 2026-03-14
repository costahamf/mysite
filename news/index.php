<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';
requireAuth();

$pdo = getPDO();
$stmt = $pdo->query('SELECT n.*, u.name AS author_name FROM news n INNER JOIN users u ON u.id = n.author_id ORDER BY n.created_at DESC');
$newsList = $stmt->fetchAll();

if (dbHasColumn('users', 'last_seen_news_id')) {
    $maxNewsId = (int) $pdo->query('SELECT COALESCE(MAX(id), 0) FROM news')->fetchColumn();
    $update = $pdo->prepare('UPDATE users SET last_seen_news_id = :last_seen_news_id WHERE id = :id');
    $update->execute([
        ':last_seen_news_id' => $maxNewsId,
        ':id' => currentUserId(),
    ]);
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новости</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="app-bg">
<nav class="navbar navbar-expand-lg bg-warning shadow-sm">
    <div class="container">
        <div class="d-flex align-items-center gap-2"><img src="/assets/img/logo.png" alt="Логотип" class="app-logo" onerror="this.style.display='none'"><span class="navbar-brand mb-0">Новости CRM</span></div>
        <div class="d-flex gap-2">
            <?php if (($_SESSION['user']['role'] ?? '') === 'admin'): ?>
                <a href="/admin" class="btn btn-dark">Админ панель</a>
            <?php else: ?>
                <a href="/dashboard" class="btn btn-dark">Личный кабинет</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<div class="container py-4">
    <h1 class="h3 section-title mb-3">Новости</h1>

    <?php if (!$newsList): ?>
        <div class="alert alert-light border">Пока новостей нет.</div>
    <?php endif; ?>

    <div class="row g-3">
        <?php foreach ($newsList as $item): ?>
            <div class="col-12">
                <div class="crm-card p-3">
                    <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
                        <h2 class="h5 mb-0"><?= h($item['title']) ?></h2>
                        <small class="text-muted"><?= h($item['created_at']) ?> · <?= h($item['author_name']) ?></small>
                    </div>
                    <?php if (!empty($item['image_path'])): ?>
                        <img src="<?= h($item['image_path']) ?>" alt="Фото к новости" class="img-fluid news-image mb-3">
                    <?php endif; ?>
                    <div style="white-space: pre-wrap;"><?= h($item['content']) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
