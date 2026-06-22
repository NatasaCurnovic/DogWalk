"use strict";

const togglePw= document.getElementById("togglePw");
const pwInput= document.getElementById("password");
const eyeIcon= document.getElementById("eyeIcon");
const loginForm= document.getElementById("loginForm");
const loginBtn= document.getElementById("loginBtn");

if (togglePw && pwInput && eyeIcon) {
    togglePw.addEventListener("click", () => {
        const visible = pwInput.type === "text";
        pwInput.type = visible ? "password" : "text";
        eyeIcon.className = visible ? "bi bi-eye" : "bi bi-eye-slash";
    });
}

if (loginForm) {
    loginForm.addEventListener("submit", (event) => {
        const email = document.getElementById("email");
        const password = document.getElementById("password");
        let valid = true;

        if (!email.value || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
            email.classList.add("is-invalid");
            valid = false;
        } else {
            email.classList.remove("is-invalid");
        }

        if (!password.value) {
            password.classList.add("is-invalid");
            valid = false;
        } else {
            password.classList.remove("is-invalid");
        }

        if (!valid) {
            event.preventDefault();
            event.stopPropagation();
            return;
        }

        if (loginBtn) {
            loginBtn.textContent = "Prijavljivanje...";
        }
    });
}
