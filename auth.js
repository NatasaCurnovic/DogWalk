"use strict";

const AUTH_API_FORGOT_PASSWORD = "api/forgot_password.php";

async function handleForgotPasswordSubmit(event) {
    event.preventDefault();

    const emailInput = document.getElementById("email");
    const email = emailInput.value.trim();
    const alert = document.getElementById("forgot-alert");

    if (alert) {
        alert.classList.add("d-none");
        alert.textContent = "";
    }

    if (!email) {
        emailInput.classList.add("is-invalid");
        return;
    }

    const btnText = document.querySelector(".btn-text");
    const btnSpinner = document.querySelector(".btn-spinner");
    if (btnText) btnText.classList.add("d-none");
    if (btnSpinner) btnSpinner.classList.remove("d-none");

    try {
        const response = await fetch(AUTH_API_FORGOT_PASSWORD, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ email }) // send an email to PHP
        });

        if (!response.ok) {
            throw new Error("Server error: " + response.status);
        }

        const data = await response.json();

        if (!data.mail_sent) {
            if (alert) {
                alert.textContent = data.message || "Mail nije poslat. Proverite Mailtrap podesavanja.";
                alert.classList.remove("d-none");
            }
            return;
        }

        document.getElementById("forgot-step-request").classList.add("d-none");
        document.getElementById("forgot-step-success").classList.remove("d-none");
    } catch (error) {
        console.error(error);
        if (alert) {
            alert.textContent = "Greska pri slanju zahteva. Proverite podesavanja i pokusajte ponovo.";
            alert.classList.remove("d-none");
        }
    } finally {
        if (btnText) btnText.classList.remove("d-none");
        if (btnSpinner) btnSpinner.classList.add("d-none");
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("forgot-form");

    if (form) {
        form.addEventListener("submit", handleForgotPasswordSubmit);
    }
});
