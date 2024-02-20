document.getElementById('toggle-password').addEventListener('click', function() {
    let passwordInputs = document.getElementsByClassName('password');

    for (let i = 0; i < passwordInputs.length; i++) {
        let passwordInput = passwordInputs[i];
        let passwordFieldType = passwordInput.getAttribute('type');

        if (passwordFieldType === 'password') {
            passwordInput.setAttribute('type', 'text');
            document.getElementById('toggle-password').textContent = 'Hide';
        } else {
            passwordInput.setAttribute('type', 'password');
            document.getElementById('toggle-password').textContent = 'Show';
        }
    }
});
