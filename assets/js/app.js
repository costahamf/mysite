document.addEventListener('DOMContentLoaded', () => {
  // === ПРОВЕРКА ПАРОЛЕЙ ===
  const pass = document.querySelector('#password');
  const passConfirm = document.querySelector('#password_confirm');

  if (pass && passConfirm) {
    const check = () => {
      if (!passConfirm.value.length) {
        passConfirm.setCustomValidity('');
        return;
      }

      if (pass.value !== passConfirm.value) {
        passConfirm.setCustomValidity('Пароли не совпадают');
      } else {
        passConfirm.setCustomValidity('');
      }
    };

    pass.addEventListener('input', check);
    passConfirm.addEventListener('input', check);
  }

  // === КОПИРОВАНИЕ ССЫЛКИ ===
  const copyButton = document.querySelector('[data-copy-link]');
  const feedback = document.querySelector('#copy-feedback');

  if (copyButton && feedback) {
    copyButton.addEventListener('click', async () => {
      const link = copyButton.dataset.copyLink || '';
      const message = copyButton.dataset.copyMessage || 'Ссылка скопирована';

      if (!link) {
        return;
      }

      try {
        await navigator.clipboard.writeText(link);
        feedback.textContent = message;
        feedback.classList.remove('d-none');
        setTimeout(() => feedback.classList.add('d-none'), 2200);
      } catch (error) {
        feedback.textContent = 'Не удалось скопировать автоматически. Скопируйте ссылку вручную.';
        feedback.classList.remove('d-none');
      }
    });
  }

  // === ПРЕДПРОСМОТР НОВОСТЕЙ ===
  const newsButtons = document.querySelectorAll('.news-select-btn');
  const previewTitle = document.querySelector('#news-preview-title');
  const previewDate = document.querySelector('#news-preview-date');
  const previewImage = document.querySelector('#news-preview-image');
  const previewContent = document.querySelector('#news-preview-content');

  if (newsButtons.length && previewTitle && previewDate && previewImage && previewContent) {
    newsButtons.forEach((button) => {
      button.addEventListener('click', () => {
        newsButtons.forEach((item) => item.classList.remove('active'));
        button.classList.add('active');

        previewTitle.textContent = button.dataset.newsTitle || '';
        previewDate.textContent = button.dataset.newsDate || '';
        previewContent.textContent = button.dataset.newsContent || '';

        const image = button.dataset.newsImage || '';
        if (image.length) {
          previewImage.src = image;
          previewImage.classList.remove('d-none');
        } else {
          previewImage.src = '';
          previewImage.classList.add('d-none');
        }
      });
    });
  }

  // === МОБИЛЬНОЕ МЕНЮ ===
  const menu = document.querySelector('#landing-menu');
  const toggle = document.querySelector('.nav-toggle');

  if (menu && toggle) {
    toggle.addEventListener('click', () => {
      const isOpen = menu.classList.toggle('open');
      toggle.setAttribute('aria-expanded', String(isOpen));
    });

    menu.querySelectorAll('a[href^="#"]').forEach((link) => {
      link.addEventListener('click', () => {
        menu.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
      });
    });
  }

  // === ПЛАВНЫЙ СКРОЛЛ ===
  const smoothLinks = document.querySelectorAll('a[href^="#"]');
  smoothLinks.forEach((link) => {
    link.addEventListener('click', (event) => {
      const href = link.getAttribute('href');
      if (!href || href === '#') {
        return;
      }

      const target = document.querySelector(href);
      if (!target) {
        return;
      }

      event.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  });

  // === FAQ АККОРДЕОН ===
  const faqItems = document.querySelectorAll('.faq-item');
  faqItems.forEach((item, index) => {
    const btn = item.querySelector('.faq-question');
    const answer = item.querySelector('.faq-answer');

    if (!btn || !answer) {
      return;
    }

    const answerId = `faq-answer-${index + 1}`;
    answer.id = answerId;
    btn.setAttribute('aria-controls', answerId);
    btn.setAttribute('aria-expanded', 'false');

    btn.addEventListener('click', () => {
      const willOpen = !item.classList.contains('open');

      faqItems.forEach((node) => {
        node.classList.remove('open');
        const nodeBtn = node.querySelector('.faq-question');
        const nodeAnswer = node.querySelector('.faq-answer');

        if (nodeBtn) {
          nodeBtn.setAttribute('aria-expanded', 'false');
        }

        if (nodeAnswer) {
          nodeAnswer.style.maxHeight = '0px';
        }
      });

      if (willOpen) {
        item.classList.add('open');
        btn.setAttribute('aria-expanded', 'true');
        answer.style.maxHeight = `${answer.scrollHeight}px`;
      }
    });
  });

  // === АНИМАЦИЯ ПОЯВЛЕНИЯ ПРИ СКРОЛЛЕ (REVEAL) ===
  const revealNodes = document.querySelectorAll('.reveal');
  if (revealNodes.length) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });

    revealNodes.forEach((node) => observer.observe(node));
  }

  // === АНИМАЦИЯ ДЛЯ ВНУТРЕННИХ СТРАНИЦ ===
  const appBody = document.body;
  if (appBody && !appBody.classList.contains('landing-page')) {
    const animatedCards = document.querySelectorAll('.crm-card, .table-responsive, .alert');
    animatedCards.forEach((node) => node.classList.add('ui-reveal'));

    if ('IntersectionObserver' in window) {
      const uiObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('ui-visible');
            uiObserver.unobserve(entry.target);
          }
        });
      }, { threshold: 0.12 });

      animatedCards.forEach((node) => uiObserver.observe(node));
    } else {
      animatedCards.forEach((node) => node.classList.add('ui-visible'));
    }
  }
});

// === ОБРАБОТЧИК ДЛЯ RESIZE (чтобы корректно работала высота FAQ) ===
let resizeTimer;
window.addEventListener('resize', () => {
  clearTimeout(resizeTimer);
  resizeTimer = setTimeout(() => {
    const openFaq = document.querySelector('.faq-item.open .faq-answer');
    if (openFaq) {
      openFaq.style.maxHeight = `${openFaq.scrollHeight}px`;
    }
  }, 250);
});