(function($) {
    'use strict';

    // Global variables
    let map;
    let markers = [];
    let markerCluster;
    let allPois = [];
    let userMarker = null;
    let geometryLayers = []; // Store polygon/circle layers

    // Custom icons for different event types
    const eventIcons = {
        concert: L.divIcon({
            className: 'eim-marker eim-marker-concert',
            html: '<span class="dashicons dashicons-format-audio"></span>',
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -40]
        }),
        exhibition: L.divIcon({
            className: 'eim-marker eim-marker-exhibition',
            html: '<span class="dashicons dashicons-admin-appearance"></span>',
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -40]
        }),
        conference: L.divIcon({
            className: 'eim-marker eim-marker-conference',
            html: '<span class="dashicons dashicons-groups"></span>',
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -40]
        }),
        workshop: L.divIcon({
            className: 'eim-marker eim-marker-workshop',
            html: '<span class="dashicons dashicons-welcome-learn-more"></span>',
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -40]
        }),
        festival: L.divIcon({
            className: 'eim-marker eim-marker-festival',
            html: '<span class="dashicons dashicons-tickets-alt"></span>',
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -40]
        }),
        sports: L.divIcon({
            className: 'eim-marker eim-marker-sports',
            html: '<span class="dashicons dashicons-awards"></span>',
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -40]
        }),
        other: L.divIcon({
            className: 'eim-marker eim-marker-other',
            html: '<span class="dashicons dashicons-location-alt"></span>',
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -40]
        })
    };

    // Geometry styles for polygons and circles (matching marker colors)
    const geometryStyles = {
        concert: {
            color: '#9b59b6',
            fillColor: '#9b59b6',
            fillOpacity: 0.3,
            weight: 3
        },
        exhibition: {
            color: '#e74c3c',
            fillColor: '#e74c3c',
            fillOpacity: 0.3,
            weight: 3
        },
        conference: {
            color: '#3498db',
            fillColor: '#3498db',
            fillOpacity: 0.3,
            weight: 3
        },
        workshop: {
            color: '#f39c12',
            fillColor: '#f39c12',
            fillOpacity: 0.3,
            weight: 3
        },
        festival: {
            color: '#d35400',
            fillColor: '#d35400',
            fillOpacity: 0.3,
            weight: 3
        },
        sports: {
            color: '#27ae60',
            fillColor: '#27ae60',
            fillOpacity: 0.3,
            weight: 3
        },
        other: {
            color: '#7f8c8d',
            fillColor: '#7f8c8d',
            fillOpacity: 0.3,
            weight: 3
        }
    };

    /**
     * Get geometry style based on event type
     */
    function getGeometryStyle(eventType) {
        return geometryStyles[eventType] || geometryStyles.other;
    }

    /**
     * Initialize the map
     */
    function initMap() {
        const mapElement = document.getElementById('event-map');
        if (!mapElement) return;

        const zoom = parseInt(mapElement.dataset.zoom) || 13;
        const centerLat = parseFloat(mapElement.dataset.centerLat) || 45.0;
        const centerLng = parseFloat(mapElement.dataset.centerLng) || 7.6;

        // Create map
        map = L.map('event-map', {
            zoomControl: true,
            scrollWheelZoom: true,
            touchZoom: true,
            tap: true
        });

        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Initialize marker cluster group
        markerCluster = L.markerClusterGroup({
            maxClusterRadius: 50,
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
            zoomToBoundsOnClick: true
        });

        map.addLayer(markerCluster);

        // Set initial view
        map.setView([centerLat, centerLng], zoom);

        // Load POIs
        loadPois();

        // Setup event listeners
        setupEventListeners();
    }

    /**
     * Load POIs from REST API
     */
    function loadPois(filterType = '') {
        showLoading();
        hideError();

        const url = filterType
            ? `${eimData.restUrl}?type=${encodeURIComponent(filterType)}`
            : eimData.restUrl;

        fetch(url, {
            headers: {
                'X-WP-Nonce': eimData.nonce
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            hideLoading();
            allPois = data;
            displayPois(data);
        })
        .catch(error => {
            console.error('Error loading POIs:', error);
            hideLoading();
            showError();
        });
    }

    /**
     * Display POIs on map
     */
    function displayPois(pois) {
        // Clear existing markers and geometry
        markerCluster.clearLayers();
        markers = [];

        // Clear existing geometry layers
        geometryLayers.forEach(layer => map.removeLayer(layer));
        geometryLayers = [];

        if (!pois || pois.length === 0) {
            showNoEvents();
            return;
        }

        const bounds = L.latLngBounds();

        pois.forEach(poi => {
            const popupContent = createPopupContent(poi);

            // Handle different location types
            if (poi.location_type === 'polygon' || poi.location_type === 'circle') {
                // Render geometry (polygon or circle)
                if (poi.geometry && poi.geometry.features && poi.geometry.features.length > 0) {
                    const geoJSONLayer = L.geoJSON(poi.geometry, {
                        style: function() {
                            return getGeometryStyle(poi.type);
                        },
                        onEachFeature: function(feature, layer) {
                            layer.bindPopup(popupContent, {
                                maxWidth: 300,
                                className: 'eim-popup'
                            });

                            // Extend bounds to include geometry
                            if (layer.getBounds) {
                                bounds.extend(layer.getBounds());
                            }
                        }
                    }).addTo(map);

                    geometryLayers.push(geoJSONLayer);
                }
            } else {
                // Default: render as point marker
                if (poi.lat && poi.lng) {
                    const icon = eventIcons[poi.type] || eventIcons.other;
                    const marker = L.marker([poi.lat, poi.lng], { icon: icon });

                    marker.bindPopup(popupContent, {
                        maxWidth: 300,
                        className: 'eim-popup'
                    });

                    markerCluster.addLayer(marker);
                    markers.push(marker);
                    bounds.extend([poi.lat, poi.lng]);
                }
            }
        });

        // Auto-center map to show all markers and geometry
        if (markers.length > 0 || geometryLayers.length > 0) {
            // Small delay to ensure map is fully rendered
            setTimeout(() => {
                if (bounds.isValid()) {
                    map.fitBounds(bounds, {
                        padding: [50, 50],
                        maxZoom: 15
                    });
                }
            }, 100);
        }
    }

    /**
     * Create popup content for a POI
     */
    function createPopupContent(poi) {
        let html = `<div class="eim-popup-content">`;
        html += `<h3>${escapeHtml(poi.title)}</h3>`;

        if (poi.type) {
            const typeLabel = eimData.types[poi.type] || poi.type;
            html += `<div class="eim-popup-type eim-type-${poi.type}">${escapeHtml(typeLabel)}</div>`;
        }

        if (poi.date || poi.time) {
            html += `<div class="eim-popup-datetime">`;
            html += `<span class="dashicons dashicons-calendar-alt"></span>`;
            if (poi.date) {
                html += formatDate(poi.date);
            }
            if (poi.time) {
                html += ` - ${escapeHtml(poi.time)}`;
            }
            html += `</div>`;
        }

        if (poi.excerpt) {
            html += `<div class="eim-popup-excerpt">${poi.excerpt}</div>`;
        }

        if (poi.permalink) {
            html += `<a href="${escapeHtml(poi.permalink)}" class="eim-popup-link" target="_blank">`;
            html += `${eimData.strings.viewDetails} <span class="dashicons dashicons-external"></span>`;
            html += `</a>`;
        }

        html += `</div>`;
        return html;
    }

    /**
     * Setup event listeners
     */
    function setupEventListeners() {
        // Type filter
        $('#eim-type-filter').on('change', function() {
            const selectedType = $(this).val();
            loadPois(selectedType);
        });

        // Search location
        $('#eim-search-btn').on('click', performSearch);
        $('#eim-search-input').on('keypress', function(e) {
            if (e.which === 13) {
                performSearch();
            }
        });

        // Geolocation
        $('#eim-locate-btn').on('click', geolocateUser);

        // Reset view
        $('#eim-reset-btn').on('click', resetView);

        // Retry button
        $('#eim-retry-btn').on('click', function() {
            loadPois();
        });

        // Responsive map resize
        $(window).on('resize', function() {
            if (map) {
                map.invalidateSize();
            }
        });
    }

    /**
     * Perform location search
     */
    function performSearch() {
        const query = $('#eim-search-input').val().trim();
        if (!query) return;

        $('#eim-search-btn').prop('disabled', true).addClass('loading');

        $.ajax({
            url: 'https://nominatim.openstreetmap.org/search',
            data: {
                q: query,
                format: 'json',
                limit: 1
            },
            success: function(data) {
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lng = parseFloat(data[0].lon);
                    map.setView([lat, lng], 14);

                    // Add temporary marker for searched location
                    if (userMarker) {
                        map.removeLayer(userMarker);
                    }
                    userMarker = L.marker([lat, lng], {
                        icon: L.divIcon({
                            className: 'eim-marker eim-marker-search',
                            html: '<span class="dashicons dashicons-search"></span>',
                            iconSize: [30, 30],
                            iconAnchor: [15, 30]
                        })
                    }).addTo(map);
                } else {
                    alert(eimData.strings.error);
                }
            },
            error: function() {
                alert(eimData.strings.error);
            },
            complete: function() {
                $('#eim-search-btn').prop('disabled', false).removeClass('loading');
            }
        });
    }

    /**
     * Geolocate user
     */
    function geolocateUser() {
        if (!navigator.geolocation) {
            alert(eimData.strings.geolocationError);
            return;
        }

        $('#eim-locate-btn').addClass('loading');

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                map.setView([lat, lng], 14);

                // Add user location marker
                if (userMarker) {
                    map.removeLayer(userMarker);
                }
                userMarker = L.marker([lat, lng], {
                    icon: L.divIcon({
                        className: 'eim-marker eim-marker-user',
                        html: '<span class="dashicons dashicons-admin-site-alt3"></span>',
                        iconSize: [30, 30],
                        iconAnchor: [15, 30]
                    })
                }).addTo(map);

                $('#eim-locate-btn').removeClass('loading');
            },
            function(error) {
                console.error('Geolocation error:', error);
                alert(eimData.strings.geolocationError);
                $('#eim-locate-btn').removeClass('loading');
            },
            {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            }
        );
    }

    /**
     * Reset map view
     */
    function resetView() {
        if (userMarker) {
            map.removeLayer(userMarker);
            userMarker = null;
        }

        if (markers.length > 0 || geometryLayers.length > 0) {
            const bounds = L.latLngBounds();

            // Add marker bounds
            markers.forEach(marker => {
                bounds.extend(marker.getLatLng());
            });

            // Add geometry bounds
            geometryLayers.forEach(layer => {
                layer.eachLayer(function(sublayer) {
                    if (sublayer.getBounds) {
                        bounds.extend(sublayer.getBounds());
                    } else if (sublayer.getLatLng) {
                        bounds.extend(sublayer.getLatLng());
                    }
                });
            });

            if (bounds.isValid()) {
                map.fitBounds(bounds, {
                    padding: [50, 50],
                    maxZoom: 15
                });
            }
        } else {
            const mapElement = document.getElementById('event-map');
            const centerLat = parseFloat(mapElement.dataset.centerLat) || 45.0;
            const centerLng = parseFloat(mapElement.dataset.centerLng) || 7.6;
            const zoom = parseInt(mapElement.dataset.zoom) || 13;
            map.setView([centerLat, centerLng], zoom);
        }
    }

    /**
     * Show loading state
     */
    function showLoading() {
        $('#eim-loading').show();
        $('#event-map').css('opacity', '0.5');
    }

    /**
     * Hide loading state
     */
    function hideLoading() {
        $('#eim-loading').hide();
        $('#event-map').css('opacity', '1');
    }

    /**
     * Show error message
     */
    function showError() {
        $('#eim-error').show();
    }

    /**
     * Hide error message
     */
    function hideError() {
        $('#eim-error').hide();
    }

    /**
     * Show no events message
     */
    function showNoEvents() {
        // Could display a message or keep map empty
        console.log(eimData.strings.noEvents);
    }

    /**
     * Format date string
     */
    function formatDate(dateString) {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) {
            return dateString;
        }
        return date.toLocaleDateString(undefined, {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    /**
     * Escape HTML to prevent XSS
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }

    // Initialize when document is ready
    $(document).ready(function() {
        initMap();
    });

})(jQuery);
