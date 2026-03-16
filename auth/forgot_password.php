<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';

if (isLoggedIn()) {
    redirect('/dashboard');
}

$email = '';
$errors = [];
$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Введите корректный email.';
    }

    if (!$errors) {
        $stmt = getPDO()->prepare('SELECT id, name, email FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            $code = (string) random_int(100000, 999999);
            $codeHash = password_hash($code, PASSWORD_DEFAULT);

            $insert = getPDO()->prepare('INSERT INTO password_reset_codes (user_id, code_hash, expires_at) VALUES (:user_id, :code_hash, DATE_ADD(NOW(), INTERVAL 15 MINUTE))');
            $insert->execute([
                ':user_id' => (int) $user['id'],
                ':code_hash' => $codeHash,
            ]);

            $message = "Здравствуйте, {$user['name']}!\n\n";
            $message .= "Код для сброса пароля: {$code}\n";
            $message .= "Код действует 15 минут.\n\n";
            $message .= "Сброс пароля: " . APP_URL . "/reset-password\n\n";
            $message .= "Если вы не запрашивали код, просто проигнорируйте письмо.";

            if (!sendSystemEmail((string) $user['email'], 'Код для сброса пароля', $message)) {
                $errors[] = 'Не удалось отправить письмо. Проверьте настройки почты на хостинге.';
            }
        }

        if (!$errors) {
            $_SESSION['success'] = 'Если email найден в системе, код отправлен на почту.';
            redirect('/reset-password?email=' . urlencode($email));
        }
    }
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Восстановление пароля</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="app-bg">
<div class="container py-5" style="max-width: 640px;">
    <div class="crm-card p-4">
        <h1 class="h4 section-title mb-3">Забыли пароль?</h1>
        <p class="text-muted">Введите email. Мы отправим код для подтверждения.</p>

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
            <button class="btn btn-warning btn-lg w-100" type="submit">Отправить код</button>
        </form>
        <p class="mt-3 mb-0"><a href="/login">Вернуться ко входу</a></p>
    </div>
</div>
</body>
</html>
