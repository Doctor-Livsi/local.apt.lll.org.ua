document.addEventListener('DOMContentLoaded', function () {
    const lastNameInput = document.getElementById('last_name');
    const firstNameInput = document.getElementById('first_name');
    const middleNameInput = document.getElementById('middle_name');
    const nameInput = document.getElementById('name');

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

    lastNameInput.addEventListener('input', updateName);
    firstNameInput.addEventListener('input', updateName);
    middleNameInput.addEventListener('input', updateName);

    updateName();
});