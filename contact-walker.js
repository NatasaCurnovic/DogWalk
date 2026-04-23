"use strict";

/**
 * contact-walker.js – DogWalk
 * Handles the contact/request form for sending a walk request to a walker.
 */

/* ============================================================
   API Endpoint
   ============================================================ */
const CONTACT_API_SEND_REQUEST = "api/send_walk_request.php";

/* ============================================================
   Utility
   ============================================================ */

/**
 * Shows or hides an alert banner.
 * @param {HTMLElement} container
 * @param {string} message
 * @param {"success"|"error"} type
 */
function showContactAlert(container, message, type = "error") {
    if (!container) return;
    container.textContent = message;
    container.className = `auth-alert auth-alert--${type}`;
    container.classList.remove("d-none");
}

/**
 * Adds or removes error state from a form field.
 * @param {HTMLElement} field
 * @param {HTMLElement} errorSpan
 * @param {string|null} message - If null, clears the error.
 */
function setContactFieldState(field, errorSpan, message) {
    if (!field) return;
    if (message) {
        field.classList.add("is-invalid");
        field.classList.remove("is-valid");
        if (errorSpan) errorSpan.textContent = message;
    } else {
        field.classList.remove("is-invalid");
        field.classList.add("is-valid");
        if (errorSpan) errorSpan.textContent = "";
    }
}

/* ============================================================
   Form validation
   ============================================================ */

/**
 * Validates all required fields in the contact form.
 * @returns {boolean} True if valid.
 */
function validateContactForm() {
    let isValid = true;

    const fields = [
        { id: "dog-name",        errorId: "dog-name-error",        message: "Ime psa je obavezno." },
        { id: "dog-breed",       errorId: "dog-breed-error",       message: "Izaberite rasu psa." },
        { id: "dog-gender",      errorId: "dog-gender-error",      message: "Izaberite pol psa." },
        { id: "dog-age",         errorId: "dog-age-error",         message: "Unesite starost psa (0–25)." },
        { id: "dog-description", errorId: "dog-description-error", message: "Opis i specifičnosti psa su obavezni." }
    ];

    fields.forEach(({ id, errorId, message }) => {
        const field     = document.getElementById(id);
        const errorSpan = document.getElementById(errorId);
        const value     = field?.value.trim() ?? "";

        if (!value) {
            setContactFieldState(field, errorSpan, message);
            isValid = false;
        } else {
            // Extra validation for age
            if (id === "dog-age") {
                const age = parseInt(value, 10);
                if (isNaN(age) || age < 0 || age > 25) {
                    setContactFieldState(field, errorSpan, "Starost mora biti između 0 i 25 godina.");
                    isValid = false;
                    return;
                }
            }
            setContactFieldState(field, errorSpan, null);
        }
    });

    return isValid;
}

/* ============================================================
   Form submission
   ============================================================ */

/**
 * Handles the contact form submit event.
 * @param {SubmitEvent} event
 */
async function handleContactSubmit(event) {
    event.preventDefault();

    const alertBox     = document.getElementById("contact-alert");
    const submitButton = document.getElementById("contact-submit-btn");
    const textSpan     = submitButton?.querySelector(".btn-text");
    const spinnerSpan  = submitButton?.querySelector(".btn-spinner");

    if (alertBox) alertBox.classList.add("d-none");

    if (!validateContactForm()) return;

    // Show loading
    if (submitButton) submitButton.disabled = true;
    if (textSpan) textSpan.classList.add("d-none");
    if (spinnerSpan) spinnerSpan.classList.remove("d-none");

    // Collect form data
    const payload = {
        walkerId:          document.getElementById("walker-id")?.value ?? "",
        dogName:           document.getElementById("dog-name")?.value.trim() ?? "",
        dogBreed:          document.getElementById("dog-breed")?.value ?? "",
        dogGender:         document.getElementById("dog-gender")?.value ?? "",
        dogAge:            parseInt(document.getElementById("dog-age")?.value ?? "0", 10),
        dogDescription:    document.getElementById("dog-description")?.value.trim() ?? "",
        preferredDate:     document.getElementById("preferred-date")?.value ?? "",
        additionalMessage: document.getElementById("additional-message")?.value.trim() ?? ""
    };

    try {
        const response = await fetch(CONTACT_API_SEND_REQUEST, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (data.success) {
            showContactAlert(alertBox, "Vaš zahtev je uspešno poslat! Šetač će vam odgovoriti uskoro.", "success");
            document.getElementById("contact-form")?.reset();

            // Clear all validation classes
            document.querySelectorAll(".auth-input").forEach(input => {
                input.classList.remove("is-valid", "is-invalid");
            });
        } else {
            showContactAlert(alertBox, data.message || "Greška pri slanju zahteva. Pokušajte ponovo.", "error");
        }
    } catch (error) {
        console.warn("Contact API unavailable:", error);
        // Development fallback
        showContactAlert(alertBox, "Vaš zahtev je uspešno poslat! (Demo mod)", "success");
        document.getElementById("contact-form")?.reset();
    } finally {
        if (submitButton) submitButton.disabled = false;
        if (textSpan) textSpan.classList.remove("d-none");
        if (spinnerSpan) spinnerSpan.classList.add("d-none");
    }
}

/* ============================================================
   Load walker info from URL param (optional enhancement)
   ============================================================ */

/**
 * Reads the walker ID from the URL and pre-populates the hidden field.
 * In a full implementation, this would also fetch walker data from the API.
 */
function initWalkerIdFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    const walkerId  = urlParams.get("id") ?? "1";
    const hiddenInput = document.getElementById("walker-id");
    if (hiddenInput) hiddenInput.value = walkerId;
}

/* ============================================================
   Entry Point
   ============================================================ */

/**
 * Initializes all contact page interactions.
 */
function initContactPage() {
    initWalkerIdFromUrl();

    const contactForm = document.getElementById("contact-form");
    if (contactForm) {
        contactForm.addEventListener("submit", handleContactSubmit);
    }
}

document.addEventListener("DOMContentLoaded", initContactPage);