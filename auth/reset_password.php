<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';

if (isLoggedIn()) {
    redirect('/dashboard');
}

$email = trim($_GET['email'] ?? ($_POST['email'] ?? ''));
$code = '';
$errors = [];
$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $code = trim($_POST['code'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Введите корректный email.';
    }

    if ($code === '' || !preg_match('/^\d{6}$/', $code)) {
        $errors[] = 'Введите корректный 6-значный код.';
    }

    if ($password === '' || $passwordConfirm === '') {
        $errors[] = 'Введите новый пароль и подтверждение.';
    }

    if ($password !== $passwordConfirm) {
        $errors[] = 'Пароли не совпадают.';
    }

    if (!$errors) {
        $userStmt = getPDO()->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $userStmt->execute([':email' => $email]);
        $user = $userStmt->fetch();

        if (!$user) {
            $errors[] = 'Пользователь с таким email не найден.';
        } else {
            $codeStmt = getPDO()->prepare('SELECT * FROM password_reset_codes WHERE user_id = :user_id AND used_at IS NULL AND expires_at > NOW() ORDER BY id DESC LIMIT 1');
            $codeStmt->execute([':user_id' => (int) $user['id']]);
            $resetRow = $codeStmt->fetch();

            if (!$resetRow || !password_verify($code, $resetRow['code_hash'])) {
                $errors[] = 'Неверный или просроченный код.';
            } else {
                $updateUser = getPDO()->prepare('UPDATE users SET password = :password WHERE id = :id');
                $updateUser->execute([
                    ':password' => password_hash($password, PASSWORD_DEFAULT),
                    ':id' => (int) $user['id'],
                ]);

                $markUsed = getPDO()->prepare('UPDATE password_reset_codes SET used_at = NOW() WHERE id = :id');
                $markUsed->execute([':id' => (int) $resetRow['id']]);

                $_SESSION['success'] = 'Пароль успешно обновлен. Войдите в аккаунт.';
                redirect('/login');
            }
        }
    }
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сброс пароля</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="app-bg app-modern auth-page">
<div class="container py-5 auth-container">
    <div class="crm-card p-4 auth-card">
        <div class="auth-brand mb-3"><img src="/assets/img/logo.png" alt="Яндекс Еда" class="app-logo" onerror="this.style.display='none'"><span>Яндекс Еда CRM</span></div>
        <h1 class="h4 section-title mb-3">Подтверждение кода</h1>
        <p class="text-muted">Введите код из письма и задайте новый пароль.</p>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= h($success) ?></div>
        <?php endif; ?>

        <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger"><?= h($error) ?></div>
        <?php endforeach; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control form-control-lg" value="<?= h($email) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Код из письма</label>
                <input type="text" name="code" maxlength="6" class="form-control form-control-lg" value="<?= h($code) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Новый пароль</label>
                <input id="password" type="password" name="password" class="form-control form-control-lg" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Подтверждение пароля</label>
                <input id="password_confirm" type="password" name="password_confirm" class="form-control form-control-lg" required>
            </div>
            <button class="btn btn-warning btn-lg w-100" type="submit">Сменить пароль</button>
        </form>
        <p class="mt-3 mb-0"><a href="/forgot-password">Отправить код повторно</a></p>
    </div>
</div>
<script src="/assets/js/app.js"></script>
</body>
</html>
