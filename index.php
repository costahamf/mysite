<?php

declare(strict_types=1);
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Партнёрская программа Яндекс Еды — привлечение курьеров</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="landing-page">
<header class="landing-header">
    <div class="landing-container">
        <nav class="landing-nav">
            <a class="brand" href="#hero" aria-label="На главную">
                <img src="/assets/img/logo.png" alt="Логотип" class="brand-logo">
                <span>Партнёры Еды</span>
            </a>
            <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="landing-menu">☰</button>
            <div class="landing-menu" id="landing-menu">
                <a href="#opportunities">Возможности</a>
                <a href="#benefits">Преимущества</a>
                <a href="#how">Как начать</a>
                <a href="#faq">FAQ</a>
                <a href="/register" class="btn btn-primary">Начать</a>
            </div>
        </nav>
    </div>
</header>

<main>
    <section class="hero" id="hero">
        <div class="landing-container hero-grid">
            <div class="hero-content reveal">
                <p class="tag">Партнёрская программа</p>
                <h1>Зарабатывайте от 0 до 50 000₽+ приглашая курьеров</h1>
                <p class="subtitle">Самозанятость, гибкий график, доход от каждого приглашённого курьера.</p>
                <div class="hero-actions">
                    <a href="/register" class="btn btn-primary">Начать работу</a>
                    <a href="#how" class="btn btn-outline">Узнать больше</a>
                </div>
            </div>
            <div class="hero-visual reveal slide-up">
                <img src="/assets/img/hero.png" alt="Иллюстрация партнёрской программы" width="1200" height="600">
            </div>
        </div>
    </section>

    <section class="section" id="how">
        <div class="landing-container">
            <div class="section-heading reveal">
                <p class="tag">Как это работает</p>
                <h2>4 простых шага до первых выплат</h2>
            </div>
            <div class="steps-grid">
                <article class="card reveal slide-up">
                    <img src="/assets/img/step1.png" alt="Регистрация" width="100" height="100">
                    <h3>1. Регистрация</h3>
                    <p>Создайте аккаунт, заполните профиль и получите личную реферальную ссылку.</p>
                </article>
                <article class="card reveal slide-up">
                    <img src="/assets/img/step2.png" alt="Приглашение курьеров" width="100" height="100">
                    <h3>2. Приглашение</h3>
                    <p>Делитесь ссылкой в соцсетях, чатах и сообществах будущих курьеров.</p>
                </article>
                <article class="card reveal slide-up">
                    <img src="/assets/img/step3.png" alt="Доход от заказов" width="100" height="100">
                    <h3>3. Доход</h3>
                    <p>Получайте вознаграждение за активных приглашённых курьеров и их заказы.</p>
                </article>
                <article class="card reveal slide-up">
                    <img src="/assets/img/step4.png" alt="Выплаты" width="100" height="100">
                    <h3>4. Выплаты</h3>
                    <p>Выводите деньги удобным способом и отслеживайте историю операций в кабинете.</p>
                </article>
            </div>
        </div>
    </section>
    <section class="section section-gray" id="benefits">
        <div class="landing-container">
            <div class="section-heading reveal">
                <p class="tag">Преимущества</p>
                <h2>Почему это удобно и выгодно</h2>
            </div>
            <div class="benefits-grid" id="opportunities">
                <article class="benefit-card reveal">
                    <img src="/assets/img/benefit1.png" alt="Мгновенные выплаты" width="200" height="200">
                    <h3>Мгновенные выплаты</h3>
                    <p>Без долгого ожидания: оформляйте вывод, когда вам удобно.</p>
                </article>
                <article class="benefit-card reveal">
                    <img src="/assets/img/benefit2.png" alt="Прозрачная статистика" width="200" height="200">
                    <h3>Прозрачная статистика</h3>
                    <p>Видите каждого приглашённого, его статус и начисления в реальном времени.</p>
                </article>
                <article class="benefit-card reveal">
                    <img src="/assets/img/benefit3.png" alt="Работа из любой точки" width="200" height="200">
                    <h3>Работа из любой точки</h3>
                    <p>Управляйте приглашениями с телефона или ноутбука — дома, в дороге, где угодно.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="section" id="faq">
        <div class="landing-container">
            <div class="section-heading reveal">
                <p class="tag">FAQ</p>
                <h2>Частые вопросы</h2>
            </div>
            <div class="faq-list">
                <article class="faq-item reveal">
                    <button class="faq-question" type="button">Сколько можно заработать в месяц?</button>
                    <div class="faq-answer"><p>Доход зависит от активности: в среднем от 0 до 50 000₽ и выше при регулярных приглашениях.</p></div>
                </article>
                <article class="faq-item reveal">
                    <button class="faq-question" type="button">Нужен ли опыт в рекрутинге?</button>
                    <div class="faq-answer"><p>Нет, достаточно делиться реферальной ссылкой и отслеживать отклики в личном кабинете.</p></div>
                </article>
                <article class="faq-item reveal">
                    <button class="faq-question" type="button">Когда приходят выплаты?</button>
                    <div class="faq-answer"><p>После подтверждения активности приглашённого курьера средства становятся доступны к выводу.</p></div>
                </article>
                <article class="faq-item reveal">
                    <button class="faq-question" type="button">Можно ли совмещать с основной работой?</button>
                    <div class="faq-answer"><p>Да, вы сами выбираете график и можете заниматься этим в удобное время.</p></div>
                </article>
            </div>
        </div>
    </section>
</main>

<footer class="landing-footer">
    <div class="landing-container footer-grid">
        <div>
            <h3>Контакты</h3>
            <p>Email: support@partner-eda.ru</p>
            <p>Телефон: +7 (900) 000-00-00</p>
        </div>
        <div>
            <h3>Поддержка</h3>
            <p><a href="/info">Центр помощи</a></p>
            <p><a href="/terms">Условия использования</a></p>
            <p><a href="/privacy">Политика конфиденциальности</a></p>
        </div>
        <div>
            <h3>Соцсети</h3>
            <p><a href="#">Telegram</a></p>
            <p><a href="#">VK</a></p>
            <p><a href="#">YouTube</a></p>
        </div>
    </div>
    <div class="landing-container footer-bottom">© <?= date('Y') ?> Партнёрская платформа Яндекс Еды. Все права защищены.</div>
</footer>

<script src="/assets/js/app.js"></script>
</body>
</html>