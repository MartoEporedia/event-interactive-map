<?php
/**
 * Register Event POI Custom Post Type
 */
function eim_register_poi_cpt() {
    $labels = [
        'name' => __('Event POIs', 'event-interactive-map'),
        'singular_name' => __('Event POI', 'event-interactive-map'),
        'add_new' => __('Add New', 'event-interactive-map'),
        'add_new_item' => __('Add New Event POI', 'event-interactive-map'),
        'edit_item' => __('Edit Event POI', 'event-interactive-map'),
        'new_item' => __('New Event POI', 'event-interactive-map'),
        'view_item' => __('View Event POI', 'event-interactive-map'),
        'search_items' => __('Search Event POIs', 'event-interactive-map'),
        'not_found' => __('No Event POIs found', 'event-interactive-map'),
        'not_found_in_trash' => __('No Event POIs found in Trash', 'event-interactive-map'),
    ];

    register_post_type('event_poi', [
        'labels' => $labels,
        'public' => true,
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
        'menu_icon' => 'dashicons-location-alt',
        'has_archive' => true,
        'rewrite' => ['slug' => 'events'],
        'show_in_menu' => true,
        'menu_position' => 5,
        'capability_type' => 'post',
    ]);
}
add_action('init', 'eim_register_poi_cpt');

/**
 * Add meta box for POI details
 */
