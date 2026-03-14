# CRM для рекрутеров курьеров (PHP + MySQL)

Простая CRM-система для рекрутеров, рассчитанная на обычный shared-хостинг.

## Функционал

- Регистрация/авторизация (`/register`, `/login`).
- Восстановление пароля по email-коду (`/forgot-password`, `/reset-password`).
- Роли: `recruiter`, `admin`.
- Кабинет рекрутера (`/dashboard`):
  - добавление курьеров;
  - таблица только своих курьеров;
  - статистика по заработку;
  - кнопка новостей с индикатором новых;
  - вывод средств (заявка + история).
- Новости:
  - в кабинете рекрутера новости открываются справа в боковой панели (offcanvas) через кнопку-колокольчик;
  - пишет/редактирует/удаляет только админ;
  - изображения новостей показываются в компактном виде (не на весь экран);
  - рекомендованный размер изображения: `1200x1200`.
- Админ-панель (`/admin`):
  - рекрутеры и курьеры;
  - управление новостями;
  - проверка выплат (одобрить/отказать с комментарием).

## Логотип

1. Подготовьте логотип и загрузите файл в: `assets/img/logo.png`.
2. Рекомендуемый размер: `512x512` или `1024x1024`, PNG с прозрачным фоном.

Промт для генерации логотипа:

```text
Создай минималистичный логотип для CRM сервиса рекрутинга курьеров.
Стиль: современный, чистый, контрастный, без мелких деталей.
Цвета: #FFD600 (желтый), #111111 (черный), #FFFFFF (белый), допускается нейтральный серый.
Символика: курьерская тематика + CRM/управление (иконка чек-листа, маршрута или карточек).
Форма: квадратная композиция, хорошо читаемая в маленьком размере.
Фон: прозрачный.
Формат: PNG, 1024x1024.
```

## Настройка почты для восстановления пароля

Восстановление пароля использует отправку кода через PHP `mail()`.

1. В `config/init.php` проверьте и укажите:
   - `APP_URL` — адрес вашего сайта;
   - `MAIL_FROM` — адрес отправителя (например, `no-reply@partner-yaedalavka.ru`);
   - `MAIL_FROM_NAME` — имя отправителя.
2. Создайте почтовый ящик `no-reply@вашдомен` в панели хостинга.
3. На хостинге убедитесь, что функция `mail()` работает для вашего домена.
4. Проверьте, что домен отправителя совпадает с вашим сайтом (это уменьшает попадание в спам).
5. Если письма не приходят:
   - проверьте папку «Спам»;
   - проверьте логи почты в панели хостинга;
   - при необходимости используйте SMTP-библиотеку (PHPMailer) вместо `mail()`.

### Логика восстановления пароля

- Пользователь вводит email на `/forgot-password`.
- Система генерирует 6-значный код и отправляет письмо.
- Код хранится в таблице `password_reset_codes` в виде хеша, срок действия — 15 минут.
- На `/reset-password` пользователь вводит email, код и новый пароль.
- После успешной проверки код помечается использованным, пароль обновляется.

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
ALTER TABLE users ADD COLUMN accepted_terms_at DATETIME NULL;
ALTER TABLE users ADD COLUMN accepted_privacy_at DATETIME NULL;
ALTER TABLE users ADD COLUMN last_seen_news_id INT UNSIGNED NOT NULL DEFAULT 0;

CREATE TABLE IF NOT EXISTS password_reset_codes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    code_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reset_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

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

> Если часть колонок/таблиц уже существует, повторно не создавайте их.

## Доступ админа по умолчанию

- Email: `admin@example.com`
- Пароль: `admin12345`

## Структура проекта

- `config/` — подключение БД и инициализация.
- `auth/` — регистрация/вход/выход/восстановление пароля.
- `dashboard/` — кабинет рекрутера и вывод средств.
- `news/` — страница новостей.
- `admin/` — админ-панель, новости, выплаты.
- `uploads/news/` — изображения новостей.
- `assets/` — стили, JS и логотип.
- `install.sql` — схема БД.
