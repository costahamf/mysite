<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';

if (isLoggedIn()) {
    redirect('/dashboard');
}

$errors = [];
$name = '';
$phoneOrEmail = '';
$acceptedTerms = false;
$acceptedPrivacy = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phoneOrEmail = trim($_POST['phone_or_email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';
    $acceptedTerms = isset($_POST['accept_terms']) && $_POST['accept_terms'] === '1';
    $acceptedPrivacy = isset($_POST['accept_privacy']) && $_POST['accept_privacy'] === '1';

    if ($name === '' || $phoneOrEmail === '' || $password === '' || $passwordConfirm === '') {
        $errors[] = 'Заполните все поля.';
    }

    if ($password !== $passwordConfirm) {
        $errors[] = 'Пароли не совпадают.';
    }

    if (!$acceptedTerms) {
        $errors[] = 'Необходимо принять условия Пользовательского соглашения.';
    }

    if (!$acceptedPrivacy) {
        $errors[] = 'Необходимо дать согласие на обработку персональных данных.';
    }

    $email = filter_var($phoneOrEmail, FILTER_VALIDATE_EMAIL) ? $phoneOrEmail : null;
    $phone = $email === null ? $phoneOrEmail : null;

    if (!$errors) {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT id FROM users WHERE phone = :phone OR email = :email LIMIT 1');
        $stmt->execute([
            ':phone' => $phone,
            ':email' => $email,
        ]);

        if ($stmt->fetch()) {
            $errors[] = 'Такой телефон или email уже зарегистрирован.';
        }
    }

    if (!$errors) {
        $stmt = getPDO()->prepare(
            'INSERT INTO users (name, phone, email, password, role, accepted_terms_at, accepted_privacy_at) VALUES (:name, :phone, :email, :password, :role, :accepted_terms_at, :accepted_privacy_at)'
        );

        $acceptedAt = date('Y-m-d H:i:s');

        $stmt->execute([
            ':name' => $name,
            ':phone' => $phone,
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_DEFAULT),
            ':role' => 'recruiter',
            ':accepted_terms_at' => $acceptedAt,
            ':accepted_privacy_at' => $acceptedAt,
        ]);

        $_SESSION['success'] = 'Регистрация успешна. Войдите в аккаунт.';
        redirect('/login');
    }
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="app-bg">
<div class="container py-5" style="max-width: 700px;">
    <div class="crm-card p-4">
        <h1 class="h3 section-title mb-4">Регистрация рекрутера</h1>

        <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger"><?= h($error) ?></div>
        <?php endforeach; ?>

        <form method="post" novalidate>
            <div class="mb-3">
                <label class="form-label">ФИО</label>
                <input type="text" name="name" class="form-control form-control-lg" value="<?= h($name) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Телефон или email</label>
                <input type="text" name="phone_or_email" class="form-control form-control-lg" value="<?= h($phoneOrEmail) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Пароль</label>
                <input id="password" type="password" name="password" class="form-control form-control-lg" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Подтверждение пароля</label>
                <input id="password_confirm" type="password" name="password_confirm" class="form-control form-control-lg" required>
            </div>

            <div class="crm-card p-3 mb-3 legal-box">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="1" id="accept_terms" name="accept_terms" <?= $acceptedTerms ? 'checked' : '' ?> required>
                    <label class="form-check-label" for="accept_terms">
                        Я принимаю условия <a href="/terms" target="_blank" rel="noopener" class="legal-link">Пользовательского соглашения</a>
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="accept_privacy" name="accept_privacy" <?= $acceptedPrivacy ? 'checked' : '' ?> required>
                    <label class="form-check-label" for="accept_privacy">
                        Я даю согласие на <a href="/privacy" target="_blank" rel="noopener" class="legal-link">обработку персональных данных</a>
                    </label>
                </div>
            </div>

            <button class="btn btn-warning btn-lg w-100" type="submit">Зарегистрироваться</button>
        </form>
        <p class="mt-3 mb-0">Уже есть аккаунт? <a href="/login">Войти</a></p>
    </div>
</div>
<script src="/assets/js/app.js"></script>
</body>
</html>
