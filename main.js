"use strict";

/**
 * main.js – DogWalk
 * Handles: search/filter, top-rated walkers, most-active walkers, mobile nav.
 * Uses Fetch API to communicate with PHP backend (JSON responses).
 * Falls back to demo data when API endpoints are unavailable.
 */

/* ============================================================
   Constants
   ============================================================ */
const API_TOP_RATED   = "api/get_top_walkers.php";
const API_MOST_ACTIVE = "api/get_most_active_walkers.php";
const API_SEARCH      = "api/search_walkers.php";
const MAX_HOME_WALKERS = 5;

/* ============================================================
   Demo / Fallback Data
   Used during development when PHP backend is not yet available.
   ============================================================ */
const demoWalkers = [
    {
        id: 1,
        fullName: "Ana Marković",
        location: "Beograd, Novi Beograd",
        rating: 4.5,
        description: "Volim pse svim srcem. 5+ godina iskustva.",
        isOnline: true,
        avatarUrl: "",
        coverUrl: "https://images.unsplash.com/photo-1548199973-03cce0bbc87b?w=400&q=70"
    },
    {
        id: 2,
        fullName: "Marko Petrović",
        location: "Novi Sad, Liman",
        rating: 4.5,
        description: "Veterinar u srcu, šetač po pozivu.",
        isOnline: false,
        avatarUrl: "",
        coverUrl: "https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=400&q=70"
    },
    {
        id: 3,
        fullName: "Mila Nikolić",
        location: "Subotica",
        rating: 4.0,
        description: "Volim pse svim srcem. 5+ godina iskustva.",
        isOnline: true,
        avatarUrl: "",
        coverUrl: "https://images.unsplash.com/photo-1601758124510-52d02ddb7cbd?w=400&q=70"
    },
    {
        id: 4,
        fullName: "Stefan Jović",
        location: "Niš, Medijana",
        rating: 5.0,
        description: "Iskusan šetač, brižan prema svakom ljubimcu.",
        isOnline: true,
        avatarUrl: "",
        coverUrl: "https://images.unsplash.com/photo-1530281700549-e82e7bf110d6?w=400&q=70"
    },
    {
        id: 5,
        fullName: "Jelena Đorđević",
        location: "Kragujevac",
        rating: 3.5,
        description: "Sertifikovani trener pasa i šetač sa iskustvom.",
        isOnline: false,
        avatarUrl: "",
        coverUrl: "https://images.unsplash.com/photo-1477884213360-7e9d7dcc1e48?w=400&q=70"
    }
];

/* ============================================================
   Utility – escapeHtml
   Prevents XSS by escaping special HTML characters.
   ============================================================ */

/**
 * Escapes special HTML characters in a string.
 * @param {string} rawText - The raw string to escape.
 * @returns {string} HTML-safe string.
 */
