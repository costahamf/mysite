document.addEventListener('DOMContentLoaded', () => {
  const pass = document.querySelector('#password');
  const passConfirm = document.querySelector('#password_confirm');

  if (!pass || !passConfirm) {
    return;
  }

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
});
