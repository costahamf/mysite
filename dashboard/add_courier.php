<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';
requireAuth();

if (($_SESSION['user']['role'] ?? '') !== 'recruiter') {
    redirect('/admin');
}

$statuses = ['Приглашен в хаб', 'Принят', 'Отклонен'];
$errors = [];
$data = [
    'last_name' => '',
    'first_name' => '',
    'city' => '',
    'invite_date' => date('Y-m-d'),
    'orders_count' => 0,
    'reward' => 0,
    'status' => 'Приглашен в хаб',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($data as $key => $value) {
        $data[$key] = trim((string) ($_POST[$key] ?? $value));
    }

    if ($data['last_name'] === '' || $data['first_name'] === '' || $data['city'] === '') {
        $errors[] = 'Заполните обязательные поля: фамилия, имя, город.';
    }

    if (!in_array($data['status'], $statuses, true)) {
        $errors[] = 'Некорректный статус.';
    }

    if (!$errors) {
        $stmt = getPDO()->prepare('INSERT INTO couriers (recruiter_id, last_name, first_name, city, invite_date, orders_count, reward, status) VALUES (:recruiter_id, :last_name, :first_name, :city, :invite_date, :orders_count, :reward, :status)');
        $stmt->execute([
            ':recruiter_id' => currentUserId(),
            ':last_name' => $data['last_name'],
            ':first_name' => $data['first_name'],
            ':city' => $data['city'],
            ':invite_date' => $data['invite_date'] ?: date('Y-m-d'),
            ':orders_count' => (int) $data['orders_count'],
            ':reward' => (float) $data['reward'],
            ':status' => $data['status'],
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
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="container py-4" style="max-width: 760px;">
    <h1 class="h3 section-title mb-3">Добавление курьера</h1>

    <?php foreach ($errors as $error): ?>
        <div class="alert alert-danger"><?= h($error) ?></div>
    <?php endforeach; ?>

    <form method="post" class="crm-card p-4">
        <div class="row g-3">
            <div class="col-md-6"><input name="last_name" class="form-control form-control-lg" placeholder="Фамилия" value="<?= h($data['last_name']) ?>" required></div>
            <div class="col-md-6"><input name="first_name" class="form-control form-control-lg" placeholder="Имя" value="<?= h($data['first_name']) ?>" required></div>
            <div class="col-md-6"><input name="city" class="form-control form-control-lg" placeholder="Город" value="<?= h($data['city']) ?>" required></div>
            <div class="col-md-6"><input type="date" name="invite_date" class="form-control form-control-lg" value="<?= h($data['invite_date']) ?>"></div>
            <div class="col-md-6"><input type="number" min="0" name="orders_count" class="form-control form-control-lg" placeholder="Количество заказов" value="<?= h((string) $data['orders_count']) ?>"></div>
            <div class="col-md-6"><input type="number" min="0" step="0.01" name="reward" class="form-control form-control-lg" placeholder="Вознаграждение" value="<?= h((string) $data['reward']) ?>"></div>
            <div class="col-12">
                <select name="status" class="form-select form-select-lg">
                    <?php foreach ($statuses as $status): ?>
                        <option value="<?= h($status) ?>" <?= $data['status'] === $status ? 'selected' : '' ?>><?= h($status) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="d-flex gap-2 mt-4">
            <button class="btn btn-warning btn-lg" type="submit">Сохранить</button>
            <a href="/dashboard" class="btn btn-outline-dark btn-lg">Отмена</a>
        </div>
    </form>
</div>
</body>
</html>
