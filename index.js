(() => {
    'use strict';

    const searchInput= document.getElementById('search-input');
    const cityFilter= document.getElementById('city-filter');
    const breedFilter= document.getElementById('breed-filter');
    const ratingFilter= document.getElementById('rating-filter');
    const noResults= document.getElementById('no-results-msg');
    const searchContainer= document.getElementById('search-results-container');

    const allCards = () => document.querySelectorAll('.walker-card[data-name]');

    function filterPageCards() {
        const query= (searchInput?.value ?? '').trim().toLowerCase();
        const city= (cityFilter?.value ?? '').trim().toLowerCase();
        const breed= (breedFilter?.value ?? '').trim().toLowerCase();
        const rating = parseFloat(ratingFilter?.value ?? '') || 0;

        let visibleCount = 0;

        allCards().forEach(card => {
            const cardName= (card.dataset.name ?? '').toLowerCase();
            const cardCity= (card.dataset.city ?? '').toLowerCase();
            const cardBreed= (card.dataset.breed  ?? '').toLowerCase();
            const cardRating = parseFloat(card.dataset.rating ?? 0);

            const matchesSearch = !query || cardName.includes(query);
            const matchesCity = !city || cardCity === city;
            const matchesBreed = !breed || cardBreed.includes(breed);
            const matchesRating = !rating || cardRating >= rating;

            const visible = matchesSearch && matchesCity && matchesBreed && matchesRating;
            card.closest('.col')?.classList.toggle('d-none', !visible);
            if (visible) visibleCount++;
        });

        updateSectionEmptyState('top-rated-list', 'top-rated-empty');
        updateSectionEmptyState('most-active-list', 'most-active-empty');
        return visibleCount;
    }

    function updateSectionEmptyState(listId, emptyId) {
        const list  = document.getElementById(listId);
        const empty = document.getElementById(emptyId);
        if (!list || !empty) return;
        const hasVisible = [...list.querySelectorAll('.col')].some(col => !col.classList.contains('d-none'));
        empty.classList.toggle('d-none', hasVisible);
    }

    function searchWalkersFromDB() {
        const query = (searchInput?.value ?? '').trim();
        const city = (cityFilter?.value ?? '').trim();
        const breed= (breedFilter?.value ?? '').trim();
        const rating = (ratingFilter?.value ?? '').trim();

        const params = new URLSearchParams();
        if (query) params.append('q', query);
        if (city) params.append('city', city);
        if (breed) params.append('breed', breed);
        if (rating) params.append('rating', rating);

        fetch('search_walkers.php?' + params.toString())
            .then(function (res) { return res.json(); })
            .then(function (data) {
                renderSearchResults(data.walkers || []);
            })
            .catch(function () {
                if (searchContainer) searchContainer.innerHTML = '';
            });
    }

// create html bookmarks
    function renderSearchResults(walkers) {
        if (!searchContainer) return;

        if (walkers.length === 0) {
            searchContainer.innerHTML = '';
            if (noResults) noResults.classList.remove('d-none');
            return;
        }

        if (noResults) noResults.classList.add('d-none');

        const html = walkers.map(function (w) {
            const name= escHtml(w.first_name + ' ' + w.last_name);
            const city= escHtml(w.city || 'Nepoznato');
            const score= parseFloat(w.avg_score) || 0;
            const ratings= parseInt(w.total_ratings) || 0;
            const exp= parseInt(w.experience_years) || 0;
            const stars= buildStars(score);
            const imgHtml= w.photo
                ? '<img src="' + escHtml(w.photo) + '" alt="' + name + '">'
                : '<div class="avatar-placeholder" style="background:#5a6a4a;height:100%;display:flex;align-items:center;justify-content:center;font-size:2rem;color:#fff;font-weight:700;">'
                + escHtml((w.first_name[0] || '') + (w.last_name[0] || '')).toUpperCase()
                + '</div>';

            return '<div class="col">'
                + '  <div class="walker-card card">'
                + '    <div class="card-img-wrap">' + imgHtml
                + '      <span class="badge-top">' + (score > 0 ? score.toFixed(1) : 'Novo') + '</span>'
                + '    </div>'
                + '    <div class="card-body">'
                + '      <div class="walker-name">' + name + '</div>'
                + '      <div class="walker-location"><i class="bi bi-geo-alt-fill"></i> ' + city + '</div>'
                + '      <div class="walker-stars">' + stars
                + '        <span class="text-muted ms-1" style="font-size:.75rem;">(' + ratings + ')</span>'
                + '      </div>'
                + '      <div class="walker-stats">'
                + (exp > 0 ? '<span class="stat-pill">' + exp + ' god. iskustva</span>' : '')
                + (w.city ? '<span class="stat-pill">' + city + '</span>' : '')
                + '      </div>'
                + '      <a href="walker_profile.php?id=' + parseInt(w.id) + '" class="btn-profile">Pogledaj profil</a>'
                + '    </div>'
                + '  </div>'
                + '</div>';
        }).join('');

        searchContainer.innerHTML = '<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-5 g-4 mt-2">' + html + '</div>';
    }

    function buildStars(score) {
        const full= Math.floor(score);
        const half= (score - full) >= 0.5 ? 1 : 0;
        const empty = 5 - full - half;
        let html = '';
        for (let i = 0; i < full;  i++) html += '<i class="bi bi-star-fill"></i>';
        if (half) html += '<i class="bi bi-star-half"></i>';
        for (let i = 0; i < empty; i++) html += '<i class="bi bi-star"></i>';
        return html;
    }

    //converts the text to a secure <script>
    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function populateCityDropdown() {
        if (!cityFilter) return;
        const cities = new Set();
        allCards().forEach(card => {
            const c = card.dataset.city?.trim();
            if (c) cities.add(c);
        });
        [...cities].sort().forEach(city => {
            const opt = document.createElement('option');
            opt.value = city.toLowerCase();
            opt.textContent = city.charAt(0).toUpperCase() + city.slice(1);
            cityFilter.appendChild(opt);
        });
    }

    function debounce(fn, delay) {
        let timer;
        return function () {
            clearTimeout(timer);
            timer = setTimeout(fn, delay || 300);
        };
    }

    function handleFilters() {
        const hasQuery= (searchInput?.value ?? '').trim().length > 0;
        const hasFilter= (cityFilter?.value || breedFilter?.value || ratingFilter?.value);

        if (hasQuery || hasFilter) {
            document.getElementById('walker-section')?.querySelectorAll('.col').forEach(col => col.classList.add('d-none'));
            searchWalkersFromDB();
        } else {
            if (searchContainer) searchContainer.innerHTML = '';
            document.getElementById('walker-section')?.querySelectorAll('.col').forEach(col => col.classList.remove('d-none'));
            filterPageCards();
        }
    }

    function bindEvents() {
        searchInput ?.addEventListener('input', debounce(handleFilters, 300));
        cityFilter ?.addEventListener('change', handleFilters);
        breedFilter ?.addEventListener('change', handleFilters);
        ratingFilter?.addEventListener('change', handleFilters);

        document.getElementById('clear-filters')?.addEventListener('click', function () {
            if (searchInput) searchInput.value = '';
            if (cityFilter) cityFilter.value = '';
            if (breedFilter) breedFilter.value = '';
            if (ratingFilter) ratingFilter.value = '';
            if (searchContainer) searchContainer.innerHTML = '';
            document.getElementById('walker-section')?.querySelectorAll('.col').forEach(col => col.classList.remove('d-none'));
            if (noResults) noResults.classList.add('d-none');
            filterPageCards();
        });
    }

    function initAnimations() {
        document.querySelectorAll('.hero-animate').forEach(el => {
            setTimeout(() => el.classList.add('animate__animated'), 100);
        });

        const scrollEls = document.querySelectorAll('.animate-on-scroll');
        scrollEls.forEach((el, i) => { el.style.transitionDelay = (i * 0.06) + 's'; });

        const revealOnScroll = function () {
            const vh = window.innerHeight;
            scrollEls.forEach(el => {
                if (el.classList.contains('is-visible')) return;
                if (el.getBoundingClientRect().top < vh - 120) {
                    el.classList.add('is-visible');
                }
            });
        };

        window.addEventListener('scroll', revealOnScroll);
        revealOnScroll();
    }

    function init() {
        populateCityDropdown();
        bindEvents();
        filterPageCards();
        initAnimations();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
