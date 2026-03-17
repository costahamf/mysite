<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';
requireAuth();

if (($_SESSION['user']['role'] ?? '') !== 'recruiter') {
    redirect('/admin');
}

$pdo = getPDO();
$recruiterId = currentUserId();
$errors = [];
$fullName = trim((string) ($_SESSION['user']['name'] ?? ''));
$requisites = '';
$amount = '';

$availableBalance = getRecruiterAvailableBalance($recruiterId);

if (!dbHasColumn('payout_requests', 'id')) {
    exit('Таблица выплат не найдена. Выполните SQL-миграцию для payout_requests.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $requisites = trim($_POST['requisites'] ?? '');
    $amount = trim($_POST['amount'] ?? '');
    $amountFloat = (float) $amount;

    if ($fullName === '' || $requisites === '' || $amount === '') {
        $errors[] = 'Заполните все поля заявки.';
    }

    if ($amountFloat <= 0) {
        $errors[] = 'Сумма должна быть больше 0.';
    }

    if ($amountFloat > $availableBalance) {
        $errors[] = 'Недостаточно средств для вывода.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO payout_requests (recruiter_id, full_name, requisites, amount, status) VALUES (:recruiter_id, :full_name, :requisites, :amount, :status)');
        $stmt->execute([
            ':recruiter_id' => $recruiterId,
            ':full_name' => $fullName,
            ':requisites' => $requisites,
            ':amount' => $amountFloat,
            ':status' => 'pending',
        ]);

        $_SESSION['success'] = 'Ваша заявка на рассмотрении.';
        redirect('/dashboard/withdraw');
    }
}

$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);

$historyStmt = $pdo->prepare('SELECT * FROM payout_requests WHERE recruiter_id = :recruiter_id ORDER BY created_at DESC');
$historyStmt->execute([':recruiter_id' => $recruiterId]);
$history = $historyStmt->fetchAll();
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вывод средств</title>
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

<div class="container modern-container py-4" style="max-width: 980px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 section-title mb-0">Вывод средств</h1>
        <a href="/dashboard" class="btn btn-outline-dark">Назад</a>
    </div>

    <div class="crm-card p-3 mb-3 stat-card">
        <div class="text-muted">Доступно к выводу</div>
        <div class="h3 mb-0"><?= number_format($availableBalance, 0, ',', ' ') ?> ₽</div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= h($success) ?></div>
    <?php endif; ?>

    <?php foreach ($errors as $error): ?>
        <div class="alert alert-danger"><?= h($error) ?></div>
    <?php endforeach; ?>

    <form method="post" class="crm-card p-4 mb-4">
        <h2 class="h5 mb-3">Новая заявка</h2>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">ФИО</label>
                <input name="full_name" class="form-control form-control-lg" value="<?= h($fullName) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Сумма</label>
                <input name="amount" type="number" min="1" step="0.01" class="form-control form-control-lg" value="<?= h($amount) ?>" required>
            </div>
            <div class="col-12">
                <label class="form-label">Реквизиты</label>
                <textarea name="requisites" rows="3" class="form-control" required><?= h($requisites) ?></textarea>
            </div>
        </div>
        <button class="btn btn-warning btn-lg mt-3" type="submit">Отправить заявку</button>
    </form>

    <div class="crm-card p-3">
        <h2 class="h5 mb-3">История выплат</h2>
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                <tr>
                    <th>Дата</th>
                    <th>Сумма</th>
                    <th>Статус</th>
                    <th>Комментарий админа</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!$history): ?>
                    <tr><td colspan="4" class="text-center">Заявок пока нет</td></tr>
                <?php endif; ?>
                <?php foreach ($history as $row): ?>
                    <tr>
                        <td><?= h($row['created_at']) ?></td>
                        <td><?= number_format((float) $row['amount'], 0, ',', ' ') ?> ₽</td>
                        <td>
                            <?php if ($row['status'] === 'approved'): ?>
                                <span class="badge text-bg-success">Одобрена</span>
                            <?php elseif ($row['status'] === 'rejected'): ?>
                                <span class="badge text-bg-danger">Отклонена</span>
                            <?php else: ?>
                                <span class="badge text-bg-secondary">На проверке</span>
                            <?php endif; ?>
                        </td>
                        <td><?= h($row['admin_comment']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
