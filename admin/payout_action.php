<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';
requireAdmin();

if (!dbHasColumn('payout_requests', 'id')) {
    exit('Таблица выплат не найдена. Выполните SQL-миграцию для payout_requests.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/payouts');
}

$id = (int) ($_POST['id'] ?? 0);
$action = trim($_POST['action'] ?? '');
$comment = trim($_POST['comment'] ?? '');

if ($id <= 0 || !in_array($action, ['approve', 'reject'], true) || $comment === '') {
    $_SESSION['success'] = 'Некорректные данные действия.';
    redirect('/admin/payouts');
}

$pdo = getPDO();
$stmt = $pdo->prepare("SELECT * FROM payout_requests WHERE id = :id AND status = 'pending' LIMIT 1");
$stmt->execute([':id' => $id]);
$request = $stmt->fetch();

if (!$request) {
    $_SESSION['success'] = 'Заявка уже обработана или не найдена.';
    redirect('/admin/payouts');
}

if ($action === 'approve') {
    $available = getRecruiterAvailableBalance((int) $request['recruiter_id']);
    if ((float) $request['amount'] > $available) {
        $_SESSION['success'] = 'Одобрение невозможно: недостаточно средств у рекрутера.';
        redirect('/admin/payouts');
    }

    $update = $pdo->prepare("UPDATE payout_requests SET status = 'approved', admin_comment = :comment, processed_at = NOW() WHERE id = :id");
    $update->execute([
        ':comment' => $comment,
        ':id' => $id,
    ]);

    $_SESSION['success'] = 'Заявка одобрена.';
    redirect('/admin/payouts');
}

$update = $pdo->prepare("UPDATE payout_requests SET status = 'rejected', admin_comment = :comment, processed_at = NOW() WHERE id = :id");
$update->execute([
    ':comment' => $comment,
    ':id' => $id,
]);

$_SESSION['success'] = 'Заявка отклонена.';
redirect('/admin/payouts');
