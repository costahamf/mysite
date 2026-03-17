<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';
requireAdmin();

$id = (int) ($_GET['id'] ?? 0);
$statuses = ['Приглашен в хаб', 'Принят', 'Отклонен'];

$stmt = getPDO()->prepare('SELECT c.*, u.name AS recruiter_name FROM couriers c INNER JOIN users u ON u.id = c.recruiter_id WHERE c.id = :id LIMIT 1');
$stmt->execute([':id' => $id]);
$courier = $stmt->fetch();

if (!$courier) {
    exit('Курьер не найден');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ordersCount = (int) ($_POST['orders_count'] ?? 0);
    $reward = (float) ($_POST['reward'] ?? 0);
    $status = trim($_POST['status'] ?? '');

    if (!in_array($status, $statuses, true)) {
        $errors[] = 'Некорректный статус.';
    }

    if (!$errors) {
        $update = getPDO()->prepare('UPDATE couriers SET orders_count = :orders_count, reward = :reward, status = :status WHERE id = :id');
        $update->execute([
            ':orders_count' => $ordersCount,
            ':reward' => $reward,
            ':status' => $status,
            ':id' => $id,
        ]);

        redirect('/admin/couriers');
    }

    $courier['orders_count'] = $ordersCount;
    $courier['reward'] = $reward;
    $courier['status'] = $status;
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать курьера</title>
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

<div class="container modern-container py-4" style="max-width: 760px;">
    <h1 class="h3 section-title mb-3">Редактировать курьера (админ)</h1>
    <p class="text-muted"><?= h($courier['last_name'] . ' ' . $courier['first_name']) ?> · Рекрутер: <?= h($courier['recruiter_name']) ?></p>

    <?php foreach ($errors as $error): ?>
        <div class="alert alert-danger"><?= h($error) ?></div>
    <?php endforeach; ?>

    <form method="post" class="crm-card p-4">
        <div class="mb-3">
            <label class="form-label">Количество заказов</label>
            <input type="number" min="0" name="orders_count" class="form-control form-control-lg" value="<?= (int) $courier['orders_count'] ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Вознаграждение</label>
            <input type="number" min="0" step="0.01" name="reward" class="form-control form-control-lg" value="<?= h((string) $courier['reward']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Статус</label>
            <select name="status" class="form-select form-select-lg">
                <?php foreach ($statuses as $status): ?>
                    <option value="<?= h($status) ?>" <?= $courier['status'] === $status ? 'selected' : '' ?>><?= h($status) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-warning btn-lg" type="submit">Сохранить</button>
            <a href="/admin/couriers" class="btn btn-outline-dark btn-lg">Отмена</a>
        </div>
    </form>
</div>
</body>
</html>
