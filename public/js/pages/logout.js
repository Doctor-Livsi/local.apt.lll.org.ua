document.addEventListener('DOMContentLoaded', function () {
    const logoutButton = document.getElementById('logout');
    if (logoutButton) {
        logoutButton.addEventListener('click', function () {
            // Створюємо форму для POST-запиту
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/logout';

            // Додаємо CSRF-токен
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = csrfToken;
            form.appendChild(tokenInput);

            // Додаємо форму до DOM і викликаємо submit
            document.body.appendChild(form);
            form.submit();
        });
    }
});