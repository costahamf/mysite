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

  if (!copyButton || !feedback) {
    return;
  }

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
});
