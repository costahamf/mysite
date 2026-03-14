<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';
requireAuth();

if (($_SESSION['user']['role'] ?? '') !== 'recruiter') {
    redirect('/admin');
}

$id = (int) ($_GET['id'] ?? 0);
$statuses = ['Приглашен в хаб', 'Принят', 'Отклонен'];

$stmt = getPDO()->prepare('SELECT * FROM couriers WHERE id = :id AND recruiter_id = :recruiter_id LIMIT 1');
$stmt->execute([':id' => $id, ':recruiter_id' => currentUserId()]);
$courier = $stmt->fetch();

if (!$courier) {
    exit('Курьер не найден.');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lastName = trim($_POST['last_name'] ?? '');
    $firstName = trim($_POST['first_name'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $inviteDate = trim($_POST['invite_date'] ?? date('Y-m-d'));
    $ordersCount = (int) ($_POST['orders_count'] ?? 0);
    $reward = (float) ($_POST['reward'] ?? 0);
    $status = trim($_POST['status'] ?? 'Приглашен в хаб');

    if ($lastName === '' || $firstName === '' || $city === '') {
        $errors[] = 'Заполните обязательные поля.';
    }

    if (!in_array($status, $statuses, true)) {
        $errors[] = 'Некорректный статус.';
    }

    if (!$errors) {
        $update = getPDO()->prepare('UPDATE couriers SET last_name = :last_name, first_name = :first_name, city = :city, invite_date = :invite_date, orders_count = :orders_count, reward = :reward, status = :status WHERE id = :id AND recruiter_id = :recruiter_id');
        $update->execute([
            ':last_name' => $lastName,
            ':first_name' => $firstName,
            ':city' => $city,
            ':invite_date' => $inviteDate,
            ':orders_count' => $ordersCount,
            ':reward' => $reward,
            ':status' => $status,
            ':id' => $id,
            ':recruiter_id' => currentUserId(),
        ]);

        redirect('/dashboard');
    }

    $courier = array_merge($courier, [
        'last_name' => $lastName,
        'first_name' => $firstName,
        'city' => $city,
        'invite_date' => $inviteDate,
        'orders_count' => $ordersCount,
        'reward' => $reward,
        'status' => $status,
    ]);
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать курьера</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="container py-4" style="max-width: 760px;">
    <h1 class="h3 section-title mb-3">Редактирование курьера</h1>

    <?php foreach ($errors as $error): ?>
        <div class="alert alert-danger"><?= h($error) ?></div>
    <?php endforeach; ?>

    <form method="post" class="crm-card p-4">
        <div class="row g-3">
            <div class="col-md-6"><input name="last_name" class="form-control form-control-lg" value="<?= h($courier['last_name']) ?>" required></div>
            <div class="col-md-6"><input name="first_name" class="form-control form-control-lg" value="<?= h($courier['first_name']) ?>" required></div>
            <div class="col-md-6"><input name="city" class="form-control form-control-lg" value="<?= h($courier['city']) ?>" required></div>
            <div class="col-md-6"><input type="date" name="invite_date" class="form-control form-control-lg" value="<?= h($courier['invite_date']) ?>"></div>
            <div class="col-md-6"><input type="number" min="0" name="orders_count" class="form-control form-control-lg" value="<?= (int) $courier['orders_count'] ?>"></div>
            <div class="col-md-6"><input type="number" min="0" step="0.01" name="reward" class="form-control form-control-lg" value="<?= h((string) $courier['reward']) ?>"></div>
            <div class="col-12">
                <select name="status" class="form-select form-select-lg">
                    <?php foreach ($statuses as $status): ?>
                        <option value="<?= h($status) ?>" <?= $courier['status'] === $status ? 'selected' : '' ?>><?= h($status) ?></option>
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
