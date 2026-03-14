<?php

declare(strict_types=1);

require_once __DIR__ . '/config/init.php';
requireAuth();
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Информация</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="app-bg">
<div class="container py-4" style="max-width: 980px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 section-title mb-0">Информация</h1>
        <div class="d-flex gap-2">
            <a href="/rates" class="btn btn-outline-dark">Ставки</a>
            <?php if (($_SESSION['user']['role'] ?? '') === 'admin'): ?>
                <a href="/admin" class="btn btn-dark">Админ</a>
            <?php else: ?>
                <a href="/dashboard" class="btn btn-dark">Кабинет</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="crm-card p-4">
        <h2 class="h5 mb-2">Раздел в разработке</h2>
        <p class="text-muted mb-0">Эту страницу можно заполнить позже: правила, инструкции, ответы на вопросы, полезные контакты, регламенты и т.д.</p>
    </div>
</div>
</body>
</html>
