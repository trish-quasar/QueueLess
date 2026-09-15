(function () {
    function setMessage(id, message, type) {
        var element = document.getElementById(id);

        if (!element) {
            return;
        }

        element.textContent = message || '';
        element.classList.remove('success', 'error');

        if (message && type) {
            element.classList.add(type);
        }
    }

    function showClientError(form, message) {
        var old = form.querySelector('.client-validation-error');

        if (old) {
            old.remove();
        }

        if (!message) {
            return;
        }

        var box = document.createElement('div');
        box.className = 'alert error client-validation-error';
        box.textContent = message;
        form.insertBefore(box, form.firstChild);
    }

    function passwordChecks(password) {
        return {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password),
            symbol: /[^A-Za-z0-9]/.test(password)
        };
    }

    function updatePasswordRules(checks, passwordValue) {
        var rules = document.getElementById('password_rules');

        if (!rules) {
            return;
        }

        var items = rules.querySelectorAll('[data-rule]');
        var hasMissingRule = false;

        for (var i = 0; i < items.length; i++) {
            var rule = items[i].getAttribute('data-rule');
            var passed = checks[rule] === true;

            // A passed rule disappears. Only missing requirements remain visible.
            if (passed) {
                items[i].style.display = 'none';
            } else {
                items[i].style.display = '';
                hasMissingRule = true;
            }
        }

        // Do not show password instructions before typing.
        // Also hide the box again once all requirements are satisfied.
        if (passwordValue === '' || !hasMissingRule) {
            rules.style.display = 'none';
        } else {
            rules.style.display = 'grid';
        }
    }

    var registerForm = document.getElementById('registerForm');

    if (registerForm) {
        var email = document.getElementById('reg_email');
        var username = document.getElementById('reg_username');
        var password = document.getElementById('reg_password');
        var confirmPassword = document.getElementById('confirm_password');
        var emailTimer = null;
        var usernameTimer = null;
        var passwordTimer = null;

        email.addEventListener('input', function () {
            var value = email.value.trim();
            clearTimeout(emailTimer);
            email.dataset.available = '';

            if (value === '') {
                setMessage('reg_email_msg', '', '');
                return;
            }

            setMessage('reg_email_msg', 'Checking email...', '');

            emailTimer = setTimeout(function () {
                var xhr = new XMLHttpRequest();
                xhr.open(
                    'GET',
                    'index.php?action=ajax_check_email&email=' + encodeURIComponent(value),
                    true
                );

                xhr.onreadystatechange = function () {
                    if (xhr.readyState !== 4 || xhr.status !== 200) {
                        return;
                    }

                    try {
                        var data = JSON.parse(xhr.responseText);
                        email.dataset.available = data.valid && !data.exists ? 'yes' : 'no';
                        setMessage(
                            'reg_email_msg',
                            data.message || '',
                            data.valid && !data.exists ? 'success' : 'error'
                        );
                    } catch (e) {
                        email.dataset.available = '';
                        setMessage('reg_email_msg', '', '');
                    }
                };

                xhr.send();
            }, 300);
        });

        username.addEventListener('input', function () {
            var value = username.value.trim().toLowerCase();
            clearTimeout(usernameTimer);
            username.dataset.available = '';

            if (value === '') {
                setMessage('reg_username_msg', '', '');
                return;
            }

            setMessage('reg_username_msg', 'Checking username...', '');

            usernameTimer = setTimeout(function () {
                var xhr = new XMLHttpRequest();
                xhr.open(
                    'GET',
                    'index.php?action=ajax_check_username&username=' + encodeURIComponent(value),
                    true
                );

                xhr.onreadystatechange = function () {
                    if (xhr.readyState !== 4 || xhr.status !== 200) {
                        return;
                    }

                    try {
                        var data = JSON.parse(xhr.responseText);
                        username.dataset.available = data.available ? 'yes' : 'no';
                        setMessage(
                            'reg_username_msg',
                            data.message || '',
                            data.available ? 'success' : 'error'
                        );
                    } catch (e) {
                        username.dataset.available = '';
                        setMessage('reg_username_msg', '', '');
                    }
                };

                xhr.send();
            }, 300);
        });

        password.addEventListener('input', function () {
            var value = password.value;
            clearTimeout(passwordTimer);
            password.dataset.valid = '';

            updatePasswordRules(passwordChecks(value), value);

            if (value === '') {
                return;
            }

            passwordTimer = setTimeout(function () {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'index.php?action=ajax_validate_password', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                xhr.onreadystatechange = function () {
                    if (xhr.readyState !== 4 || xhr.status !== 200) {
                        return;
                    }

                    try {
                        var data = JSON.parse(xhr.responseText);
                        password.dataset.valid = data.valid ? 'yes' : 'no';
                        updatePasswordRules(data.checks || {}, value);
                    } catch (e) {
                        password.dataset.valid = '';
                    }
                };

                xhr.send('password=' + encodeURIComponent(value));
            }, 250);
        });

        function checkConfirmPassword() {
            if (confirmPassword.value === '') {
                setMessage('confirm_password_msg', '', '');
                return;
            }

            if (password.value === confirmPassword.value) {
                setMessage('confirm_password_msg', 'Passwords match.', 'success');
            } else {
                setMessage('confirm_password_msg', 'Passwords do not match.', 'error');
            }
        }

        confirmPassword.addEventListener('input', checkConfirmPassword);
        password.addEventListener('input', checkConfirmPassword);

        registerForm.addEventListener('submit', function (event) {
            var name = document.getElementById('full_name').value.trim();
            var phone = document.getElementById('phone').value.trim();
            var securityQuestion = document.getElementById('security_question').value;
            var securityAnswer = document.getElementById('security_answer').value.trim();
            var checks = passwordChecks(password.value);
            var errors = [];

            setMessage('name_msg', '', '');
            setMessage('phone_msg', '', '');

            if (name === '') {
                errors.push('Full name is required.');
                setMessage('name_msg', 'Full name is required.', 'error');
            }

            if (!/^[A-Za-z0-9_.]{4,30}$/.test(username.value.trim())) {
                errors.push('Choose a valid username.');
            } else if (username.dataset.available === 'no') {
                errors.push('Username is already taken.');
            }

            if (email.value.trim() === '') {
                errors.push('Email is required.');
            } else if (email.dataset.available === 'no') {
                errors.push('Use a valid, unused email address.');
            }

            if (!/^\d{11}$/.test(phone)) {
                errors.push('Phone must contain exactly 11 digits.');
                setMessage('phone_msg', 'Phone must contain exactly 11 digits.', 'error');
            }

            if (!checks.length || !checks.uppercase || !checks.lowercase || !checks.number || !checks.symbol) {
                errors.push('Password does not meet all requirements.');
            }

            if (password.value !== confirmPassword.value) {
                errors.push('Passwords do not match.');
            }

            if (securityQuestion === '') {
                errors.push('Select a security question.');
            }

            if (securityAnswer === '') {
                errors.push('Security answer is required.');
            }

            if (errors.length > 0) {
                event.preventDefault();
                showClientError(registerForm, errors[0]);
            }
        });
    }

    var loginForm = document.getElementById('loginForm');

    if (loginForm) {
        loginForm.addEventListener('submit', function (event) {
            var login = document.getElementById('login').value.trim();
            var password = document.getElementById('password').value;

            if (login === '' || password === '') {
                event.preventDefault();
                showClientError(loginForm, 'Enter your email/username and password.');
            }
        });
    }

    var appointmentForm = document.getElementById('appointmentForm');

    if (appointmentForm) {
        appointmentForm.addEventListener('submit', function (event) {
            var service = document.getElementById('service_id').value;
            var date = document.getElementById('appointment_date').value;
            var time = document.getElementById('slot_time').value;

            if (service === '' || date === '' || time === '') {
                event.preventDefault();
                showClientError(appointmentForm, 'Complete the service, date and time fields.');
            }
        });
    }
})();
