var registerForm = document.getElementById("registerForm");

if (registerForm) {
    var email = document.getElementById("reg_email");
    var emailMsg = document.getElementById("reg_email_msg");

    // AJAX duplicate email check
    email.addEventListener("keyup", function () {
        var value = email.value.trim();

        if (value === "") {
            emailMsg.innerHTML = "";
            return;
        }

        var xhr = new XMLHttpRequest();
        xhr.open(
            "GET",
            "index.php?action=ajax_check_email&email=" + encodeURIComponent(value),
            true
        );

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var data = JSON.parse(xhr.responseText);

                if (data.exists) {
                    emailMsg.innerHTML = "Email already registered";
                } else {
                    emailMsg.innerHTML = "";
                }
            }
        };

        xhr.send();
    });

    // JavaScript form validation
    registerForm.addEventListener("submit", function (event) {
        var name = document.getElementById("full_name").value.trim();
        var phone = document.getElementById("phone").value.trim();
        var password = document.getElementById("reg_password").value;
        var confirmPassword = document.getElementById("confirm_password").value;
        var errors = [];

        if (name === "") {
            errors.push("Full name is required.");
        }

        if (!/^\d{11}$/.test(phone)) {
            errors.push("Phone must contain exactly 11 digits.");
        }

        if (password.length < 6) {
            errors.push("Password must contain at least 6 characters.");
        }

        if (password !== confirmPassword) {
            errors.push("Passwords do not match.");
        }

        if (errors.length > 0) {
            event.preventDefault();
            alert(errors.join("\n"));
        }
    });
}

var appointmentForm = document.getElementById("appointmentForm");

if (appointmentForm) {
    appointmentForm.addEventListener("submit", function (event) {
        var service = document.getElementById("service_id").value;
        var date = document.getElementById("appointment_date").value;
        var time = document.getElementById("slot_time").value;

        if (service === "" || date === "" || time === "") {
            event.preventDefault();
            alert("Please complete all appointment fields.");
        }
    });
}
