document.addEventListener('DOMContentLoaded', function () {
    const lastNameInput = document.getElementById('last_name');
    const firstNameInput = document.getElementById('first_name');
    const middleNameInput = document.getElementById('middle_name');
    const nameInput = document.getElementById('name');

    // Створюємо елементи для повідомлень про помилки
    const createErrorElement = (input, id) => {
        const errorMessage = document.createElement('div');
        errorMessage.className = 'text-danger mt-1';
        errorMessage.style.fontSize = '0.875rem';
        errorMessage.id = id;
        input.parentNode.appendChild(errorMessage);
    };

    createErrorElement(lastNameInput, 'last-name-error');
    createErrorElement(firstNameInput, 'first-name-error');
    createErrorElement(middleNameInput, 'middle-name-error');

    // Функція для форматування: перша буква велика, решта — малі
    function capitalizeFirstLetter(input) {
        const value = input.value.trim();
        if (value) {
            input.value = value.charAt(0).toUpperCase() + value.slice(1).toLowerCase();
        }
    }

    function updateName() {
        let lastName = (lastNameInput.value || '').trim();
        let firstName = (firstNameInput.value || '').trim();
        let middleName = (middleNameInput.value || '').trim();

        let formattedName = lastName;

        if (firstName) {
            firstName = firstName.charAt(0).toUpperCase() + '.';
            formattedName += ' ' + firstName;
        }

        if (middleName) {
            middleName = middleName.charAt(0).toUpperCase() + '.';
            formattedName += middleName;
        }

        nameInput.value = formattedName.trim();
    }

    // Функція для перевірки довжини поля
    const validateField = (input, errorId, fieldName) => {
        const value = input.value.trim();
        const errorElement = document.getElementById(errorId);
        const minLength = 3;

        if (value.length < minLength) {
            input.classList.add('is-invalid');
            errorElement.textContent = `${fieldName} має бути не менше ${minLength} символів.`;
            return false;
        } else {
            input.classList.remove('is-invalid');
            errorElement.textContent = '';
            return true;
        }
    };

    // Функція для перевірки всіх полів
    const validateAllFields = () => {
        const isLastNameValid = validateField(lastNameInput, 'last-name-error', 'Прізвище');
        const isFirstNameValid = validateField(firstNameInput, 'first-name-error', "Ім'я");
        const isMiddleNameValid = validateField(middleNameInput, 'middle-name-error', 'По батькові');

        return isLastNameValid && isFirstNameValid && isMiddleNameValid;
    };

    // Обробник для кожного поля
    function handleInput(input, errorId, fieldName) {
        return function () {
            capitalizeFirstLetter(input);
            validateField(input, errorId, fieldName);
            updateName();
            // Додаткове форматування через 100 мс
            setTimeout(() => capitalizeFirstLetter(input), 100);
        };
    }

    lastNameInput.addEventListener('input', handleInput(lastNameInput, 'last-name-error', 'Прізвище'));
    firstNameInput.addEventListener('input', handleInput(firstNameInput, 'first-name-error', "Ім'я"));
    middleNameInput.addEventListener('input', handleInput(middleNameInput, 'middle-name-error', 'По батькові'));

    // Перевірка при втраті фокусу
    lastNameInput.addEventListener('blur', () => validateField(lastNameInput, 'last-name-error', 'Прізвище'));
    firstNameInput.addEventListener('blur', () => validateField(firstNameInput, 'first-name-error', "Ім'я"));
    middleNameInput.addEventListener('blur', () => validateField(middleNameInput, 'middle-name-error', 'По батькові'));

    // Блокування відправки форми
    const form = lastNameInput.closest('form');
    if (form) {
        form.addEventListener('submit', (e) => {
            if (!validateAllFields()) {
                e.preventDefault();
                // Фокус на перше поле з помилкою
                if (!validateField(lastNameInput, 'last-name-error', 'Прізвище')) {
                    lastNameInput.focus();
                } else if (!validateField(firstNameInput, 'first-name-error', "Ім'я")) {
                    firstNameInput.focus();
                } else if (!validateField(middleNameInput, 'middle-name-error', 'По батькові')) {
                    middleNameInput.focus();
                }
            }
        });
    }

    // Початкове форматування
    setTimeout(() => {
        capitalizeFirstLetter(lastNameInput);
        capitalizeFirstLetter(firstNameInput);
        capitalizeFirstLetter(middleNameInput);
        validateAllFields();
        updateName();
    }, 500);
});