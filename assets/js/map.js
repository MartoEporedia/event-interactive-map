(function($) {
    'use strict';

    let map;
    let markers = [];
    let markerCluster;
    let allPois = [];
    let userMarker = null;
    let activeDay = '';

    const stageIcon = L.divIcon({
        className: 'eim-marker eim-marker-stage',
        html: '<span class="dashicons dashicons-tickets-alt"></span>',
        iconSize: [40, 40],
        iconAnchor: [20, 40],
        popupAnchor: [0, -44]
    });

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
            filterAndDisplay(activeDay);
        })
        .catch(() => {
            hideLoading();
            showError();
        });
    }

    function filterAndDisplay(day) {
        const filtered = day
            ? allPois.filter(poi =>
                Array.isArray(poi.program) &&
                poi.program.some(slot => slot.day === day)
              )
            : allPois;
        displayPois(filtered, day);
    }

    function displayPois(pois, day) {
        markerCluster.clearLayers();
        markers = [];

        if (!pois || pois.length === 0) return;

        const bounds = L.latLngBounds();

        pois.forEach(poi => {
            if (!poi.lat || !poi.lng) return;

            const marker = L.marker([poi.lat, poi.lng], { icon: stageIcon });
            marker.bindPopup(createPopupContent(poi, day), {
                maxWidth: 320,
                className: 'eim-popup'
            });

            markerCluster.addLayer(marker);
            markers.push(marker);
            bounds.extend([poi.lat, poi.lng]);
        });

        if (markers.length > 0) {
            setTimeout(() => {
                map.fitBounds(bounds, { padding: [80, 80], maxZoom: 16 });
            }, 100);
        }
    }

    // Colour palette for day buttons (cycles for N days)
    const DAY_COLORS = ['eim-day-color-0', 'eim-day-color-1', 'eim-day-color-2', 'eim-day-color-3', 'eim-day-color-4'];
    let dayColorMap = {}; // day label → CSS color class

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
        days.forEach((day, i) => { dayColorMap[day] = DAY_COLORS[i % DAY_COLORS.length]; });

        const $all = $(`<button class="eim-day-btn active" data-day="">${eimData.strings.allDays || 'All'}</button>`);
        $container.append($all);

        days.forEach(day => {
            const cls = dayColorMap[day];
            $container.append(`<button class="eim-day-btn ${cls}" data-day="${escapeHtml(day)}">${escapeHtml(day)}</button>`);
        });

        $container.show();
    }

    function createPopupContent(poi, filterDay) {
        let html = `<div class="eim-popup-content">`;
        html += `<h3>${escapeHtml(poi.title)}</h3>`;

        const program = Array.isArray(poi.program) ? poi.program : [];
        const slots = filterDay
            ? program.filter(s => s.day === filterDay)
            : program;

        if (slots.length > 0) {
            // group by day
            const byDay = {};
            slots.forEach(s => {
                const d = s.day || 'N/D';
                if (!byDay[d]) byDay[d] = [];
                byDay[d].push(s);
            });

            html += `<div class="eim-popup-program">`;
            Object.entries(byDay).forEach(([day, daySlots]) => {
                const colorCls = dayColorMap[day] || 'eim-day-color-0';
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

    function setupEventListeners() {
        // day filter buttons
        $(document).on('click', '.eim-day-btn', function() {
            $('.eim-day-btn').removeClass('active');
            $(this).addClass('active');
            activeDay = $(this).data('day');
            filterAndDisplay(activeDay);
        });

        // location search
        $('#eim-search-btn').on('click', performSearch);
        $('#eim-search-input').on('keypress', function(e) {
            if (e.which === 13) performSearch();
        });

        $('#eim-locate-btn').on('click', geolocateUser);
        $('#eim-reset-btn').on('click', resetView);
        $('#eim-retry-btn').on('click', loadPois);

        $(window).on('resize', () => { if (map) map.invalidateSize(); });
    }

    function performSearch() {
        const query = $('#eim-search-input').val().trim();
        if (!query) return;

        $('#eim-search-btn').prop('disabled', true).addClass('loading');

        $.ajax({
            url: 'https://nominatim.openstreetmap.org/search',
            data: { q: query, format: 'json', limit: 1 },
            success(data) {
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lng = parseFloat(data[0].lon);
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
            complete() {
                $('#eim-search-btn').prop('disabled', false).removeClass('loading');
            }
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
        return String(text).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
    }

    $(document).ready(initMap);

})(jQuery);
