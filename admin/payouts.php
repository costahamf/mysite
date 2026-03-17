<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';
requireAdmin();

if (!dbHasColumn('payout_requests', 'id')) {
    exit('Таблица выплат не найдена. Выполните SQL-миграцию для payout_requests.');
}

$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);

$sql = 'SELECT p.*, u.name AS recruiter_name
        FROM payout_requests p
        INNER JOIN users u ON u.id = p.recruiter_id
        ORDER BY FIELD(p.status, "pending", "rejected", "approved"), p.created_at DESC';
$payouts = getPDO()->query($sql)->fetchAll();
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Проверка выплат</title>
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

<div class="container modern-container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 section-title mb-0">Проверка выплат</h1>
        <a href="/admin" class="btn btn-outline-dark">Назад</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= h($success) ?></div>
    <?php endif; ?>

    <div class="table-responsive crm-card p-3">
        <table class="table table-striped align-middle mb-0">
            <thead>
            <tr>
                <th>Рекрутер</th>
                <th>ФИО</th>
                <th>Реквизиты</th>
                <th>Сумма</th>
                <th>Статус</th>
                <th>Комментарий</th>
                <th>Действие</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!$payouts): ?>
                <tr><td colspan="7" class="text-center">Заявок нет</td></tr>
            <?php endif; ?>
            <?php foreach ($payouts as $payout): ?>
                <tr>
                    <td><?= h($payout['recruiter_name']) ?></td>
                    <td><?= h($payout['full_name']) ?></td>
                    <td><?= nl2br(h($payout['requisites'])) ?></td>
                    <td><?= number_format((float) $payout['amount'], 0, ',', ' ') ?> ₽</td>
                    <td>
                        <?php if ($payout['status'] === 'approved'): ?>
                            <span class="badge text-bg-success">Одобрена</span>
                        <?php elseif ($payout['status'] === 'rejected'): ?>
                            <span class="badge text-bg-danger">Отклонена</span>
                        <?php else: ?>
                            <span class="badge text-bg-secondary">На проверке</span>
                        <?php endif; ?>
                    </td>
                    <td><?= h($payout['admin_comment']) ?></td>
                    <td>
                        <?php if ($payout['status'] === 'pending'): ?>
                            <form method="post" action="/admin/payout-action" class="d-grid gap-2" style="min-width:220px;">
                                <input type="hidden" name="id" value="<?= (int) $payout['id'] ?>">
                                <textarea name="comment" class="form-control form-control-sm" rows="2" placeholder="Причина/комментарий" required></textarea>
                                <div class="d-flex gap-2">
                                    <button name="action" value="approve" class="btn btn-sm btn-success">Одобрить</button>
                                    <button name="action" value="reject" class="btn btn-sm btn-danger">Отказать</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
