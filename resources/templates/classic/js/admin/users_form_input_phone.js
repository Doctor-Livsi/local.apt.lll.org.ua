document.addEventListener('DOMContentLoaded', function () {
    const phoneInput = document.getElementById('phone');
    if (!phoneInput) return;

    phoneInput.setAttribute('inputmode', 'tel');

    // Позиції цифр у відформатованому рядку
    const digitPositions = [5, 6, 8, 9, 10, 12, 13, 15, 16];

    // Створюємо елемент для повідомлення про помилку
    const errorMessage = document.createElement('div');
    errorMessage.className = 'text-danger mt-1';
    errorMessage.style.fontSize = '0.875rem';
    errorMessage.id = 'phone-error';
    phoneInput.parentNode.appendChild(errorMessage);

    // Функція для форматування номера
    const formatPhoneNumber = (digits) => {
        digits = digits.replace(/\D/g, ''); // Залишаємо лише цифри
        if (digits.length === 0) return '+380(__)___-__-__';

        let formatted = '+380';
        if (digits.length > 3) {
            formatted += '(' + digits.slice(3, 5).padEnd(2, '_') + ')';
            if (digits.length > 5) {
                formatted += digits.slice(5, 8).padEnd(3, '_') + '-';
                if (digits.length > 8) {
                    formatted += digits.slice(8, 10).padEnd(2, '_') + '-';
                    if (digits.length > 10) {
                        formatted += digits.slice(10, 12).padEnd(2, '_');
                    } else {
                        formatted += '__';
                    }
                } else {
                    formatted += '___-__';
                }
            } else {
                formatted += '___-__-__';
            }
        } else {
            formatted += '(__)___-__-__';
        }
        return formatted;
    };

    // Функція для перевірки повноти номера
    const validatePhone = () => {
        const digits = phoneInput.value.replace(/\D/g, '');
        const errorElement = document.getElementById('phone-error');

        if (digits.length < 12) {
            phoneInput.classList.add('is-invalid');
            errorElement.textContent = 'Номер телефону введено не повністю.';
            return false;
        } else {
            phoneInput.classList.remove('is-invalid');
            errorElement.textContent = '';
            return true;
        }
    };

    // Обробка введення
    phoneInput.addEventListener('input', (e) => {
        const digits = phoneInput.value.replace(/\D/g, '');
        phoneInput.value = formatPhoneNumber(digits);

        // Перевірка номера після введення
        validatePhone();

        // Визначаємо кількість введених цифр (без префікса 380)
        const userDigitCount = Math.max(0, digits.length - 3); // Віднімаємо 3 цифри префікса
        const digitCount = Math.min(userDigitCount, digitPositions.length);
        let newPos = digitPositions[digitCount] || digitPositions[0];

        // Якщо видаляємо цифру, коригуємо позицію
        if (e.inputType === 'deleteContentBackward' || e.inputType === 'deleteContentForward') {
            newPos = digitPositions[Math.max(0, digitCount - 1)];
        }

        setTimeout(() => {
            phoneInput.setSelectionRange(newPos, newPos);
        }, 0);
    });

    // Обробка переміщення курсора
    phoneInput.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
            e.preventDefault();

            const pos = phoneInput.selectionStart;
            const direction = e.key === 'ArrowRight' ? 1 : -1;

            let currentIndex = digitPositions.indexOf(pos);
            if (currentIndex === -1) {
                currentIndex = digitPositions.reduce((closestIndex, digitPos, index) => {
                    if (direction > 0) {
                        if (digitPos > pos && (closestIndex === -1 || digitPos < digitPositions[closestIndex])) {
                            return index;
                        }
                    } else {
                        if (digitPos < pos && (closestIndex === -1 || digitPos > digitPositions[closestIndex])) {
                            return index;
                        }
                    }
                    return closestIndex;
                }, -1);

                if (currentIndex === -1) {
                    currentIndex = direction > 0 ? 0 : digitPositions.length - 1;
                }
            } else {
                currentIndex = (currentIndex + direction + digitPositions.length) % digitPositions.length;
            }

            const newPos = digitPositions[currentIndex];
            setTimeout(() => {
                phoneInput.setSelectionRange(newPos, newPos);
            }, 0);
        }
    });

    // Перевірка при втраті фокусу
    phoneInput.addEventListener('blur', validatePhone);

    // Блокування відправки форми
    const form = phoneInput.closest('form');
    if (form) {
        form.addEventListener('submit', (e) => {
            if (!validatePhone()) {
                e.preventDefault();
                phoneInput.focus();
            }
        });
    }

    // Форматування початкового значення
    if (phoneInput.value && phoneInput.value !== '+380(__)___-__-__') {
        const digits = phoneInput.value.replace(/\D/g, '');
        phoneInput.value = formatPhoneNumber(digits);
        validatePhone();
    } else {
        phoneInput.value = '+380(__)___-__-__';
        validatePhone(); // Перевіряємо початкове значення
    }
});