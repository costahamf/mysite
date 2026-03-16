document.addEventListener('DOMContentLoaded', () => {
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
});

document.addEventListener('DOMContentLoaded', () => {
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

  const faqItems = document.querySelectorAll('.faq-item');
  faqItems.forEach((item) => {
    const btn = item.querySelector('.faq-question');
    const answer = item.querySelector('.faq-answer');

    if (!btn || !answer) {
      return;
    }

    btn.addEventListener('click', () => {
      const willOpen = !item.classList.contains('open');

      faqItems.forEach((node) => {
        node.classList.remove('open');
        const nodeAnswer = node.querySelector('.faq-answer');
        if (nodeAnswer) {
          nodeAnswer.style.maxHeight = '0px';
        }
      });

      if (willOpen) {
        item.classList.add('open');
        answer.style.maxHeight = `${answer.scrollHeight}px`;
      }
    });
  });

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
});