function escapeHtml(rawText) {
    if (typeof rawText !== "string") return "";
    const escapeMap = {
        "&":  "&amp;",
        "<":  "&lt;",
        ">":  "&gt;",
        '"':  "&quot;",
        "'":  "&#039;"
    };
    return rawText.replace(/[&<>"']/g, match => escapeMap[match]);
}

/* ============================================================
   Star Rating Builder
   ============================================================ */

/**
 * Builds an accessible star-rating widget HTML string.
 * @param {number} rating - Value from 0–5, supports .5 increments.
 * @returns {string} HTML string.
 */
function buildStarRating(rating) {
    let html = `<div class="star-rating" aria-label="Ocena: ${rating} od 5">`;

    for (let index = 1; index <= 5; index++) {
        if (rating >= index) {
            html += '<i class="bi bi-star-fill filled" aria-hidden="true"></i>';
        } else if (rating >= index - 0.5) {
            html += '<i class="bi bi-star-half half-filled" aria-hidden="true"></i>';
        } else {
            html += '<i class="bi bi-star" aria-hidden="true"></i>';
        }
    }

    html += '</div>';
    return html;
}

/* ============================================================
   Avatar Builder
   ============================================================ */

/**
 * Returns an avatar <img> or an initials placeholder element.
 * @param {Object} walker - Walker data object.
 * @returns {string} HTML string.
 */
function buildAvatarHtml(walker) {
    if (walker.avatarUrl) {
        return `<img
            src="${escapeHtml(walker.avatarUrl)}"
            alt="Profilna fotografija šetača ${escapeHtml(walker.fullName)}"
            class="walker-avatar"
            loading="lazy"
        >`;
    }

    const nameParts = walker.fullName.trim().split(" ");
    const initials = nameParts.map(part => part.charAt(0).toUpperCase()).slice(0, 2).join("");

    return `<div class="walker-avatar-placeholder" aria-hidden="true">${initials}</div>`;
}

/* ============================================================
   Walker Card Builder
   ============================================================ */

/**
 * Builds the HTML for a single walker card column.
 * @param {Object} walker - Walker data object.
 * @returns {string} HTML string.
 */
function buildWalkerCard(walker) {
    const avatarHtml = buildAvatarHtml(walker);
    const starsHtml  = buildStarRating(walker.rating);
    const onlineClass = walker.isOnline ? "" : "is-offline";
    const onlineLabel = walker.isOnline ? "Dostupan/a" : "Nedostupan/a";
    const coverSrc    = walker.coverUrl
        ? escapeHtml(walker.coverUrl)
        : "https://images.unsplash.com/photo-1601758124510-52d02ddb7cbd?w=400&q=70";

    return `
        <div class="col">
            <article class="walker-card" aria-label="Šetač: ${escapeHtml(walker.fullName)}">

                <div class="card-image-wrapper">
                    <img
                        src="${coverSrc}"
                        alt="Fotografija šetača ${escapeHtml(walker.fullName)}"
                        loading="lazy"
                    >
                    <span
                        class="online-badge ${onlineClass}"
                        title="${onlineLabel}"
                        aria-label="${onlineLabel}"
                    ></span>
                </div>

                <div class="card-body-inner">

                    <div class="walker-header">
                        ${avatarHtml}
                        <div>
                            <p class="walker-name">${escapeHtml(walker.fullName)}</p>
                            <p class="walker-location">
                                <i class="bi bi-geo-alt" aria-hidden="true"></i>
                                ${escapeHtml(walker.location)}
                            </p>
                        </div>
                    </div>

                    ${starsHtml}

                    <p class="walker-description">${escapeHtml(walker.description)}</p>

                    <div class="card-actions">
                        <a
                            href="walker-profile.php?id=${encodeURIComponent(walker.id)}"
                            class="btn-view-profile"
                            aria-label="Pogledaj profil – ${escapeHtml(walker.fullName)}"
                        >Pogledaj profil</a>
                        <a
                            href="contact-walker.php?id=${encodeURIComponent(walker.id)}"
                            class="btn-contact"
                            aria-label="Kontaktiraj – ${escapeHtml(walker.fullName)}"
                        >Kontakt</a>
                    </div>

                </div>
            </article>
        </div>
    `;
}

/* ============================================================
   Render Helpers
   ============================================================ */

/**
 * Renders walker cards into a container element.
 * @param {HTMLElement} container - Target DOM element.
 * @param {Array<Object>} walkers - Walker data array.
 * @param {number} limit - Maximum number of cards to render.
 */
function renderWalkerCards(container, walkers, limit = MAX_HOME_WALKERS) {
    if (!container) return;
    const visible = walkers.slice(0, limit);
    container.innerHTML = visible.map(buildWalkerCard).join("");
}

/**
 * Renders an animated skeleton loading state.
 * @param {HTMLElement} container - Target DOM element.
 * @param {number} count - Number of skeleton cards.
 */
function renderLoadingSkeleton(container, count = MAX_HOME_WALKERS) {
    if (!container) return;

    const skeletonCard = `
        <div class="col">
            <div class="walker-card skeleton-card">
                <div class="card-image-wrapper" style="background:#e8ede5;"></div>
                <div class="card-body-inner">
                    <div class="skeleton-line" style="width:70%;"></div>
                    <div class="skeleton-line" style="width:50%;"></div>
                    <div class="skeleton-line" style="width:85%;"></div>
                    <div class="skeleton-line" style="width:60%;margin-top:4px;"></div>
                </div>
            </div>
        </div>`;

    container.innerHTML = Array(count).fill(skeletonCard).join("");
}

/**
 * Renders an error message inside a container.
 * @param {HTMLElement} container - Target DOM element.
 * @param {string} message - Error message text.
 */
function renderErrorMessage(container, message) {
    if (!container) return;
    container.innerHTML = `
        <div class="col-12">
            <p class="text-center py-3" style="color:#6c757d;">
                <i class="bi bi-exclamation-triangle me-2" aria-hidden="true"></i>
                ${escapeHtml(message)}
            </p>
        </div>`;
}

/* ============================================================
   Fetch Helpers
   ============================================================ */

/**
 * Fetches JSON data from a given API endpoint.
 * @param {string} endpoint - API URL.
 * @param {Object|null} bodyData - Optional POST body (sends as JSON).
 * @returns {Promise<Array|Object>} Parsed JSON response.
 */
async function fetchJson(endpoint, bodyData = null) {
    const options = {
        method: bodyData ? "POST" : "GET",
        headers: { "Accept": "application/json" }
    };

    if (bodyData) {
        options.headers["Content-Type"] = "application/json";
        options.body = JSON.stringify(bodyData);
    }

    const response = await fetch(endpoint, options);

    if (!response.ok) {
        throw new Error(`HTTP ${response.status} – ${endpoint}`);
    }

    return response.json();
}

/* ============================================================
   Top Rated & Most Active Loaders
   ============================================================ */

/**
 * Loads and renders the top-rated walkers section.
 */
async function loadTopRatedWalkers() {
    const container = document.getElementById("top-rated-list");
    renderLoadingSkeleton(container);

    try {
        const walkers = await fetchJson(API_TOP_RATED);
        renderWalkerCards(container, walkers);
    } catch (error) {
        console.warn("Top-rated walkers API unavailable, using demo data.", error);
        renderWalkerCards(container, demoWalkers);
    }
}

/**
 * Loads and renders the most-active walkers section.
 */
async function loadMostActiveWalkers() {
    const container = document.getElementById("most-active-list");
    renderLoadingSkeleton(container);

    try {
        const walkers = await fetchJson(API_MOST_ACTIVE);
        renderWalkerCards(container, walkers);
    } catch (error) {
        console.warn("Most-active walkers API unavailable, using demo data.", error);
        // Slightly shuffle demo data to differentiate from top-rated list visually
        const shuffled = [...demoWalkers].reverse();
        renderWalkerCards(container, shuffled);
    }
}

/* ============================================================
   Search & Filter
   ============================================================ */

/**
 * Collects all current search filter values from the DOM.
 * @returns {{search: string, city: string, breed: string, rating: string}}
 */
function getSearchFilters() {
    const search = document.getElementById("search-input")?.value.trim() ?? "";
    const city   = document.getElementById("city-filter")?.value.trim() ?? "";
    const breed  = document.getElementById("breed-filter")?.value ?? "";
    const rating = document.getElementById("rating-filter")?.value ?? "";

    return { search, city, breed, rating };
}

/**
 * Validates that at least one search filter has a value.
 * @param {Object} filters - Filter values object.
 * @returns {boolean} True if valid.
 */
function hasActiveFilter(filters) {
    return Object.values(filters).some(value => value !== "");
}

/**
 * Renders search results into the results container.
 * @param {Array<Object>} walkers - Matched walker objects.
 * @param {Object} filters - Active filter values (for empty-state message).
 */
function renderSearchResults(walkers, filters) {
    const container = document.getElementById("search-results-container");
    if (!container) return;

    if (walkers.length === 0) {
        container.innerHTML = `
            <p class="no-results-msg">
                <i class="bi bi-search me-2" aria-hidden="true"></i>
                Nema šetača koji odgovaraju vašoj pretrazi. Pokušajte sa drugim filterima.
            </p>`;
        return;
    }

    const titleHtml = `<p class="results-title">Pronađeno: ${walkers.length} šetač(a)</p>`;
    const cardsHtml = `
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-5 g-4">
            ${walkers.map(buildWalkerCard).join("")}
        </div>`;

    container.innerHTML = titleHtml + cardsHtml;
}

/**
 * Handles the search button click:
 * validates filters, calls API, renders results.
 */
async function handleSearch() {
    const filters = getSearchFilters();
    const resultContainer = document.getElementById("search-results-container");

    if (!hasActiveFilter(filters)) {
        if (resultContainer) resultContainer.innerHTML = "";
        return;
    }

    // Show skeleton in results area
    if (resultContainer) {
        resultContainer.innerHTML = `
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-5 g-4" id="search-skeleton"></div>`;
        renderLoadingSkeleton(document.getElementById("search-skeleton"), 3);
    }

    try {
        const results = await fetchJson(API_SEARCH, filters);
        renderSearchResults(results, filters);
    } catch (error) {
        console.warn("Search API unavailable, filtering demo data locally.", error);

        // Local fallback: filter demoWalkers client-side
        const localResults = demoWalkers.filter(walker => {
            const nameMatch   = !filters.search || walker.fullName.toLowerCase().includes(filters.search.toLowerCase());
            const cityMatch   = !filters.city   || walker.location.toLowerCase().includes(filters.city.toLowerCase());
            const ratingMatch = !filters.rating || walker.rating >= parseFloat(filters.rating);
            return nameMatch && cityMatch && ratingMatch;
        });

        renderSearchResults(localResults, filters);
    }
}

/* ============================================================
   Mobile Navigation Toggle
   ============================================================ */

/**
 * Initializes the mobile hamburger menu toggle.
 */
function initMobileNav() {
    const toggleButton = document.getElementById("navToggle");
    const navMenu = document.getElementById("navMenu");

    if (!toggleButton || !navMenu) return;

    toggleButton.addEventListener("click", () => {
        const isOpen = navMenu.classList.toggle("is-open");
        toggleButton.setAttribute("aria-expanded", String(isOpen));
    });

    // Close nav when a link is clicked (single-page navigation)
    navMenu.querySelectorAll("a").forEach(link => {
        link.addEventListener("click", () => {
            navMenu.classList.remove("is-open");
            toggleButton.setAttribute("aria-expanded", "false");
        });
    });
}

/* ============================================================
   Event Listeners
   ============================================================ */

/**
 * Attaches all interactive event listeners.
 */
function initEventListeners() {
    // Search button
    const searchButton = document.getElementById("search-btn");
    if (searchButton) {
        searchButton.addEventListener("click", handleSearch);
    }

    // Allow Enter key inside search input
    const searchInput = document.getElementById("search-input");
    if (searchInput) {
        searchInput.addEventListener("keydown", event => {
            if (event.key === "Enter") handleSearch();
        });
    }

    // Trigger search automatically when filters change
    ["city-filter", "breed-filter", "rating-filter"].forEach(filterId => {
        const element = document.getElementById(filterId);
        if (element) {
            element.addEventListener("change", () => {
                const filters = getSearchFilters();
                if (hasActiveFilter(filters)) handleSearch();
            });
        }
    });
}

/* ============================================================
   Entry Point
   ============================================================ */

/**
 * Initializes the entire page once the DOM is ready.
 */
function initPage() {
    initMobileNav();
    initEventListeners();
    loadTopRatedWalkers();
    loadMostActiveWalkers();
}

document.addEventListener("DOMContentLoaded", initPage);
