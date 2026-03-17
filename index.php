<?php

declare(strict_types=1);
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Партнёрская программа Яндекс Еды — привлекайте курьеров и зарабатывайте</title>
    <meta name="description" content="Современная партнёрская платформа для привлечения курьеров Яндекс Еды: прозрачная статистика, гибкий формат и быстрые выплаты.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="landing-page">
<a class="skip-link" href="#main-content">Перейти к содержимому</a>

<header class="landing-header" id="top">
    <div class="landing-shell">
        <nav class="topbar" aria-label="Главная навигация">
            <a class="brand" href="#top" aria-label="На главную">
                <img src="/assets/img/logo.png" alt="Логотип платформы" class="brand-logo" width="40" height="40">
                <span class="brand-text">Партнёры Еды</span>
            </a>

            <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="landing-menu" aria-label="Открыть меню">
                <span></span><span></span><span></span>
            </button>

            <div class="landing-menu" id="landing-menu">
                <a href="#opportunities">Возможности</a>
                <a href="#benefits">Преимущества</a>
                <a href="#how">Как начать</a>
                <a href="#faq">FAQ</a>
            </div>

            <div class="topbar-actions">
                <a href="/login" class="btn btn-ghost">Войти</a>
                <a href="/register" class="btn btn-primary">Начать</a>
            </div>
        </nav>
    </div>
</header>

