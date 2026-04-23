"use strict";

/**
 * auth.js – DogWalk
 * Handles: registration form, login form, forgot-password form.
 * All API calls use Fetch API (JSON).
 * Client-side validation runs before any network request.
 */

/* ============================================================
   API Endpoints
   ============================================================ */
const AUTH_API_REGISTER       = "api/register.php";
const AUTH_API_LOGIN          = "api/login.php";
const AUTH_API_FORGOT_PASSWORD = "api/forgot_password.php";

/* ============================================================
   Utility – Show / hide inline alert banner
   ============================================================ */

/**
 * Shows a styled alert banner inside a container.
 * @param {HTMLElement} container - The alert element.
 * @param {string} message - Message text to display.
 * @param {"success"|"error"} type - Visual style.
 */
function showAlert(container, message, type = "error") {
    if (!container) return;
    container.textContent = message;
    container.className = `auth-alert auth-alert--${type}`;
    container.classList.remove("d-none");
}

/**
 * Hides an alert banner.
 * @param {HTMLElement} container - The alert element.
 */
function hideAlert(container) {
    if (!container) return;
    container.classList.add("d-none");
    container.textContent = "";
}

/* ============================================================
   Utility – Field validation helpers
   ============================================================ */

/**
 * Marks a field as invalid and shows an error message.
 * @param {HTMLElement} field - Input element.
 * @param {HTMLElement} errorSpan - Error message container.
 * @param {string} message - Error message text.
 */
function setFieldError(field, errorSpan, message) {
    if (field) field.classList.add("is-invalid");
    if (errorSpan) errorSpan.textContent = message;
}

/**
 * Clears error state from a field.
 * @param {HTMLElement} field - Input element.
 * @param {HTMLElement} errorSpan - Error message container.
 */
function clearFieldError(field, errorSpan) {
    if (field) {
        field.classList.remove("is-invalid");
        field.classList.add("is-valid");
    }
    if (errorSpan) errorSpan.textContent = "";
}

/**
 * Removes all validation classes from a field (neutral state).
 * @param {HTMLElement} field - Input element.
 */
function resetFieldState(field) {
    if (!field) return;
    field.classList.remove("is-invalid", "is-valid");
}

/* ============================================================
   Utility – Password strength validator
   ============================================================ */

/**
 * Checks whether a password meets minimum requirements.
 * Minimum: 8 chars, 1 uppercase letter, 1 digit.
 * @param {string} password - Raw password string.
 * @returns {boolean}
 */
function isPasswordStrong(password) {
    if (password.length < 8) return false;
    if (!/[A-Z]/.test(password)) return false;
    if (!/[0-9]/.test(password)) return false;
    return true;
}

/* ============================================================
   Utility – Button loading state
   ============================================================ */

/**
 * Toggles the loading state of a submit button.
 * @param {HTMLButtonElement} button - Submit button element.
 * @param {boolean} isLoading - Whether to show spinner.
 */
function setButtonLoading(button, isLoading) {
    if (!button) return;
    const textSpan    = button.querySelector(".btn-text");
    const spinnerSpan = button.querySelector(".btn-spinner");

    button.disabled = isLoading;
    if (textSpan) textSpan.classList.toggle("d-none", isLoading);
    if (spinnerSpan) spinnerSpan.classList.toggle("d-none", !isLoading);
}

/* ============================================================
   Password visibility toggle
   ============================================================ */

/**
 * Initializes all "show/hide password" toggle buttons on the page.
 */
function initPasswordToggles() {
    document.querySelectorAll(".toggle-password").forEach(button => {
        button.addEventListener("click", () => {
            const targetId = button.getAttribute("data-target");
            const input    = document.getElementById(targetId);
            if (!input) return;

            const isHidden = input.type === "password";
            input.type = isHidden ? "text" : "password";

            const icon = button.querySelector("i");
            if (icon) {
                icon.className = isHidden ? "bi bi-eye-slash" : "bi bi-eye";
            }
        });
    });
}

/* ============================================================
   Mobile nav (shared with main.js pattern)
   ============================================================ */

/**
 * Initializes the mobile hamburger menu toggle.
 */
function initMobileNav() {
    const toggleButton = document.getElementById("navToggle");
    const navMenu      = document.getElementById("navMenu");
    if (!toggleButton || !navMenu) return;

    toggleButton.addEventListener("click", () => {
        const isOpen = navMenu.classList.toggle("is-open");
        toggleButton.setAttribute("aria-expanded", String(isOpen));
    });
}

/* ============================================================
   Walker toggle (register page)
   ============================================================ */

/**
 * Initializes the "register as walker" toggle switch.
 * Shows/hides additional walker fields.
 */
