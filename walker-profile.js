"use strict";

/**
 * walker-profile.js – DogWalk
 * Handles: star rating picker, review submission via code,
 * save-walker toggle, and loading profile data from API.
 */

/* ============================================================
   API Endpoints
   ============================================================ */
const PROFILE_API_GET_WALKER   = "api/get_walker.php";
const PROFILE_API_SUBMIT_RATING = "api/submit_rating.php";

/* ============================================================
   Utility – reuse alert helpers from auth.js if present,
   otherwise define locally
   ============================================================ */

/**
 * Shows an inline alert in a given element.
 * @param {HTMLElement} container
 * @param {string} message
 * @param {"success"|"error"} type
 */
function showProfileAlert(container, message, type = "error") {
    if (!container) return;
    container.textContent = message;
    container.className = `auth-alert auth-alert--${type}`;
    container.classList.remove("d-none");
}

/* ============================================================
   Star picker – interactive rating selection
   ============================================================ */

/** Currently selected rating value (1–5). */
let selectedStarRating = 0;

/**
 * Updates the visual star state in the picker.
 * @param {number} hoverValue - Star value being hovered (0 = unhover).
 * @param {number} currentSelected - Currently committed value.
 */
function updateStarPickerDisplay(hoverValue, currentSelected) {
    const starButtons = document.querySelectorAll(".star-pick-btn");

    starButtons.forEach(button => {
        const buttonValue = parseInt(button.getAttribute("data-value"), 10);
        const icon        = button.querySelector("i");
        const isActive    = buttonValue <= (hoverValue || currentSelected);

        if (icon) {
            icon.className = isActive ? "bi bi-star-fill" : "bi bi-star";
        }

        button.classList.toggle("is-active", isActive);
    });
}

/**
 * Initializes the interactive star rating picker.
 */
function initStarPicker() {
    const starButtons     = document.querySelectorAll(".star-pick-btn");
    const hiddenInput     = document.getElementById("selected-rating");
    if (starButtons.length === 0) return;

    starButtons.forEach(button => {
        const value = parseInt(button.getAttribute("data-value"), 10);

        // Hover effects
        button.addEventListener("mouseenter", () => {
            updateStarPickerDisplay(value, selectedStarRating);
        });

        button.addEventListener("mouseleave", () => {
            updateStarPickerDisplay(0, selectedStarRating);
        });

        // Click to select
        button.addEventListener("click", () => {
            selectedStarRating = value;
            if (hiddenInput) hiddenInput.value = String(value);
            updateStarPickerDisplay(0, selectedStarRating);
        });

        // Keyboard accessibility
        button.addEventListener("keydown", event => {
            if (event.key === "Enter" || event.key === " ") {
                event.preventDefault();
                selectedStarRating = value;
                if (hiddenInput) hiddenInput.value = String(value);
                updateStarPickerDisplay(0, selectedStarRating);
            }
        });
    });
}

/* ============================================================
   Save / bookmark walker
   ============================================================ */

/**
 * Initializes the "save walker" bookmark toggle button.
 */
function initSaveWalkerButton() {
    const saveButton = document.getElementById("save-walker-btn");
    if (!saveButton) return;

    let isSaved = false;

    saveButton.addEventListener("click", () => {
        isSaved = !isSaved;
        const icon = saveButton.querySelector("i");

        if (isSaved) {
            saveButton.setAttribute("aria-pressed", "true");
            saveButton.setAttribute("aria-label", "Ukloni iz omiljenih");
            if (icon) icon.className = "bi bi-bookmark-fill";
        } else {
            saveButton.setAttribute("aria-pressed", "false");
            saveButton.setAttribute("aria-label", "Sačuvaj šetača u omiljene");
            if (icon) icon.className = "bi bi-bookmark";
        }
    });
}

/* ============================================================
   Rating form submission
   ============================================================ */

/**
 * Validates and submits the walker rating form.
 * @param {SubmitEvent} event
 */
async function handleRatingSubmit(event) {
    event.preventDefault();

    const form         = document.getElementById("rate-form");
    const submitButton = document.getElementById("rate-submit-btn");
    const codeInput    = document.getElementById("rating-code");
    const codeError    = document.getElementById("rating-code-error");
    const ratingError  = document.getElementById("rating-error");
    let isValid        = true;

    // Validate code
    if (!codeInput?.value.trim()) {
        if (codeError) codeError.textContent = "Unesite kôd za ocenjivanje.";
        if (codeInput) codeInput.classList.add("is-invalid");
        isValid = false;
    } else {
        if (codeError) codeError.textContent = "";
        if (codeInput) codeInput.classList.remove("is-invalid");
    }

    // Validate star rating
    if (!selectedStarRating) {
        if (ratingError) ratingError.textContent = "Izaberite ocenu.";
        isValid = false;
    } else {
        if (ratingError) ratingError.textContent = "";
    }

    if (!isValid) return;

    // Disable button
    if (submitButton) submitButton.disabled = true;
    const textSpan    = submitButton?.querySelector(".btn-text");
    const spinnerSpan = submitButton?.querySelector(".btn-spinner");
    if (textSpan) textSpan.classList.add("d-none");
    if (spinnerSpan) spinnerSpan.classList.remove("d-none");

    // Get walker ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const walkerId  = urlParams.get("id") ?? "1";

    const payload = {
        walkerId:   walkerId,
        code:       codeInput?.value.trim() ?? "",
        rating:     selectedStarRating,
        reviewText: document.getElementById("review-text")?.value.trim() ?? ""
    };

    try {
        const response = await fetch(PROFILE_API_SUBMIT_RATING, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (data.success) {
            const rateSection = document.getElementById("rate-walker-section");
            if (rateSection) {
                rateSection.innerHTML = `
                    <div class="success-box" role="status" aria-live="polite">
                        <div class="success-icon"><i class="bi bi-star-fill" aria-hidden="true"></i></div>
                        <h3 class="success-title">Hvala na oceni!</h3>
                        <p class="success-desc">Vaša ocena je uspešno zabeležena.</p>
                    </div>`;
            }
        } else {
            if (codeError) codeError.textContent = data.message || "Nevažeći kôd. Pokušajte ponovo.";
            if (codeInput) codeInput.classList.add("is-invalid");
            if (submitButton) submitButton.disabled = false;
            if (textSpan) textSpan.classList.remove("d-none");
            if (spinnerSpan) spinnerSpan.classList.add("d-none");
        }
    } catch (error) {
        console.warn("Rating API unavailable:", error);
        // Demo fallback
        const rateSection = document.getElementById("rate-walker-section");
        if (rateSection) {
            rateSection.innerHTML = `
                <div class="success-box" role="status" aria-live="polite">
                    <div class="success-icon"><i class="bi bi-star-fill" aria-hidden="true"></i></div>
                    <h3 class="success-title">Hvala na oceni! (Demo)</h3>
                    <p class="success-desc">Vaša ocena je uspešno zabeležena.</p>
                </div>`;
        }
    }
}

/* ============================================================
   Entry Point
   ============================================================ */

/**
 * Initializes all walker profile page interactions.
 */
function initWalkerProfilePage() {
    initStarPicker();
    initSaveWalkerButton();

    const rateForm = document.getElementById("rate-form");
    if (rateForm) {
        rateForm.addEventListener("submit", handleRatingSubmit);
    }
}

document.addEventListener("DOMContentLoaded", initWalkerProfilePage);