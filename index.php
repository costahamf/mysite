<?php

declare(strict_types=1);

require_once __DIR__ . '/config/init.php';

if (!isLoggedIn()) {
    redirect('/login');
}

if (($_SESSION['user']['role'] ?? '') === 'admin') {
    redirect('/admin');
}

redirect('/dashboard');
