<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';
requireAdmin();

$errors = [];
$title = '';
$content = '';

function processNewsImageUpload(array $file, array &$errors): ?string
{
    if ((int) $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ((int) $file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Ошибка загрузки изображения.';
        return null;
    }

    $tmpName = (string) $file['tmp_name'];
    $size = (int) $file['size'];

    if ($size > 5 * 1024 * 1024) {
        $errors[] = 'Максимальный размер изображения: 5 МБ.';
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = $finfo ? finfo_file($finfo, $tmpName) : '';
    if ($finfo) {
        finfo_close($finfo);
    }

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($allowed[$mime])) {
        $errors[] = 'Допустимые форматы: JPG, PNG, WEBP.';
    }

    if ($errors) {
        return null;
    }

    $fileName = 'news_' . date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $allowed[$mime];
    $destinationDir = __DIR__ . '/../uploads/news';
    if (!is_dir($destinationDir)) {
        mkdir($destinationDir, 0775, true);
    }
    $destination = $destinationDir . '/' . $fileName;

    if (!move_uploaded_file($tmpName, $destination)) {
        $errors[] = 'Не удалось сохранить изображение на сервере.';
        return null;
    }

    return '/uploads/news/' . $fileName;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $imagePath = null;

    if ($title === '' || $content === '') {
        $errors[] = 'Введите заголовок и текст новости.';
    }

    if (isset($_FILES['image'])) {
        $imagePath = processNewsImageUpload($_FILES['image'], $errors);
    }

    if (!$errors) {
        $stmt = getPDO()->prepare('INSERT INTO news (author_id, title, content, image_path) VALUES (:author_id, :title, :content, :image_path)');
        $stmt->execute([
            ':author_id' => currentUserId(),
            ':title' => $title,
            ':content' => $content,
            ':image_path' => $imagePath,
        ]);

        $_SESSION['success'] = 'Новость опубликована.';
        redirect('/admin/news');
    }
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Написать новость</title>
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

<div class="container modern-container py-4" style="max-width: 920px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 section-title mb-0">Написать новость</h1>
        <a href="/admin" class="btn btn-outline-dark">Назад</a>
    </div>

    <?php foreach ($errors as $error): ?>
        <div class="alert alert-danger"><?= h($error) ?></div>
    <?php endforeach; ?>

    <form method="post" enctype="multipart/form-data" class="crm-card p-4">
        <div class="mb-3">
            <label class="form-label">Заголовок</label>
            <input type="text" name="title" class="form-control form-control-lg" value="<?= h($title) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Текст новости</label>
            <textarea name="content" rows="7" class="form-control" required><?= h($content) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Фото (опционально)</label>
            <input type="file" name="image" class="form-control" accept="image/png,image/jpeg,image/webp">
            <div class="form-text">Идеальный размер: 1200x1200px (1:1). Рекомендуется квадрат для ровного отображения. Форматы: JPG, PNG, WEBP. До 5 МБ.</div>
        </div>
        <button class="btn btn-warning btn-lg" type="submit">Опубликовать</button>
    </form>
</div>
</body>
</html>
