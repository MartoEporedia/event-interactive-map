(function($) {
    'use strict';

    let map;
    let markers = [];
    let markerCluster;
    let allPois = [];
    let userMarker = null;
    let activeDay = '';
    let poiMarkers = {};  // poi.id → marker
    let bandIndex  = [];  // [{band, day, time, poi}]

    function buildMarkerIcon(poi) {
        const catSlug = poi.category?.slug || '';
        const catCfg  = (eimData.categoryIcons || {})[catSlug] || {};

        const raw   = (catCfg.icon || eimData.markerIcon || 'dashicons-tickets-alt').trim();
        const color = catCfg.color || '';
        const html  = raw.startsWith('dashicons-')
            ? `<span class="dashicons ${raw.replace(/[^a-z0-9-]/g, '')}"></span>`
            : `<span>${raw}</span>`;

        const styleAttr = color
            ? ` style="border-color:${color};background:${color}22"`
            : '';
        const catClass = catSlug ? ` eim-cat-${catSlug.replace(/[^a-z0-9-]/g, '')}` : '';

        return L.divIcon({
            className: `eim-marker eim-marker-stage${catClass}`,
            html: `<div class="eim-marker-inner"${styleAttr}>${html}</div>`,
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -44]
        });
    }

    function initMap() {
        const mapElement = document.getElementById('event-map');
        if (!mapElement) return;

        const zoom      = parseInt(mapElement.dataset.zoom) || 15;
        const centerLat = parseFloat(mapElement.dataset.centerLat) || 45.1792;
        const centerLng = parseFloat(mapElement.dataset.centerLng) || 7.6497;

        map = L.map('event-map', {
            zoomControl: true,
            scrollWheelZoom: true,
            touchZoom: true,
            tap: true
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        markerCluster = L.markerClusterGroup({
            maxClusterRadius: 40,
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
            zoomToBoundsOnClick: true
        });
        map.addLayer(markerCluster);
        map.setView([centerLat, centerLng], zoom);

        loadPois();
        setupEventListeners();
    }

    function loadPois() {
        showLoading();
        hideError();

        const url = eimData.mapSet
            ? `${eimData.restUrl}?map_set=${encodeURIComponent(eimData.mapSet)}`
            : eimData.restUrl;

        fetch(url, { headers: { 'X-WP-Nonce': eimData.nonce } })
        .then(r => {
            if (!r.ok) throw new Error(r.status);
            return r.json();
        })
        .then(data => {
            hideLoading();
            allPois = data;
            buildDayFilter(allPois);
            buildBandIndex(allPois);
            filterAndDisplay(activeDay);
        })
        .catch(() => {
            hideLoading();
            showError();
        });
    }

    // ── Band index ────────────────────────────────────────────────────────────

    function buildBandIndex(pois) {
        bandIndex = [];
        pois.forEach(poi => {
            (poi.program || []).forEach(slot => {
                if (slot.band) bandIndex.push({ band: slot.band, day: slot.day, time: slot.time, poi });
            });
        });
    }

    function searchBands(query) {
        const q = query.toLowerCase().trim();
        if (!q) return [];
        return bandIndex.filter(item => item.band.toLowerCase().includes(q)).slice(0, 8);
    }

    function focusBand(item) {
        closeAutocomplete();
        $('#eim-search-input').val(item.band);

        const marker = poiMarkers[item.poi.id];
        if (!marker) return;

        markerCluster.zoomToShowLayer(marker, () => {
            marker.openPopup();
        });
    }

    // ── Autocomplete UI ───────────────────────────────────────────────────────

    function showAutocomplete(results) {
        let $ac = $('#eim-autocomplete');
        if (!$ac.length) {
            $ac = $('<div id="eim-autocomplete" class="eim-autocomplete"></div>');
            $('#eim-search-input').closest('.eim-control-group').append($ac);
        }

        if (!results.length) { $ac.hide(); return; }

        $ac.empty();
        results.forEach(item => {
            const dayMeta = DAY_COLORS[dayColorMap[item.day]] || '';
            const time    = item.time ? `<span class="eim-ac-time">${escapeHtml(item.time)}</span>` : '';
            const $item   = $(`
                <div class="eim-ac-item">
                    <span class="eim-ac-band">${escapeHtml(item.band)}</span>
                    <span class="eim-ac-venue">${escapeHtml(item.poi.title)}</span>
                    ${time}
                </div>`);
            $item.on('mousedown', () => focusBand(item));
            $ac.append($item);
        });
        $ac.show();
    }

    function closeAutocomplete() {
        $('#eim-autocomplete').hide().empty();
    }

    // ── Day filter ────────────────────────────────────────────────────────────

    const DAY_COLORS = ['eim-day-color-0', 'eim-day-color-1', 'eim-day-color-2', 'eim-day-color-3', 'eim-day-color-4'];
    let dayColorMap = {};

    function buildDayFilter(pois) {
        const days = [];
        pois.forEach(poi => {
            (poi.program || []).forEach(slot => {
                if (slot.day && !days.includes(slot.day)) days.push(slot.day);
            });
        });

        const $container = $('#eim-day-filter');
        $container.empty();
        if (days.length === 0) { $container.hide(); return; }

        dayColorMap = {};
        days.forEach((day, i) => { dayColorMap[day] = i % DAY_COLORS.length; });

        $container.append(`<button class="eim-day-btn active" data-day="">${eimData.strings.allDays || 'All'}</button>`);
        days.forEach(day => {
            const cls = DAY_COLORS[dayColorMap[day]];
            $container.append(`<button class="eim-day-btn ${cls}" data-day="${escapeHtml(day)}">${escapeHtml(day)}</button>`);
        });
        $container.show();
    }

    // ── Display ───────────────────────────────────────────────────────────────

    function filterAndDisplay(day) {
        const filtered = day
            ? allPois.filter(poi =>
                Array.isArray(poi.program) && poi.program.some(slot => slot.day === day))
            : allPois;
        displayPois(filtered, day);
    }

    function displayPois(pois, day) {
        markerCluster.clearLayers();
        markers    = [];
        poiMarkers = {};

        if (!pois || pois.length === 0) return;

        const bounds = L.latLngBounds();

        pois.forEach(poi => {
            if (!poi.lat || !poi.lng) return;

            const marker = L.marker([poi.lat, poi.lng], { icon: buildMarkerIcon(poi) });
            marker.bindPopup(createPopupContent(poi, day), {
                maxWidth: 320,
                className: 'eim-popup'
            });

            markerCluster.addLayer(marker);
            markers.push(marker);
            poiMarkers[poi.id] = marker;
            bounds.extend([poi.lat, poi.lng]);
        });

        if (markers.length > 0) {
            setTimeout(() => {
                map.fitBounds(bounds, { padding: [80, 80], maxZoom: 16 });
            }, 100);
        }
    }

    function createPopupContent(poi, filterDay) {
        let html = `<div class="eim-popup-content"><h3>${escapeHtml(poi.title)}</h3>`;

        const program = Array.isArray(poi.program) ? poi.program : [];
        const slots   = filterDay ? program.filter(s => s.day === filterDay) : program;

        if (slots.length > 0) {
            const byDay = {};
            slots.forEach(s => {
                const d = s.day || 'N/D';
                if (!byDay[d]) byDay[d] = [];
                byDay[d].push(s);
            });

            html += `<div class="eim-popup-program">`;
            Object.entries(byDay).forEach(([day, daySlots]) => {
                const colorCls = DAY_COLORS[dayColorMap[day]] || 'eim-day-color-0';
                html += `<div class="eim-popup-day-header ${colorCls}">${escapeHtml(day)}</div>`;
                daySlots.forEach(slot => {
                    const time = slot.time ? `<span class="eim-slot-time">${escapeHtml(slot.time)}</span>` : '';
                    const band = slot.link
                        ? `<a href="${escapeHtml(slot.link)}" target="_blank">${escapeHtml(slot.band)}</a>`
                        : escapeHtml(slot.band);
                    html += `<div class="eim-slot">${time}<span class="eim-slot-band">${band}</span></div>`;
                });
            });
            html += `</div>`;
        } else {
            html += `<p class="eim-popup-empty">Nessun concerto per questo filtro.</p>`;
        }

        html += `</div>`;
        return html;
    }

    // ── Event listeners ───────────────────────────────────────────────────────

    function setupEventListeners() {
        // day filter
        $(document).on('click', '.eim-day-btn', function() {
            $('.eim-day-btn').removeClass('active');
            $(this).addClass('active');
            activeDay = $(this).data('day');
            filterAndDisplay(activeDay);
        });

        // band autocomplete
        $('#eim-search-input').on('input', function() {
            const q = $(this).val().trim();
            if (q.length < 2) { closeAutocomplete(); return; }
            showAutocomplete(searchBands(q));
        });

        $('#eim-search-input').on('blur', () => setTimeout(closeAutocomplete, 150));
        $('#eim-search-input').on('keydown', function(e) {
            if (e.key === 'Escape') closeAutocomplete();
            if (e.key === 'Enter') {
                const first = bandIndex.find(item =>
                    item.band.toLowerCase().includes($(this).val().toLowerCase().trim()));
                if (first) { focusBand(first); e.preventDefault(); }
            }
        });

        // location search button (Nominatim fallback)
        $('#eim-search-btn').on('click', performSearch);

        $('#eim-locate-btn').on('click', geolocateUser);
        $('#eim-reset-btn').on('click', resetView);
        $('#eim-retry-btn').on('click', loadPois);

        // Toggle collasso filtri su mobile
        $('.eim-controls-header').on('click', function() {
            const $controls = $('.eim-map-controls');
            const collapsed = $controls.toggleClass('eim-collapsed').hasClass('eim-collapsed');
            $('#eim-toggle-controls').attr('aria-expanded', String(!collapsed));
            if (!collapsed && map) setTimeout(() => map.invalidateSize(), 270);
        });

        $(window).on('resize', () => { if (map) map.invalidateSize(); });
    }

    // ── Location search (Nominatim) ───────────────────────────────────────────

    function performSearch() {
        const query = $('#eim-search-input').val().trim();
        if (!query) return;

        // se corrisponde a una band, usa l'indice locale
        const match = bandIndex.find(item => item.band.toLowerCase() === query.toLowerCase());
        if (match) { focusBand(match); return; }

        $('#eim-search-btn').prop('disabled', true).addClass('loading');
        $.ajax({
            url: 'https://nominatim.openstreetmap.org/search',
            data: { q: query, format: 'json', limit: 1 },
            success(data) {
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat), lng = parseFloat(data[0].lon);
                    map.setView([lat, lng], 16);
                    if (userMarker) map.removeLayer(userMarker);
                    userMarker = L.marker([lat, lng], {
                        icon: L.divIcon({
                            className: 'eim-marker eim-marker-search',
                            html: '<span class="dashicons dashicons-search"></span>',
                            iconSize: [30, 30], iconAnchor: [15, 30]
                        })
                    }).addTo(map);
                }
            },
            complete() { $('#eim-search-btn').prop('disabled', false).removeClass('loading'); }
        });
    }

    function geolocateUser() {
        if (!navigator.geolocation) return;
        $('#eim-locate-btn').addClass('loading');
        navigator.geolocation.getCurrentPosition(
            pos => {
                const { latitude: lat, longitude: lng } = pos.coords;
                map.setView([lat, lng], 16);
                if (userMarker) map.removeLayer(userMarker);
                userMarker = L.marker([lat, lng], {
                    icon: L.divIcon({
                        className: 'eim-marker eim-marker-user',
                        html: '<span class="dashicons dashicons-admin-site-alt3"></span>',
                        iconSize: [30, 30], iconAnchor: [15, 30]
                    })
                }).addTo(map);
                $('#eim-locate-btn').removeClass('loading');
            },
            () => $('#eim-locate-btn').removeClass('loading'),
            { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
        );
    }

    function resetView() {
        if (userMarker) { map.removeLayer(userMarker); userMarker = null; }
        closeAutocomplete();
        if (markers.length > 0) {
            const bounds = L.latLngBounds();
            markers.forEach(m => bounds.extend(m.getLatLng()));
            map.fitBounds(bounds, { padding: [80, 80], maxZoom: 16 });
        }
    }

    function showLoading() { $('#eim-loading').show(); $('#event-map').css('opacity', '0.5'); }
    function hideLoading() { $('#eim-loading').hide(); $('#event-map').css('opacity', '1'); }
    function showError()   { $('#eim-error').show(); }
    function hideError()   { $('#eim-error').hide(); }

    function escapeHtml(text) {
        return String(text).replace(/[&<>"']/g, m =>
            ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m]));
    }

    $(document).ready(initMap);

})(jQuery);