function initWalkerToggle() {
    const walkerSwitch = document.getElementById("is-walker");
    const walkerFields = document.getElementById("walker-fields");
    if (!walkerSwitch || !walkerFields) return;

    walkerSwitch.addEventListener("change", () => {
        const isWalker = walkerSwitch.checked;
        walkerSwitch.setAttribute("aria-checked", String(isWalker));

        if (isWalker) {
            walkerFields.classList.remove("d-none");
            walkerFields.removeAttribute("aria-hidden");
        } else {
            walkerFields.classList.add("d-none");
            walkerFields.setAttribute("aria-hidden", "true");
        }
    });
}

/* ============================================================
   Register form
   ============================================================ */

/**
 * Validates the registration form fields.
 * @returns {boolean} True if all fields are valid.
 */
function validateRegisterForm() {
    let isValid = true;

    const firstNameInput  = document.getElementById("first-name");
    const lastNameInput   = document.getElementById("last-name");
    const emailInput      = document.getElementById("email");
    const passwordInput   = document.getElementById("password");
    const confirmInput    = document.getElementById("password-confirm");
    const termsInput      = document.getElementById("terms");

    const firstNameError  = document.getElementById("first-name-error");
    const lastNameError   = document.getElementById("last-name-error");
    const emailError      = document.getElementById("email-error");
    const passwordError   = document.getElementById("password-error");
    const confirmError    = document.getElementById("password-confirm-error");
    const termsError      = document.getElementById("terms-error");

    // Reset all states
    [firstNameInput, lastNameInput, emailInput, passwordInput, confirmInput].forEach(resetFieldState);

    // First name
    if (!firstNameInput?.value.trim()) {
        setFieldError(firstNameInput, firstNameError, "Ime je obavezno.");
        isValid = false;
    } else {
        clearFieldError(firstNameInput, firstNameError);
    }

    // Last name
    if (!lastNameInput?.value.trim()) {
        setFieldError(lastNameInput, lastNameError, "Prezime je obavezno.");
        isValid = false;
    } else {
        clearFieldError(lastNameInput, lastNameError);
    }

    // Email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailInput?.value.trim() || !emailRegex.test(emailInput.value)) {
        setFieldError(emailInput, emailError, "Unesite validnu e-mail adresu.");
        isValid = false;
    } else {
        clearFieldError(emailInput, emailError);
    }

    // Password strength
    if (!passwordInput?.value || !isPasswordStrong(passwordInput.value)) {
        setFieldError(passwordInput, passwordError, "Lozinka mora imati min. 8 karaktera, 1 veliko slovo i 1 broj.");
        isValid = false;
    } else {
        clearFieldError(passwordInput, passwordError);
    }

    // Password confirmation
    if (!confirmInput?.value || confirmInput.value !== passwordInput?.value) {
        setFieldError(confirmInput, confirmError, "Lozinke se ne poklapaju.");
        isValid = false;
    } else {
        clearFieldError(confirmInput, confirmError);
    }

    // Terms checkbox
    if (termsInput && !termsInput.checked) {
        if (termsError) termsError.textContent = "Morate prihvatiti uslove korišćenja.";
        isValid = false;
    } else {
        if (termsError) termsError.textContent = "";
    }

    return isValid;
}

/**
 * Handles the registration form submit event.
 * Validates, then calls the API (or shows fallback message in development).
 * @param {SubmitEvent} event
 */
async function handleRegisterSubmit(event) {
    event.preventDefault();

    const alertBox    = document.getElementById("register-alert");
    const submitButton = document.getElementById("register-submit-btn");
    hideAlert(alertBox);

    if (!validateRegisterForm()) return;

    const formData = {
        firstName:           document.getElementById("first-name")?.value.trim() ?? "",
        lastName:            document.getElementById("last-name")?.value.trim() ?? "",
        email:               document.getElementById("email")?.value.trim() ?? "",
        phone:               document.getElementById("phone")?.value.trim() ?? "",
        address:             document.getElementById("address")?.value.trim() ?? "",
        password:            document.getElementById("password")?.value ?? "",
        isWalker:            document.getElementById("is-walker")?.checked ?? false,
        walkerDescription:   document.getElementById("walker-description")?.value.trim() ?? "",
        favoriteBreed:       document.getElementById("favorite-breed")?.value ?? ""
    };

    setButtonLoading(submitButton, true);

    try {
        const response = await fetch(AUTH_API_REGISTER, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (data.success) {
            showAlert(alertBox, "Nalog je kreiran! Proverite vaš e-mail i aktivirajte nalog.", "success");
            document.getElementById("register-form")?.reset();
        } else {
            showAlert(alertBox, data.message || "Greška pri registraciji. Pokušajte ponovo.", "error");
        }
    } catch (error) {
        console.warn("Register API unavailable:", error);
        // Development fallback – simulate success
        showAlert(alertBox, "Nalog je kreiran! Proverite vaš e-mail i aktivirajte nalog. (Demo mod)", "success");
    } finally {
        setButtonLoading(submitButton, false);
    }
}

/* ============================================================
   Login form
   ============================================================ */

/**
 * Validates the login form fields.
 * @returns {boolean} True if valid.
 */
function validateLoginForm() {
    let isValid = true;

    const emailInput    = document.getElementById("email");
    const passwordInput = document.getElementById("password");
    const emailError    = document.getElementById("email-error");
    const passwordError = document.getElementById("password-error");

    resetFieldState(emailInput);
    resetFieldState(passwordInput);

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailInput?.value.trim() || !emailRegex.test(emailInput.value)) {
        setFieldError(emailInput, emailError, "Unesite validnu e-mail adresu.");
        isValid = false;
    } else {
        clearFieldError(emailInput, emailError);
    }

    if (!passwordInput?.value) {
        setFieldError(passwordInput, passwordError, "Lozinka je obavezna.");
        isValid = false;
    } else {
        clearFieldError(passwordInput, passwordError);
    }

    return isValid;
}

