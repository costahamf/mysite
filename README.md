# CRM для рекрутеров курьеров (PHP + MySQL)

Простая CRM-система для рекрутеров, рассчитанная на обычный shared-хостинг.

## Функционал

- Регистрация/авторизация (`/register`, `/login`).
- Роли: `recruiter`, `admin`.
- Кабинет рекрутера (`/dashboard`):
  - добавление курьеров;
  - таблица только своих курьеров;
  - статистика по заработку;
  - кнопка новостей с индикатором новых;
  - вывод средств (заявка + история).
- Новости (`/news`):
  - читают все авторизованные пользователи;
  - пишет/редактирует/удаляет только админ;
  - с фото, рекомендованный размер изображения 1200x1200.
- Админ-панель (`/admin`):
  - рекрутеры и курьеры;
  - управление новостями;
  - проверка выплат (одобрить/отказать с комментарием).

## Установка на хостинг

1. Создайте базу MySQL.
2. Импортируйте `install.sql`.
3. Загрузите проект в корень сайта (`public_html` или аналог).
4. Настройте `config/db.php` (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`).
5. Включите `mod_rewrite` и проверьте, что `.htaccess` лежит в корне сайта.
6. Убедитесь, что `uploads/news` доступна на запись.

## Миграция для уже установленной версии (без переустановки)

Если CRM уже была установлена ранее, не обязательно пересоздавать БД. Выполните SQL:

```sql
ALTER TABLE users ADD COLUMN last_seen_news_id INT UNSIGNED NOT NULL DEFAULT 0;

CREATE TABLE IF NOT EXISTS payout_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    recruiter_id INT UNSIGNED NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    requisites TEXT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    admin_comment TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at DATETIME NULL,
    CONSTRAINT fk_payout_recruiter FOREIGN KEY (recruiter_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

> Если `last_seen_news_id` уже существует, пропустите `ALTER TABLE`.

## Доступ админа по умолчанию

- Email: `admin@example.com`
- Пароль: `admin12345`

## Структура проекта

- `config/` — подключение БД и инициализация.
- `auth/` — регистрация/вход/выход.
- `dashboard/` — кабинет рекрутера и вывод средств.
- `news/` — страница новостей.
- `admin/` — админ-панель, новости, выплаты.
- `uploads/news/` — изображения новостей.
- `assets/` — стили и JS.
- `install.sql` — схема БД.
