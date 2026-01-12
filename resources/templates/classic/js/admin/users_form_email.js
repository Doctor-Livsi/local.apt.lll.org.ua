document.addEventListener('DOMContentLoaded', function () {
    const applyEmailMaskWithDelay = () => {
        if (typeof $.fn.inputmask !== "undefined") {
            const emailInput = document.getElementById('email');
            if (emailInput) {
                try {
                    emailInput.setAttribute('inputmode', 'email');

                    // Створюємо елемент для повідомлення про помилку
                    const errorMessage = document.createElement('div');
                    errorMessage.className = 'text-danger mt-1';
                    errorMessage.style.fontSize = '0.875rem';
                    errorMessage.id = 'email-error';
                    emailInput.parentNode.appendChild(errorMessage);

                    // Налаштування маски для email
                    $(emailInput).inputmask({
                        alias: 'email',
                        placeholder: "___@__.__",
                        autoUnmask: false,
                        showMaskOnHover: true,
                        showMaskOnFocus: true,
                        clearIncomplete: true,
                        onBeforePaste: function (pastedValue, opts) {
                            return pastedValue.toLowerCase();
                        }
                    });

                    // Функція для перевірки коректності email
                    const validateEmail = () => {
                        const value = emailInput.value;
                        const errorElement = document.getElementById('email-error');
                        // Регулярний вираз для перевірки email (user@domain.com)
                        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

                        if (!value || !emailRegex.test(value)) {
                            emailInput.classList.add('is-invalid');
                            errorElement.textContent = 'Введіть коректний email у форматі user@domain.com';
                            return false;
                        } else {
                            emailInput.classList.remove('is-invalid');
                            errorElement.textContent = '';
                            return true;
                        }
                    };

                    // Перевірка при введенні
                    emailInput.addEventListener('input', () => {
                        emailInput.value = emailInput.value.toLowerCase();
                        validateEmail();
                    });

                    // Перевірка при втраті фокусу
                    emailInput.addEventListener('blur', validateEmail);

                    // Блокування відправки форми
                    const form = emailInput.closest('form');
                    if (form) {
                        form.addEventListener('submit', (e) => {
                            if (!validateEmail()) {
                                e.preventDefault();
                                emailInput.focus();
                            }
                        });
                    }

                    // Початкова перевірка
                    validateEmail();

                    console.log("Inputmask initialized for #email");
                } catch (e) {
                    console.error("Error initializing Inputmask for email:", e);
                }
            } else {
                console.log("Email input not found");
            }
        } else {
            console.log("Inputmask not available yet, retrying...");
            setTimeout(applyEmailMaskWithDelay, 1000);
        }
    };

    setTimeout(applyEmailMaskWithDelay, 500);
});