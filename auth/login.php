<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';

if (isLoggedIn()) {
    redirect('/dashboard');
}

$errors = [];
$login = '';
$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($login === '' || $password === '') {
        $errors[] = 'Введите логин и пароль.';
    } else {
        $email = filter_var($login, FILTER_VALIDATE_EMAIL) ? $login : null;
        $phone = $email === null ? $login : null;

        $stmt = getPDO()->prepare('SELECT * FROM users WHERE phone = :phone OR email = :email LIMIT 1');
        $stmt->execute([
            ':phone' => $phone,
            ':email' => $email,
        ]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            $errors[] = 'Неверные учетные данные.';
        } else {
            $_SESSION['user'] = [
                'id' => (int) $user['id'],
                'name' => $user['name'],
                'role' => $user['role'],
            ];

            if ($user['role'] === 'admin') {
                redirect('/admin');
            }

            redirect('/dashboard');
        }
    }
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="container py-5" style="max-width: 640px;">
    <div class="crm-card p-4">
        <h1 class="h3 section-title mb-4">Вход в CRM</h1>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= h($success) ?></div>
        <?php endif; ?>

        <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger"><?= h($error) ?></div>
        <?php endforeach; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label">Телефон или email</label>
                <input type="text" name="login" class="form-control form-control-lg" value="<?= h($login) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Пароль</label>
                <input type="password" name="password" class="form-control form-control-lg" required>
            </div>
            <button class="btn btn-warning btn-lg w-100" type="submit">Войти</button>
        </form>
        <p class="mt-3 mb-1">Нет аккаунта? <a href="/register">Зарегистрироваться</a></p>
        <p class="mb-0"><a href="/forgot-password">Забыли пароль?</a></p>
    </div>
</div>
</body>
</html>