<main id="main-content">
    <section class="hero" aria-labelledby="hero-title">
        <div class="landing-shell hero-layout">
            <div class="hero-copy reveal">
                <span class="badge">Партнёрская программа</span>
                <h1 id="hero-title">Зарабатывайте до</h1>
                <h1 id="hero-title">50 000₽+</h1>
                <h1 id="hero-title">приглашая курьеров</h1>
                <p class="hero-subtitle">Самозанятость, гибкий график, доход от каждого приглашённого курьера. Всё в одном понятном кабинете.</p>

                <div class="hero-cta">
                    <a href="/register" class="btn btn-primary btn-lg">Начать работу</a>
                    <a href="#opportunities" class="btn btn-outline btn-lg">Узнать больше</a>
                </div>

                <ul class="hero-points" aria-label="Ключевые преимущества">
                    <li>Без привязки к офису</li>
                    <li>Прозрачные условия выплат</li>
                    <li>Поддержка на каждом этапе</li>
                </ul>
            </div>

            <div class="hero-media reveal">
                <img src="/assets/img/hero.png" alt="Иллюстрация партнёрской платформы" width="1200" height="600" onerror="this.style.display='none'">
                <div class="floating-card card-one">
                    <strong>+12 450₽</strong>
                    <span>Начисления за неделю</span>
                </div>
                <div class="floating-card card-two">
                    <strong>24 курьера</strong>
                    <span>В работе сейчас</span>
                </div>
            </div>
        </div>
    </section>

    <section class="metrics reveal" aria-label="Показатели платформы">
        <div class="landing-shell metrics-grid">
            <article><strong>100+</strong><span>регистраций в месяц</span></article>
            <article><strong>24/7</strong><span>поддержка партнёров</span></article>
            <article><strong>до 50 000₽+</strong><span>потенциал дохода</span></article>
            <article><strong>100%</strong><span>прозрачная аналитика</span></article>
        </div>
    </section>

    <section class="section" id="opportunities">
        <div class="landing-shell">
            <div class="section-head reveal">
                <span class="badge">Возможности</span>
                <h2>Инструменты для стабильного дохода</h2>
            </div>
            <div class="opportunities-grid">
                <article class="feature-card reveal slide-up">
                    <h3>Личный кабинет</h3>
                    <p>Следите за каждым приглашением и начислениями в реальном времени.</p>
                </article>
                <article class="feature-card reveal slide-up">
                    <h3>Гибкий формат</h3>
                    <p>Работайте в удобное время: утром, вечером или только в выходные.</p>
                </article>
                <article class="feature-card reveal slide-up">
                    <h3>Понятные правила</h3>
                    <p>Никаких скрытых условий: видите весь путь от лида до выплаты.</p>
                </article>
                <article class="feature-card reveal slide-up">
                    <h3>Рост без ограничений</h3>
                    <p>Чем активнее приглашения, тем выше ваш ежемесячный результат.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="section section-soft" id="how">
        <div class="landing-shell">
            <div class="section-head reveal">
                <span class="badge">Как начать</span>
                <h2>4 шага до первой выплаты</h2>
            </div>
            <div class="steps-timeline">
                <article class="step-card reveal">
                    <img src="/assets/img/step1.png" alt="Регистрация" width="100" height="100" onerror="this.style.display='none'">
                    <h3>Регистрация</h3>
                    <p>Создайте аккаунт и получите персональную ссылку.</p>
                </article>
                <article class="step-card reveal">
                    <img src="/assets/img/step2.png" alt="Приглашение" width="100" height="100" onerror="this.style.display='none'">
                    <h3>Приглашение курьеров</h3>
                    <p>Делитесь ссылкой в соцсетях, чатах и сообществах.</p>
                </article>
                <article class="step-card reveal">
                    <img src="/assets/img/step3.png" alt="Доход" width="100" height="100" onerror="this.style.display='none'">
                    <h3>Доход от заказов</h3>
                    <p>Получайте начисления за активных приглашённых курьеров.</p>
                </article>
                <article class="step-card reveal">
                    <img src="/assets/img/step4.png" alt="Выплаты" width="100" height="100" onerror="this.style.display='none'">
                    <h3>Выплаты</h3>
                    <p>Оформляйте вывод средств и контролируйте историю транзакций.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="section" id="benefits">
        <div class="landing-shell">
            <div class="section-head reveal">
                <span class="badge">Преимущества</span>
                <h2>Современная платформа для комфортной работы</h2>
            </div>
            <div class="benefits-grid">
                <article class="benefit-card reveal">
                    <img src="/assets/img/benefit1.png" alt="Мгновенные выплаты" width="200" height="200" onerror="this.style.display='none'">
                    <h3>Мгновенные выплаты</h3>
                    <p>Подайте заявку и получайте деньги быстро и предсказуемо.</p>
                </article>
                <article class="benefit-card reveal">
                    <img src="/assets/img/benefit2.png" alt="Прозрачная статистика" width="200" height="200" onerror="this.style.display='none'">
                    <h3>Прозрачная статистика</h3>
                    <p>Вся аналитика в одном месте: статусы, начисления и динамика.</p>
                </article>
                <article class="benefit-card reveal">
                    <img src="/assets/img/benefit3.png" alt="Работа из любой точки" width="200" height="200" onerror="this.style.display='none'">
                    <h3>Работа из любой точки</h3>
                    <p>Используйте ноутбук или смартфон — платформа всегда под рукой.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="section section-soft" id="faq">
        <div class="landing-shell faq-layout">
            <div class="section-head reveal">
                <span class="badge">FAQ</span>
                <h2>Частые вопросы</h2>
                <p class="section-lead">Краткие и понятные ответы перед стартом.</p>
            </div>
            <div class="faq-list">
                <article class="faq-item reveal">
                    <button class="faq-question" type="button">Сколько можно заработать в месяц?</button>
                    <div class="faq-answer"><p>Доход зависит от активности: от 0 до 50 000₽+ при регулярном потоке приглашений.</p></div>
                </article>
                <article class="faq-item reveal">
                    <button class="faq-question" type="button">Нужен ли опыт в рекрутинге?</button>
                    <div class="faq-answer"><p>Нет, стартовать можно без опыта. Достаточно делиться ссылкой и отслеживать заявки.</p></div>
                </article>
                <article class="faq-item reveal">
                    <button class="faq-question" type="button">Как и когда происходят выплаты?</button>
                    <div class="faq-answer"><p>После подтверждения активности курьера начисления становятся доступны для вывода в кабинете. Каждый месяц выплаты приходят с 15 до 20 числа</p></div>
                </article>
                <article class="faq-item reveal">
                    <button class="faq-question" type="button">Можно ли совмещать с основной работой?</button>
                    <div class="faq-answer"><p>Да. График полностью свободный: вы сами выбираете время и интенсивность работы.</p></div>
                </article>
            </div>
        </div>
    </section>

    <section class="cta-strip reveal" aria-label="Финальный призыв">
        <div class="landing-shell cta-strip-content">
            <div>
                <h2>Готовы начать уже сегодня?</h2>
                <p>Пройдите быструю регистрацию и подключитесь к партнёрской платформе.</p>
            </div>
            <a href="/register" class="btn btn-primary btn-lg">Открыть аккаунт</a>
        </div>
    </section>
</main>

<footer class="landing-footer">
    <div class="landing-shell footer-grid">
        <section>
            <h3>Контакты</h3>
            <p>Email: support@partner-eda.ru</p>
        </section>
        <section>
            <h3>Поддержка</h3>
            <p><a href="/info">Центр помощи</a></p>
            <p><a href="/terms">Условия использования</a></p>
            <p><a href="/privacy">Политика конфиденциальности</a></p>
        </section>
        <section>
            <h3>Соцсети</h3>
            <p><a href="#">Telegram</a></p>
            <p><a href="#">VK</a></p>
            <p><a href="#">YouTube</a></p>
        </section>
    </div>
    <div class="landing-shell footer-bottom">© <?= date('Y')?> Будьте с нашей командой!</div>
</footer>

<script src="/assets/js/app.js"></script>
</body>
</html>