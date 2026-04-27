/* ── Mobile nav ── */
const navToggle = document.getElementById("navToggle");
const navMenu   = document.getElementById("navMenu");
if (navToggle && navMenu) {
    navToggle.addEventListener("click", () => {
        const isOpen = navMenu.classList.toggle("is-open");
        navToggle.setAttribute("aria-expanded", String(isOpen));
    });
}

/* ── Password toggle ── */
document.querySelectorAll(".toggle-password").forEach(btn => {
    btn.addEventListener("click", () => {
        const input = document.getElementById(btn.getAttribute("data-target"));
        if (!input) return;
        const hidden = input.type === "password";
        input.type = hidden ? "text" : "password";
        const icon = btn.querySelector("i");
        if (icon) icon.className = hidden ? "bi bi-eye-slash" : "bi bi-eye";
    });
});

/* ── Alert helpers ── */
function showAlert(el, msg, type = "error") {
    if (!el) return;
    el.textContent = msg;
    el.className = `auth-alert auth-alert--${type}`;
    el.classList.remove("d-none");
}
function hideAlert(el) {
    if (!el) return;
    el.classList.add("d-none");
    el.textContent = "";
}

/* ── Field helpers ── */
function setFieldError(field, span, msg) {
    if (field) field.classList.add("is-invalid");
    if (span)  span.textContent = msg;
}
function clearFieldError(field, span) {
    if (field) { field.classList.remove("is-invalid"); field.classList.add("is-valid"); }
    if (span)  span.textContent = "";
}
function resetField(field) {
    if (field) field.classList.remove("is-invalid", "is-valid");
}

/* ── Button loading ── */
function setLoading(btn, state) {
    if (!btn) return;
    btn.disabled = state;
    btn.querySelector(".btn-text")?.classList.toggle("d-none", state);
    btn.querySelector(".btn-spinner")?.classList.toggle("d-none", !state);
}

/* ── Validate ── */
function validateLogin() {
    const emailInput = document.getElementById("email");
    const passInput  = document.getElementById("password");
    const emailErr   = document.getElementById("email-error");
    const passErr    = document.getElementById("password-error");
    let valid = true;

    resetField(emailInput);
    resetField(passInput);

    if (!emailInput?.value.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value)) {
        setFieldError(emailInput, emailErr, "Unesite validnu e-mail adresu.");
        valid = false;
    } else {
        clearFieldError(emailInput, emailErr);
    }

    if (!passInput?.value) {
        setFieldError(passInput, passErr, "Lozinka je obavezna.");
        valid = false;
    } else {
        clearFieldError(passInput, passErr);
    }

    return valid;
}

/* ── Submit ── */
document.getElementById("login-form")?.addEventListener("submit", async (e) => {
    e.preventDefault();
    const alertBox = document.getElementById("login-alert");
    const submitBtn = document.getElementById("login-submit-btn");
    hideAlert(alertBox);
    if (!validateLogin()) return;

    const payload = {
        email:      document.getElementById("email").value.trim(),
        password:   document.getElementById("password").value,
        rememberMe: document.getElementById("remember-me").checked
    };

    setLoading(submitBtn, true);
    try {
        const res  = await fetch("api/login.php", {
            method: "POST",
            headers: { "Content-Type": "application/json", "Accept": "application/json" },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.success) {
            const url = data.role === "admin" ? "admin.html"
                : data.role === "walker" ? "walker-dashboard.html"
                    : "index.html";
            window.location.href = url;
        } else {
            showAlert(alertBox, data.message || "Pogrešan e-mail ili lozinka.", "error");
        }
    } catch {
        // Demo fallback
        showAlert(alertBox, "API nije dostupan. Preusmeravanje... (Demo mod)", "success");
        setTimeout(() => { window.location.href = "walker-dashboard.html"; }, 1500);
    } finally {
        setLoading(submitBtn, false);
    }
});