/**
 * Handles the login form submit event.
 * @param {SubmitEvent} event
 */
async function handleLoginSubmit(event) {
    event.preventDefault();

    const alertBox     = document.getElementById("login-alert");
    const submitButton = document.getElementById("login-submit-btn");
    hideAlert(alertBox);

    if (!validateLoginForm()) return;

    const formData = {
        email:      document.getElementById("email")?.value.trim() ?? "",
        password:   document.getElementById("password")?.value ?? "",
        rememberMe: document.getElementById("remember-me")?.checked ?? false
    };

    setButtonLoading(submitButton, true);

    try {
        const response = await fetch(AUTH_API_LOGIN, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (data.success) {
            // Redirect based on user role
            const redirectUrl = data.role === "admin"
                ? "admin.html"
                : (data.role === "walker" ? "walker-dashboard.html" : "index.html");
            window.location.href = redirectUrl;
        } else {
            showAlert(alertBox, data.message || "Pogrešan e-mail ili lozinka.", "error");
        }
    } catch (error) {
        console.warn("Login API unavailable:", error);
        // Development fallback
        showAlert(alertBox, "API nije dostupan. Preusmeravanje... (Demo mod)", "success");
        setTimeout(() => { window.location.href = "walker-dashboard.html"; }, 1500);
    } finally {
        setButtonLoading(submitButton, false);
    }
}

/* ============================================================
   Forgot password form
   ============================================================ */

/**
 * Handles the forgot-password form submit.
 * @param {SubmitEvent} event
 */
async function handleForgotPasswordSubmit(event) {
    event.preventDefault();

    const alertBox     = document.getElementById("forgot-alert");
    const submitButton = document.getElementById("forgot-submit-btn");
    const emailInput   = document.getElementById("email");
    const emailError   = document.getElementById("email-error");
    const stepRequest  = document.getElementById("forgot-step-request");
    const stepSuccess  = document.getElementById("forgot-step-success");

    hideAlert(alertBox);
    resetFieldState(emailInput);

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailInput?.value.trim() || !emailRegex.test(emailInput.value)) {
        setFieldError(emailInput, emailError, "Unesite validnu e-mail adresu.");
        return;
    }
    clearFieldError(emailInput, emailError);

    setButtonLoading(submitButton, true);

    try {
        const response = await fetch(AUTH_API_FORGOT_PASSWORD, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify({ email: emailInput.value.trim() })
        });

        const data = await response.json();

        if (data.success || response.ok) {
            if (stepRequest) stepRequest.classList.add("d-none");
            if (stepSuccess) stepSuccess.classList.remove("d-none");
        } else {
            showAlert(alertBox, data.message || "Greška. Pokušajte ponovo.", "error");
        }
    } catch (error) {
        console.warn("Forgot password API unavailable:", error);
        // Development fallback – show success step
        if (stepRequest) stepRequest.classList.add("d-none");
        if (stepSuccess) stepSuccess.classList.remove("d-none");
    } finally {
        setButtonLoading(submitButton, false);
    }
}

/* ============================================================
   Entry Point – bind correct form based on page
   ============================================================ */

/**
 * Detects which auth form is present and wires up event listeners.
 */
function initAuthPage() {
    initMobileNav();
    initPasswordToggles();
    initWalkerToggle();

    const registerForm       = document.getElementById("register-form");
    const loginForm          = document.getElementById("login-form");
    const forgotPasswordForm = document.getElementById("forgot-form");

    if (registerForm) {
        registerForm.addEventListener("submit", handleRegisterSubmit);
    }

    if (loginForm) {
        loginForm.addEventListener("submit", handleLoginSubmit);
    }

    if (forgotPasswordForm) {
        forgotPasswordForm.addEventListener("submit", handleForgotPasswordSubmit);
    }
}

document.addEventListener("DOMContentLoaded", initAuthPage);