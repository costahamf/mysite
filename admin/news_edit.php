<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';
requireAdmin();

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

$id = (int) ($_GET['id'] ?? 0);
$stmt = getPDO()->prepare('SELECT * FROM news WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $id]);
$news = $stmt->fetch();

if (!$news) {
    exit('Новость не найдена.');
}

$errors = [];
$title = $news['title'];
$content = $news['content'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $deleteImage = (isset($_POST['delete_image']) && $_POST['delete_image'] === '1');
    $newImagePath = null;

    if ($title === '' || $content === '') {
        $errors[] = 'Введите заголовок и текст новости.';
    }

    if (isset($_FILES['image'])) {
        $newImagePath = processNewsImageUpload($_FILES['image'], $errors);
    }

    if (!$errors) {
        $imagePath = $news['image_path'];

        if ($deleteImage) {
            $imagePath = null;
        }

        if ($newImagePath !== null) {
            $imagePath = $newImagePath;
        }

        $update = getPDO()->prepare('UPDATE news SET title = :title, content = :content, image_path = :image_path WHERE id = :id');
        $update->execute([
            ':title' => $title,
            ':content' => $content,
            ':image_path' => $imagePath,
            ':id' => $id,
        ]);

        $_SESSION['success'] = 'Новость обновлена.';
        redirect('/admin/news');
    }
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Изменить новость</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="app-bg">
<div class="container py-4" style="max-width: 920px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 section-title mb-0">Изменить новость</h1>
        <a href="/admin/news" class="btn btn-outline-dark">Назад</a>
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
        <?php if (!empty($news['image_path'])): ?>
            <div class="mb-3">
                <img src="<?= h($news['image_path']) ?>" alt="Текущее фото" class="img-fluid news-image mb-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="delete_image" name="delete_image">
                    <label class="form-check-label" for="delete_image">Удалить текущее фото</label>
                </div>
            </div>
        <?php endif; ?>
        <div class="mb-3">
            <label class="form-label">Новое фото (опционально)</label>
            <input type="file" name="image" class="form-control" accept="image/png,image/jpeg,image/webp">
        </div>
        <button class="btn btn-warning btn-lg" type="submit">Сохранить изменения</button>
    </form>
</div>
</body>
</html>
