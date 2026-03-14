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
