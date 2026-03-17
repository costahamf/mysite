<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';
requireAuth();

if (($_SESSION['user']['role'] ?? '') !== 'recruiter') {
    redirect('/admin');
}

$errors = [];
$data = [
    'last_name' => '',
    'first_name' => '',
    'city' => '',
    'invite_date' => date('Y-m-d'),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($data as $key => $value) {
        $data[$key] = trim((string) ($_POST[$key] ?? $value));
    }

    if ($data['last_name'] === '' || $data['first_name'] === '' || $data['city'] === '') {
        $errors[] = 'Заполните обязательные поля: фамилия, имя, город.';
    }

    if (!$errors) {
        $stmt = getPDO()->prepare('INSERT INTO couriers (recruiter_id, last_name, first_name, city, invite_date, orders_count, reward, status) VALUES (:recruiter_id, :last_name, :first_name, :city, :invite_date, :orders_count, :reward, :status)');
        $stmt->execute([
            ':recruiter_id' => currentUserId(),
            ':last_name' => $data['last_name'],
            ':first_name' => $data['first_name'],
            ':city' => $data['city'],
            ':invite_date' => $data['invite_date'] ?: date('Y-m-d'),
            ':orders_count' => 0,
            ':reward' => 0,
            ':status' => 'Приглашен в хаб',
        ]);

        redirect('/dashboard');
    }
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить курьера</title>
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
                <a href="/dashboard" class="top-nav-link"><i class="fas fa-house"></i>Главная</a>
                <a href="/rates" class="top-nav-link"><i class="fas fa-chart-line"></i>Ставки</a>
                <a href="/info" class="top-nav-link"><i class="fas fa-circle-info"></i>Информация</a>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="/logout" class="btn btn-warning">Выйти</a>
        </div>
    </div>
</nav>

<div class="container modern-container py-4" style="max-width: 760px;">
    <h1 class="h3 section-title mb-1">Добавление курьера</h1>
    <p class="text-muted">Статус, количество заказов и вознаграждение устанавливает администратор после проверки.</p>

    <?php foreach ($errors as $error): ?>
        <div class="alert alert-danger"><?= h($error) ?></div>
    <?php endforeach; ?>

    <form method="post" class="crm-card p-4">
        <div class="row g-3">
            <div class="col-md-6"><input name="last_name" class="form-control form-control-lg" placeholder="Фамилия" value="<?= h($data['last_name']) ?>" required></div>
            <div class="col-md-6"><input name="first_name" class="form-control form-control-lg" placeholder="Имя" value="<?= h($data['first_name']) ?>" required></div>
            <div class="col-md-6"><input name="city" class="form-control form-control-lg" placeholder="Город" value="<?= h($data['city']) ?>" required></div>
            <div class="col-md-6"><input type="date" name="invite_date" class="form-control form-control-lg" value="<?= h($data['invite_date']) ?>"></div>
        </div>
        <div class="d-flex gap-2 mt-4">
            <button class="btn btn-warning btn-lg" type="submit">Сохранить</button>
            <a href="/dashboard" class="btn btn-outline-dark btn-lg">Отмена</a>
        </div>
    </form>
</div>
</body>
</html>
