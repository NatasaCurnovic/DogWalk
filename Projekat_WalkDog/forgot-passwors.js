"use strict";

var AUTH_API_FORGOT = "api/forgot_password.php";

/* ── Mobile nav ── */
(function () {
    var btn  = document.getElementById("navToggle");
    var menu = document.getElementById("navMenu");
    if (!btn || !menu) return;
    btn.addEventListener("click", function () {
        var open = menu.classList.toggle("d-flex");
        menu.classList.toggle("d-none", !open);
        btn.setAttribute("aria-expanded", String(open));
    });
})();

function showAlert(container, message, type) {
    if (!container) return;
    container.textContent = message;
    container.className   = "auth-alert auth-alert--" + type + " mb-3";
    container.classList.remove("d-none");
}

function hideAlert(container) {
    if (!container) return;
    container.classList.add("d-none");
    container.textContent = "";
}

function setLoading(btn, loading) {
    if (!btn) return;
    var text    = btn.querySelector(".btn-text");
    var spinner = btn.querySelector(".btn-spinner");
    btn.disabled = loading;
    if (text)    text.classList.toggle("d-none", loading);
    if (spinner) spinner.classList.toggle("d-none", !loading);
}

function showSuccess() {
    document.getElementById("forgot-step-request").classList.add("d-none");
    document.getElementById("forgot-step-success").classList.remove("d-none");
}

async function handleSubmit(email) {
    var alertBox  = document.getElementById("forgot-alert");
    var submitBtn = document.getElementById("forgot-submit-btn");

    hideAlert(alertBox);
    setLoading(submitBtn, true);

    try {
        var response = await fetch(AUTH_API_FORGOT, {
            method: "POST",
            headers: { "Content-Type": "application/json", "Accept": "application/json" },
            body: JSON.stringify({ email: email })
        });
        var data = await response.json();
        if (data.success || response.ok) {
            showSuccess();
        } else {
            showAlert(alertBox, data.message || "Greška. Pokušajte ponovo.", "error");
        }
    } catch (err) {
        showSuccess(); // Demo fallback
    } finally {
        setLoading(submitBtn, false);
    }
}

document.getElementById("forgot-form").addEventListener("submit", function (e) {
    e.preventDefault();

    var emailInput = document.getElementById("email");
    var emailError = document.getElementById("email-error");
    var emailRx    = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    emailInput.classList.remove("is-invalid", "is-valid");
    emailError.textContent = "";

    if (!emailInput.value.trim() || !emailRx.test(emailInput.value)) {
        emailInput.classList.add("is-invalid");
        emailError.textContent = "Unesite validnu e-mail adresu.";
        return;
    }

    emailInput.classList.add("is-valid");
    handleSubmit(emailInput.value.trim());
});

document.getElementById("resend-btn").addEventListener("click", function () {
    document.getElementById("forgot-step-success").classList.add("d-none");
    document.getElementById("forgot-step-request").classList.remove("d-none");
    var emailInput = document.getElementById("email");
    if (emailInput) {
        emailInput.classList.remove("is-invalid", "is-valid");
        emailInput.focus();
    }
});