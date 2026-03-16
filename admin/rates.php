<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';
requireAdmin();

$errors = [];
$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);
$bulkInput = '';

/**
 * @return array<int, array<string, string|float>>
 */
function parseRatesBulkInput(string $input): array
{
    $normalized = str_replace(["\r\n", "\r"], "\n", trim($input));
    if ($normalized === '') {
        return [];
    }

    $lines = array_values(array_filter(array_map('trim', explode("\n", $normalized)), function($v) {
        return $v !== '';
    }));

    $rows = [];

    // Mode A: each row in one line with separators (tab/;|)
    foreach ($lines as $line) {
        if (preg_match('/[\t;|]/', $line)) {
            $parts = preg_split('/[\t;|]+/', $line) ?: [];
            $parts = array_values(array_map('trim', $parts));
            if (count($parts) >= 8) {
                $rows[] = array_slice($parts, 0, 8);
            }
        }
    }

    if ($rows) {
        return array_map(function(array $parts): array {
            return [
                'city' => $parts[0],
                'lead_type' => $parts[1],
                'registered_from' => $parts[2],
                'registered_to' => $parts[3],
                'min_orders_cd' => (float) str_replace(',', '.', $parts[4]),
                'cd_threshold' => (float) str_replace(',', '.', $parts[5]),
                'order_rate_over_cd' => (float) str_replace(',', '.', $parts[6]),
                'max_income_per_courier' => (float) str_replace(',', '.', $parts[7]),
            ];
        }, $rows);
    }

    // Mode B: vertical block, every 8 lines = one rate row
    $chunks = array_chunk($lines, 8);
    $result = [];

    foreach ($chunks as $chunk) {
        if (count($chunk) < 8) {
            continue;
        }

        $result[] = [
            'city' => $chunk[0],
            'lead_type' => $chunk[1],
            'registered_from' => $chunk[2],
            'registered_to' => $chunk[3],
            'min_orders_cd' => (float) str_replace(',', '.', $chunk[4]),
            'cd_threshold' => (float) str_replace(',', '.', $chunk[5]),
            'order_rate_over_cd' => (float) str_replace(',', '.', $chunk[6]),
            'max_income_per_courier' => (float) str_replace(',', '.', $chunk[7]),
        ];
    }

    return $result;
}

function normalizeDateRuToSql(string $date): string
{
    $date = trim($date);
    if (preg_match('/^(\d{2})\.(\d{2})\.(\d{4})$/', $date, $m)) {
        return $m[3] . '-' . $m[2] . '-' . $m[1];
    }
    return $date;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bulkInput = trim($_POST['bulk_input'] ?? '');
    $replaceAll = isset($_POST['replace_all']) && $_POST['replace_all'] === '1';

    $parsedRows = parseRatesBulkInput($bulkInput);

    if (!$parsedRows) {
        $errors[] = 'Не удалось распознать данные. Вставьте строки через таб/; или блоками по 8 строк на ставку.';
    }

    foreach ($parsedRows as $i => $row) {
        if (
            trim((string) $row['city']) === '' ||
            trim((string) $row['lead_type']) === '' ||
            trim((string) $row['registered_from']) === '' ||
            trim((string) $row['registered_to']) === ''
        ) {
            $errors[] = 'Пустые обязательные поля в строке #' . ($i + 1);
        }
    }

    if (!$errors) {
        $pdo = getPDO();
        $pdo->beginTransaction();

        try {
            if ($replaceAll) {
                $pdo->exec('TRUNCATE TABLE rates');
            }

            $insert = $pdo->prepare('INSERT INTO rates (city, lead_type, registered_from, registered_to, min_orders_cd, cd_threshold, order_rate_over_cd, max_income_per_courier) VALUES (:city, :lead_type, :registered_from, :registered_to, :min_orders_cd, :cd_threshold, :order_rate_over_cd, :max_income_per_courier)');

            foreach ($parsedRows as $row) {
                $insert->execute([
                    ':city' => trim((string) $row['city']),
                    ':lead_type' => trim((string) $row['lead_type']),
                    ':registered_from' => normalizeDateRuToSql((string) $row['registered_from']),
                    ':registered_to' => normalizeDateRuToSql((string) $row['registered_to']),
                    ':min_orders_cd' => (float) $row['min_orders_cd'],
                    ':cd_threshold' => (float) $row['cd_threshold'],
                    ':order_rate_over_cd' => (float) $row['order_rate_over_cd'],
                    ':max_income_per_courier' => (float) $row['max_income_per_courier'],
                ]);
            }

            $pdo->commit();
            $_SESSION['success'] = 'Ставки успешно сохранены: ' . count($parsedRows);
            redirect('/admin/rates');
        } catch (Throwable $e) {
            $pdo->rollBack();
            $errors[] = 'Ошибка сохранения: ' . $e->getMessage();
        }
    }
}

$rates = getPDO()->query('SELECT * FROM rates ORDER BY city, registered_from DESC')->fetchAll();
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление ставками</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="app-bg">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 section-title mb-0">Управление ставками</h1>
        <div class="d-flex gap-2">
            <a href="/rates" class="btn btn-outline-dark">Публичная таблица</a>
            <a href="/admin" class="btn btn-dark">Назад</a>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= h($success) ?></div>
    <?php endif; ?>

    <?php foreach ($errors as $error): ?>
        <div class="alert alert-danger"><?= h($error) ?></div>
    <?php endforeach; ?>

    <div class="crm-card p-4 mb-3">
        <h2 class="h5 mb-2">Быстрый импорт</h2>
        <p class="text-muted mb-2">Вставьте данные и нажмите сохранить. Поддержка двух форматов:</p>
        <ul class="text-muted small">
            <li>Строка на ставку (разделители: TAB / ; / |)</li>
            <li>Вертикальный блок по 8 строк на ставку (как в вашем примере)</li>
        </ul>

        <form method="post">
            <div class="mb-2">
                <textarea name="bulk_input" rows="11" class="form-control" placeholder="Елабуга
Курьер
16.03.2026
29.03.2026
1
1000
200.00
26000.00"><?= h($bulkInput) ?></textarea>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" value="1" id="replace_all" name="replace_all">
                <label class="form-check-label" for="replace_all">Очистить старые ставки перед загрузкой</label>
            </div>
            <button class="btn btn-warning" type="submit">Сохранить ставки</button>
        </form>
    </div>

    <div class="table-responsive crm-card p-3">
        <table class="table table-striped align-middle mb-0">
            <thead>
            <tr>
                <th>Город</th>
                <th>Тип лида</th>
                <th>С</th>
                <th>До</th>
                <th>Мин. заказы (ЦД)</th>
                <th>ЦД</th>
                <th>Ставка сверх ЦД</th>
                <th>Макс. доход</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!$rates): ?>
                <tr><td colspan="8" class="text-center">Ставки не заполнены</td></tr>
            <?php endif; ?>
            <?php foreach ($rates as $row): ?>
                <tr>
                    <td><?= h($row['city']) ?></td>
                    <td><?= h($row['lead_type']) ?></td>
                    <td><?= h($row['registered_from']) ?></td>
                    <td><?= h($row['registered_to']) ?></td>
                    <td><?= number_format((float)$row['min_orders_cd'], 0, ',', ' ') ?></td>
                    <td><?= number_format((float)$row['cd_threshold'], 0, ',', ' ') ?></td>
                    <td><?= number_format((float)$row['order_rate_over_cd'], 2, '.', ' ') ?></td>
                    <td><?= number_format((float)$row['max_income_per_courier'], 2, '.', ' ') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>