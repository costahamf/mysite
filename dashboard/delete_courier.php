<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';
requireAuth();

http_response_code(403);
exit('Удаление курьеров доступно только администратору.');
