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
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="app-bg">
<div class="container py-4">
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
