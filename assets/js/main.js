document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.querySelector('[data-nav-toggle]');
  const links = document.querySelector('[data-nav-links]');

  if (toggle && links) {
    toggle.addEventListener('click', () => {
      links.classList.toggle('open');
      toggle.classList.toggle('open');
    });
  }

  document.querySelectorAll('[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
      const message = form.getAttribute('data-confirm') || 'Are you sure?';
      if (!window.confirm(message)) {
        event.preventDefault();
      }
    });
  });

  document.querySelectorAll('form[data-validate]').forEach((form) => {
    form.addEventListener('submit', (event) => {
      const invalid = [...form.querySelectorAll('[required]')].find((field) => !field.value.trim());
      if (invalid) {
        event.preventDefault();
        invalid.focus();
        invalid.classList.add('field-error');
      }
    });
  });

  document.querySelectorAll('.field-error').forEach((field) => {
    field.addEventListener('input', () => field.classList.remove('field-error'));
  });
});