function eim_add_poi_meta_box() {
    add_meta_box(
        'eim_poi_details',
        __('Event Details & Location', 'event-interactive-map'),
        'eim_poi_meta_box_callback',
        'event_poi',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'eim_add_poi_meta_box');

/**
 * Meta box callback function
 */
function eim_poi_meta_box_callback($post) {
    wp_nonce_field('eim_save_poi_meta', 'eim_poi_meta_nonce');

    $lat = get_post_meta($post->ID, 'lat', true);
    $lng = get_post_meta($post->ID, 'lng', true);
    $event_type = get_post_meta($post->ID, 'event_type', true);
    $event_date = get_post_meta($post->ID, 'event_date', true);
    $event_time = get_post_meta($post->ID, 'event_time', true);
    $event_address = get_post_meta($post->ID, 'event_address', true);
    $location_type = get_post_meta($post->ID, 'location_type', true) ?: 'point';
    $geometry_data = get_post_meta($post->ID, 'geometry_data', true);

    ?>
    <div class="eim-meta-box">
        <style>
            .eim-meta-box { padding: 10px 0; }
            .eim-field { margin-bottom: 20px; }
            .eim-field label { display: block; font-weight: 600; margin-bottom: 5px; }
            .eim-field input[type="text"],
            .eim-field input[type="date"],
            .eim-field input[type="time"],
            .eim-field input[type="number"],
            .eim-field select { width: 100%; max-width: 400px; }
            .eim-field-group { display: flex; gap: 15px; }
            .eim-field-group .eim-field { flex: 1; }
            #eim-admin-map { height: 500px; width: 100%; margin-top: 10px; border: 1px solid #ddd; }
            .eim-map-instructions { background: #f0f6fc; padding: 10px; border-left: 4px solid #0073aa; margin-bottom: 10px; }
            .eim-location-type-group { display: flex; gap: 20px; margin-top: 10px; }
            .eim-location-type-group label { display: flex; align-items: center; gap: 5px; font-weight: normal; cursor: pointer; }
            .eim-location-type-group input[type="radio"] { margin: 0; }
        </style>

        <div class="eim-field">
            <label for="event_type"><?php _e('Event Type', 'event-interactive-map'); ?></label>
            <select name="event_type" id="event_type">
                <option value=""><?php _e('Select Type', 'event-interactive-map'); ?></option>
                <option value="concert" <?php selected($event_type, 'concert'); ?>><?php _e('Concert', 'event-interactive-map'); ?></option>
                <option value="exhibition" <?php selected($event_type, 'exhibition'); ?>><?php _e('Exhibition', 'event-interactive-map'); ?></option>
                <option value="conference" <?php selected($event_type, 'conference'); ?>><?php _e('Conference', 'event-interactive-map'); ?></option>
                <option value="workshop" <?php selected($event_type, 'workshop'); ?>><?php _e('Workshop', 'event-interactive-map'); ?></option>
                <option value="festival" <?php selected($event_type, 'festival'); ?>><?php _e('Festival', 'event-interactive-map'); ?></option>
                <option value="sports" <?php selected($event_type, 'sports'); ?>><?php _e('Sports', 'event-interactive-map'); ?></option>
                <option value="other" <?php selected($event_type, 'other'); ?>><?php _e('Other', 'event-interactive-map'); ?></option>
            </select>
        </div>

        <div class="eim-field-group">
            <div class="eim-field">
                <label for="event_date"><?php _e('Event Date', 'event-interactive-map'); ?></label>
                <input type="date" name="event_date" id="event_date" value="<?php echo esc_attr($event_date); ?>">
            </div>

            <div class="eim-field">
                <label for="event_time"><?php _e('Event Time', 'event-interactive-map'); ?></label>
                <input type="time" name="event_time" id="event_time" value="<?php echo esc_attr($event_time); ?>">
            </div>
        </div>

        <div class="eim-field">
            <label><?php _e('Location Type', 'event-interactive-map'); ?></label>
            <div class="eim-location-type-group">
                <label>
                    <input type="radio" name="location_type" value="point" <?php checked($location_type, 'point'); ?>>
                    <?php _e('Point (Single Location)', 'event-interactive-map'); ?>
                </label>
                <label>
                    <input type="radio" name="location_type" value="polygon" <?php checked($location_type, 'polygon'); ?>>
                    <?php _e('Area/Polygon (Region)', 'event-interactive-map'); ?>
                </label>
                <label>
                    <input type="radio" name="location_type" value="circle" <?php checked($location_type, 'circle'); ?>>
                    <?php _e('Circle (Radius)', 'event-interactive-map'); ?>
                </label>
            </div>
            <p class="description">
                <?php _e('Select "Point" for a specific location marker, or "Area/Polygon" to draw boundaries for region-wide events, or "Circle" for events with a radius.', 'event-interactive-map'); ?>
            </p>
        </div>

        <div class="eim-field">
            <label for="event_address"><?php _e('Address', 'event-interactive-map'); ?></label>
            <input type="text" name="event_address" id="event_address" value="<?php echo esc_attr($event_address); ?>" placeholder="<?php _e('Enter address to search on map', 'event-interactive-map'); ?>">
            <button type="button" id="eim-search-address" class="button"><?php _e('Search Address', 'event-interactive-map'); ?></button>
        </div>

        <input type="hidden" name="geometry_data" id="geometry_data" value="<?php echo esc_attr($geometry_data); ?>">

        <div class="eim-field-group">
            <div class="eim-field">
                <label for="lat"><?php _e('Latitude', 'event-interactive-map'); ?></label>
                <input type="number" step="any" name="lat" id="lat" value="<?php echo esc_attr($lat); ?>" placeholder="45.0" required>
            </div>

            <div class="eim-field">
                <label for="lng"><?php _e('Longitude', 'event-interactive-map'); ?></label>
                <input type="number" step="any" name="lng" id="lng" value="<?php echo esc_attr($lng); ?>" placeholder="7.6" required>
            </div>
        </div>

        <div class="eim-field">
            <div class="eim-map-instructions">
                <strong><?php _e('How to set location:', 'event-interactive-map'); ?></strong>
                <ul style="margin: 5px 0 0 20px;">
                    <li><?php _e('Search for an address using the field above', 'event-interactive-map'); ?></li>
                    <li><strong><?php _e('For Point:', 'event-interactive-map'); ?></strong> <?php _e('Click on map or drag marker to set location', 'event-interactive-map'); ?></li>
                    <li><strong><?php _e('For Area/Polygon:', 'event-interactive-map'); ?></strong> <?php _e('Use the polygon tool (⬠) in the map toolbar to draw boundaries', 'event-interactive-map'); ?></li>
                    <li><strong><?php _e('For Circle:', 'event-interactive-map'); ?></strong> <?php _e('Use the circle tool (○) to draw a radius around the event', 'event-interactive-map'); ?></li>
                    <li><?php _e('You can edit or delete drawn shapes using the toolbar', 'event-interactive-map'); ?></li>
                </ul>
            </div>
            <div id="eim-admin-map"></div>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        // Wait for Leaflet to be fully loaded
        if (typeof L === 'undefined') {
            console.error('Leaflet not loaded! Cannot initialize map.');
            return;
        }

        // Initialize map with timeout to ensure DOM is ready
        setTimeout(function() {
            try {
                // Get default coordinates
                var defaultLat = <?php echo !empty($lat) ? floatval($lat) : 45.0; ?>;
                var defaultLng = <?php echo !empty($lng) ? floatval($lng) : 7.6; ?>;

                console.log('Initializing admin map at:', defaultLat, defaultLng);

                // Create map instance
                var map = L.map('eim-admin-map', {
                    center: [defaultLat, defaultLng],
                    zoom: 13,
                    scrollWheelZoom: true
                });

                // Add tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 19
                }).addTo(map);

                // Feature group to store drawn items
                var drawnItems = new L.FeatureGroup();
                map.addLayer(drawnItems);

                // Add draggable marker
                var marker = L.marker([defaultLat, defaultLng], {
                    draggable: true,
                    autoPan: true
                });

                // Get location type
                var locationType = $('input[name="location_type"]:checked').val();

                // Show marker only for point type
                if (locationType === 'point') {
                    marker.addTo(map);
                }

                // Initialize Leaflet.Draw controls
                var drawControl = new L.Control.Draw({
                    position: 'topright',
                    draw: {
                        polyline: false,
                        marker: false,
                        circlemarker: false,
                        rectangle: false,
                        polygon: {
                            allowIntersection: false,
                            showArea: true,
                            metric: true
                        },
                        circle: {
                            showRadius: true,
                            metric: true
                        }
                    },
                    edit: {
                        featureGroup: drawnItems,
                        remove: true
                    }
                });
                map.addControl(drawControl);

                // Load existing geometry if available
                var existingGeometry = $('#geometry_data').val();
                if (existingGeometry) {
                    try {
                        var geoJSON = JSON.parse(existingGeometry);
                        L.geoJSON(geoJSON, {
                            onEachFeature: function(feature, layer) {
                                drawnItems.addLayer(layer);
                            }
                        });
                        console.log('Loaded existing geometry:', geoJSON);
                    } catch (e) {
                        console.error('Error loading geometry:', e);
                    }
                }

                // Force map to recalculate size (fixes display issues)
                setTimeout(function() {
                    map.invalidateSize();
                }, 250);

                // Update coordinates when marker is moved
                function updateCoordinates(lat, lng) {
                    $('#lat').val(lat.toFixed(6));
                    $('#lng').val(lng.toFixed(6));
                }

                marker.on('dragend', function(e) {
                    var position = marker.getLatLng();
                    updateCoordinates(position.lat, position.lng);
                    console.log('Marker moved to:', position.lat, position.lng);
                });

                // Click on map to set position
                map.on('click', function(e) {
                    marker.setLatLng(e.latlng);
                    updateCoordinates(e.latlng.lat, e.latlng.lng);
                    console.log('Map clicked at:', e.latlng.lat, e.latlng.lng);
                });

                // Update marker when coordinates are manually changed
                $('#lat, #lng').on('change', function() {
                    var lat = parseFloat($('#lat').val());
                    var lng = parseFloat($('#lng').val());
                    if (!isNaN(lat) && !isNaN(lng)) {
                        marker.setLatLng([lat, lng]);
                        map.setView([lat, lng]);
                        console.log('Coordinates manually set to:', lat, lng);
                    }
                });

                // Function to save geometry data
                function saveGeometry() {
                    var data = drawnItems.toGeoJSON();
                    $('#geometry_data').val(JSON.stringify(data));
                    console.log('Geometry saved:', data);
                }

                // Handle drawing events
                map.on(L.Draw.Event.CREATED, function(event) {
                    var layer = event.layer;

                    // Clear previous drawings
                    drawnItems.clearLayers();

                    // Add new drawing
                    drawnItems.addLayer(layer);

                    // Update center coordinates from drawn shape
                    var bounds = layer.getBounds ? layer.getBounds() : null;
                    if (bounds) {
                        var center = bounds.getCenter();
                        updateCoordinates(center.lat, center.lng);
                    } else if (layer.getLatLng) {
                        var latlng = layer.getLatLng();
                        updateCoordinates(latlng.lat, latlng.lng);
                    }

                    saveGeometry();
                    console.log('Shape created:', event.layerType);
                });

                // Handle edit events
                map.on(L.Draw.Event.EDITED, function(event) {
                    saveGeometry();
                    console.log('Shapes edited');
                });

                // Handle delete events
                map.on(L.Draw.Event.DELETED, function(event) {
                    saveGeometry();
                    console.log('Shapes deleted');
                });

                // Handle location type change
                $('input[name="location_type"]').on('change', function() {
                    var selectedType = $(this).val();
                    console.log('Location type changed to:', selectedType);

                    if (selectedType === 'point') {
                        // Show marker, hide drawing tools would be in future enhancement
                        if (!map.hasLayer(marker)) {
                            marker.addTo(map);
                        }
                    } else {
                        // Hide marker for area/polygon/circle types
                        if (map.hasLayer(marker)) {
                            map.removeLayer(marker);
                        }
                    }
                });

                // Address search using Nominatim
                $('#eim-search-address').on('click', function() {
                    var address = $('#event_address').val();
                    if (!address) {
                        alert('<?php _e('Please enter an address', 'event-interactive-map'); ?>');
                        return;
                    }

                    console.log('Searching for address:', address);
                    $(this).prop('disabled', true).text('<?php _e('Searching...', 'event-interactive-map'); ?>');

                    $.ajax({
                        url: 'https://nominatim.openstreetmap.org/search',
                        data: {
                            q: address,
                            format: 'json',
                            limit: 1
                        },
                        success: function(data) {
                            if (data && data.length > 0) {
                                var lat = parseFloat(data[0].lat);
                                var lng = parseFloat(data[0].lon);
                                marker.setLatLng([lat, lng]);
                                map.setView([lat, lng], 15);
                                updateCoordinates(lat, lng);
                                console.log('Address found:', lat, lng);
                            } else {
                                alert('<?php _e('Address not found', 'event-interactive-map'); ?>');
                                console.warn('Address not found:', address);
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('<?php _e('Error searching address', 'event-interactive-map'); ?>');
                            console.error('Geocoding error:', error);
                        },
                        complete: function() {
                            $('#eim-search-address').prop('disabled', false).text('<?php _e('Search Address', 'event-interactive-map'); ?>');
                        }
                    });
                });

                console.log('Admin map initialized successfully!');

            } catch (error) {
                console.error('Error initializing admin map:', error);
                $('#eim-admin-map').html('<div style="padding: 20px; background: #fee; border: 1px solid #c33; color: #c33;">Error loading map: ' + error.message + '</div>');
            }
        }, 500); // 500ms delay to ensure everything is loaded
    });
    </script>
    <?php
}

/**
 * Save meta box data
 */
function eim_save_poi_meta($post_id) {
    // Check nonce
    if (!isset($_POST['eim_poi_meta_nonce']) || !wp_verify_nonce($_POST['eim_poi_meta_nonce'], 'eim_save_poi_meta')) {
        return;
    }

    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save fields
    $fields = ['lat', 'lng', 'event_type', 'event_date', 'event_time', 'event_address', 'location_type'];

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }

    // Save geometry data separately (JSON data)
    if (isset($_POST['geometry_data'])) {
        update_post_meta($post_id, 'geometry_data', wp_kses_post($_POST['geometry_data']));
    }
}
add_action('save_post_event_poi', 'eim_save_poi_meta');

/**
 * Add admin notices for required fields
 */
function eim_admin_notices() {
    global $post;

    if (get_post_type($post) === 'event_poi') {
        $lat = get_post_meta($post->ID, 'lat', true);
        $lng = get_post_meta($post->ID, 'lng', true);

        if (empty($lat) || empty($lng)) {
            echo '<div class="notice notice-warning"><p>';
            _e('Please set the event location on the map below.', 'event-interactive-map');
            echo '</p></div>';
        }
    }
}
add_action('admin_notices', 'eim_admin_notices');