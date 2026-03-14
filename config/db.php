<?php

declare(strict_types=1);

const DB_HOST = '127.0.0.1';      // Точно как в панели
const DB_PORT = '3308';            // Порт из панели
const DB_NAME = 'costahamf';       // Имя базы
const DB_USER = 'costahamf';       // Логин
const DB_PASS = 'Costa132465';     // Пароль
const DB_CHARSET = 'utf8mb4';

function getPDO(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    // ВАЖНО: используем хост и порт из панели
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        // Временно покажем ошибку
        die('Ошибка подключения к БД: ' . $e->getMessage());
    }
}