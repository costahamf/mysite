<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';
requireAdmin();

$errors = [];
$title = '';
$content = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $imagePath = null;

    if ($title === '' || $content === '') {
        $errors[] = 'Введите заголовок и текст новости.';
    }

    if (isset($_FILES['image']) && (int) $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ((int) $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Ошибка загрузки изображения.';
        } else {
            $tmpName = (string) $_FILES['image']['tmp_name'];
            $size = (int) $_FILES['image']['size'];

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

            if (!$errors) {
                $fileName = 'news_' . date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $allowed[$mime];
                $destinationDir = __DIR__ . '/../uploads/news';
                if (!is_dir($destinationDir)) {
                    mkdir($destinationDir, 0775, true);
                }
                $destination = $destinationDir . '/' . $fileName;

                if (!move_uploaded_file($tmpName, $destination)) {
                    $errors[] = 'Не удалось сохранить изображение на сервере.';
                } else {
                    $imagePath = '/uploads/news/' . $fileName;
                }
            }
        }
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
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="container py-4" style="max-width: 920px;">
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
            <div class="form-text">Форматы: JPG, PNG, WEBP. До 5 МБ.</div>
        </div>
        <button class="btn btn-warning btn-lg" type="submit">Опубликовать</button>
    </form>
</div>
</body>
</html>
