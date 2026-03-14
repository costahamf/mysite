<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';
requireAdmin();

$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);

$stmt = getPDO()->query('SELECT n.*, u.name AS author_name FROM news n INNER JOIN users u ON u.id = n.author_id ORDER BY n.created_at DESC');
$newsList = $stmt->fetchAll();
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление новостями</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="app-bg">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 section-title mb-0">Новости</h1>
        <div class="d-flex gap-2">
            <a href="/admin/news_create" class="btn btn-warning">Написать новость</a>
            <a href="/admin" class="btn btn-outline-dark">Назад</a>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= h($success) ?></div>
    <?php endif; ?>

    <?php if (!$newsList): ?>
        <div class="alert alert-light border">Новостей пока нет.</div>
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
                    <div style="white-space: pre-wrap;" class="mb-3"><?= h($item['content']) ?></div>
                    <div class="d-flex gap-2">
                        <a href="/admin/news_edit?id=<?= (int) $item['id'] ?>" class="btn btn-sm btn-outline-dark">Изменить</a>
                        <a href="/admin/news_delete?id=<?= (int) $item['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить новость?')">Удалить</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
