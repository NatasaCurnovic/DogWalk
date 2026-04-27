"use strict";

var API_VERIFY  = "api/verify.php";
var API_RESEND  = "api/resend_verification.php";

/* ── Helpers ── */
function showState(id) {
    var states = ["state-loading", "state-success", "state-success-walker",
        "state-expired", "state-error", "state-already"];
    states.forEach(function (s) {
        var el = document.getElementById(s);
        if (el) el.classList.toggle("d-none", s !== id);
    });
}

function getParam(name) {
    var params = new URLSearchParams(window.location.search);
    return params.get(name);
}

/* ── Verify token on page load ── */
async function verifyToken(token) {
    try {
        var response = await fetch(API_VERIFY, {
            method: "POST",
            headers: { "Content-Type": "application/json", "Accept": "application/json" },
            body: JSON.stringify({ token: token })
        });
        var data = await response.json();

        if (data.success) {
            if (data.role === "pending_walker") {
                showState("state-success-walker");
            } else {
                showState("state-success");
            }
        } else if (data.code === "expired") {
            showState("state-expired");
        } else if (data.code === "already_verified") {
            showState("state-already");
        } else {
            showState("state-error");
        }

    } catch (err) {
        // Demo: simulate success after 1.5s delay
        setTimeout(function () {
            showState("state-success");
        }, 1500);
    }
}

/* ── Resend verification email ── */
document.getElementById("resend-btn").addEventListener("click", async function () {
    var btn       = this;
    var alertBox  = document.getElementById("resend-alert");
    var textSpan  = btn.querySelector(".btn-text");
    var spinner   = btn.querySelector(".btn-spinner");
    var token     = getParam("token");

    btn.disabled = true;
    textSpan.classList.add("d-none");
    spinner.classList.remove("d-none");
    alertBox.classList.add("d-none");

    try {
        var response = await fetch(API_RESEND, {
            method: "POST",
            headers: { "Content-Type": "application/json", "Accept": "application/json" },
            body: JSON.stringify({ token: token })
        });
        var data = await response.json();

        alertBox.classList.remove("d-none");
        if (data.success || response.ok) {
            alertBox.style.color = "#1b4332";
            alertBox.innerHTML = '<i class="bi bi-check-circle me-1"></i> Novi link je poslat na vaš e-mail.';
        } else {
            alertBox.style.color = "#721c24";
            alertBox.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>' + (data.message || "Greška. Pokušajte ponovo.");
        }
    } catch (err) {
        // Demo fallback
        alertBox.classList.remove("d-none");
        alertBox.style.color = "#1b4332";
        alertBox.innerHTML = '<i class="bi bi-check-circle me-1"></i> Novi link je poslat na vaš e-mail. (Demo mod)';
    } finally {
        btn.disabled = false;
        textSpan.classList.remove("d-none");
        spinner.classList.add("d-none");
    }
});

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

/* ── Init ── */
(function () {
    var token = getParam("token");
    if (!token) {
        showState("state-error");
        return;
    }
    verifyToken(token);
})();