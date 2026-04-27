"use strict";

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

/* ── Password visibility toggles ── */
document.querySelectorAll(".toggle-password").forEach(function (button) {
    button.addEventListener("click", function () {
        var targetId = button.getAttribute("data-target");
        var input    = document.getElementById(targetId);
        if (!input) return;
        var hidden   = input.type === "password";
        input.type   = hidden ? "text" : "password";
        var icon     = button.querySelector("i");
        if (icon) icon.className = hidden ? "bi bi-eye-slash" : "bi bi-eye";
    });
});

/* ── Walker toggle ── */
(function () {
    var sw     = document.getElementById("is-walker");
    var fields = document.getElementById("walker-fields");
    if (!sw || !fields) return;
    sw.addEventListener("change", function () {
        var on = sw.checked;
        sw.setAttribute("aria-checked", String(on));
        fields.classList.toggle("d-none", !on);
        if (on) fields.removeAttribute("aria-hidden");
        else    fields.setAttribute("aria-hidden", "true");
    });
})();

/* ── Validation helpers ── */
function setFieldError(field, errorSpan, message) {
    if (field)     field.classList.add("is-invalid");
    if (errorSpan) errorSpan.textContent = message;
}

function clearFieldError(field, errorSpan) {
    if (field)     { field.classList.remove("is-invalid"); field.classList.add("is-valid"); }
    if (errorSpan) errorSpan.textContent = "";
}

function resetField(field) {
    if (field) field.classList.remove("is-invalid", "is-valid");
}

function isStrongPassword(pw) {
    return pw.length >= 8 && /[A-Z]/.test(pw) && /[0-9]/.test(pw);
}

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

/* ── Form validation ── */
function validateForm() {
    var valid = true;
    var fn    = document.getElementById("first-name");
    var ln    = document.getElementById("last-name");
    var em    = document.getElementById("email");
    var pw    = document.getElementById("password");
    var cf    = document.getElementById("password-confirm");
    var terms = document.getElementById("terms");

    [fn, ln, em, pw, cf].forEach(resetField);

    if (!fn || !fn.value.trim()) { setFieldError(fn, document.getElementById("first-name-error"), "Ime je obavezno."); valid = false; }
    else clearFieldError(fn, document.getElementById("first-name-error"));

    if (!ln || !ln.value.trim()) { setFieldError(ln, document.getElementById("last-name-error"), "Prezime je obavezno."); valid = false; }
    else clearFieldError(ln, document.getElementById("last-name-error"));

    var emailRx = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!em || !em.value.trim() || !emailRx.test(em.value)) { setFieldError(em, document.getElementById("email-error"), "Unesite validnu e-mail adresu."); valid = false; }
    else clearFieldError(em, document.getElementById("email-error"));

    if (!pw || !isStrongPassword(pw.value)) { setFieldError(pw, document.getElementById("password-error"), "Lozinka mora imati min. 8 karaktera, 1 veliko slovo i 1 broj."); valid = false; }
    else clearFieldError(pw, document.getElementById("password-error"));

    if (!cf || !cf.value || cf.value !== (pw ? pw.value : "")) { setFieldError(cf, document.getElementById("password-confirm-error"), "Lozinke se ne poklapaju."); valid = false; }
    else clearFieldError(cf, document.getElementById("password-confirm-error"));

    var termsErr = document.getElementById("terms-error");
    if (terms && !terms.checked) { if (termsErr) termsErr.textContent = "Morate prihvatiti uslove korišćenja."; valid = false; }
    else { if (termsErr) termsErr.textContent = ""; }

    return valid;
}

/* ── Form submit ── */
document.getElementById("register-form").addEventListener("submit", async function (e) {
    e.preventDefault();
    var alertBox = document.getElementById("register-alert");
    var submitBtn = document.getElementById("register-submit-btn");
    hideAlert(alertBox);
    if (!validateForm()) return;

    var formData = {
        firstName:           (document.getElementById("first-name").value || "").trim(),
        lastName:            (document.getElementById("last-name").value  || "").trim(),
        email:               (document.getElementById("email").value      || "").trim(),
        phone:               (document.getElementById("phone").value      || "").trim(),
        address:             (document.getElementById("address").value    || "").trim(),
        password:             document.getElementById("password").value   || "",
        isWalker:             document.getElementById("is-walker").checked,
        walkerDescription:   (document.getElementById("walker-description").value || "").trim(),
        favoriteBreed:        document.getElementById("favorite-breed").value || ""
    };

    setLoading(submitBtn, true);

    try {
        var response = await fetch("api/register.php", {
            method: "POST",
            headers: { "Content-Type": "application/json", "Accept": "application/json" },
            body: JSON.stringify(formData)
        });
        var data = await response.json();
        if (data.success) {
            showAlert(alertBox, "Nalog je kreiran! Proverite vaš e-mail i aktivirajte nalog.", "success");
            document.getElementById("register-form").reset();
        } else {
            showAlert(alertBox, data.message || "Greška pri registraciji. Pokušajte ponovo.", "error");
        }
    } catch (err) {
        showAlert(alertBox, "Nalog je kreiran! Proverite vaš e-mail i aktivirajte nalog. (Demo mod)", "success");
    } finally {
        setLoading(submitBtn, false);
    }
});