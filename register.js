"use strict";

function toggleWalker() {
    const checkbox= document.getElementById('walkerToggle');
    const extraBox= document.getElementById('walkerExtra');
    const walkerBox= document.getElementById('walkerBox');

    if (event.currentTarget && event.currentTarget.classList.contains('walker-header')) {
        checkbox.checked = !checkbox.checked;
    }

    if (checkbox.checked) {
        extraBox.classList.add('open');
        walkerBox.classList.add('active');
    } else {
        extraBox.classList.remove('open');
        walkerBox.classList.remove('active');
    }
}


function togglePwd(fieldId, btn) {
    const input = document.getElementById(fieldId);
    if (input.type === 'password') {
        input.type= 'text';
        btn.textContent = '🙈';
    } else {
        input.type= 'password';
        btn.textContent = '👁️';
    }
}

document.addEventListener('DOMContentLoaded', function () {

    const emailInput = document.getElementById('email');
    const emailCheck = document.getElementById('emailCheck');

    if (emailInput && emailCheck) {
        emailInput.addEventListener('blur', function () {
            const emailValue = emailInput.value.trim();
            if (!emailValue) return;

            fetch('check_email.php', {
                method:'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: emailValue })
            })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (!data.valid) {
                        emailCheck.textContent = 'Unesite ispravnu e-mail adresu.';
                        emailCheck.style.color = '#c0392b';
                    } else if (data.exists) {
                        emailCheck.textContent = 'Korisnik sa ovom email adresom vec postoji.';
                        emailCheck.style.color = '#c0392b';
                    } else {
                        emailCheck.textContent = 'Email adresa je dostupna \u2713';
                        emailCheck.style.color = '#4a5e3a';
                    }
                })
                .catch(function () { emailCheck.textContent = ''; });
        });
    }

    const pwdInput= document.getElementById('pwd');
    const pwd2Input= document.getElementById('pwd2');
    const pwd2Msg= document.getElementById('pwd2Msg');

    if (pwd2Input) {
        pwd2Input.addEventListener('input', function () {
            if (!pwdInput.value || !pwd2Input.value) {
                if (pwd2Msg) pwd2Msg.textContent = '';
                return;
            }
            if (pwdInput.value === pwd2Input.value) {
                pwd2Input.classList.remove('is-invalid');
                if (pwd2Msg) { pwd2Msg.textContent = 'Lozinke se poklapaju \u2713'; pwd2Msg.style.color = '#4a5e3a'; }
            } else {
                pwd2Input.classList.add('is-invalid');
                if (pwd2Msg) { pwd2Msg.textContent = 'Lozinke se ne poklapaju.'; pwd2Msg.style.color = '#c0392b'; }
            }
        });
    }

    const regForm = document.getElementById('regForm');

    if (regForm) {
        regForm.addEventListener('submit', function (e) {
            let isValid = true;

            const requiredFields = ['fname', 'lname', 'email', 'pwd', 'pwd2'];
            requiredFields.forEach(function (fieldId) {
                const field = document.getElementById(fieldId);
                if (!field || !field.value.trim()) {
                    if (field) field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    if (field) field.classList.remove('is-invalid');
                }
            });

            if (pwdInput && pwdInput.value) {
                const pwdValue = pwdInput.value;
                const isStrong = pwdValue.length >= 8 && /[A-Z]/.test(pwdValue) && /[0-9]/.test(pwdValue);
                if (!isStrong) {
                    pwdInput.classList.add('is-invalid');
                    isValid = false;
                }
            }

            if (pwdInput && pwd2Input && pwdInput.value !== pwd2Input.value) {
                pwd2Input.classList.add('is-invalid');
                isValid = false;
            }

            const termsBox = document.getElementById('terms');
            if (termsBox && !termsBox.checked) {
                termsBox.classList.add('is-invalid');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    }

    document.querySelectorAll('.form-control, .form-check-input').forEach(function (el) {
        el.addEventListener('input', function () { el.classList.remove('is-invalid'); });
    });
});
