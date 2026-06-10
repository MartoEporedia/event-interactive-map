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
 * Register map_set taxonomy for grouping POIs into separate maps
 */
function eim_register_map_set_taxonomy() {
    register_taxonomy('map_set', 'event_poi', [
        'labels' => [
            'name'          => __('Map Sets', 'event-interactive-map'),
            'singular_name' => __('Map Set', 'event-interactive-map'),
            'add_new_item'  => __('Add New Map Set', 'event-interactive-map'),
            'edit_item'     => __('Edit Map Set', 'event-interactive-map'),
        ],
        'public'            => false,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'hierarchical'      => false,
        'rewrite'           => false,
    ]);
}
add_action('init', 'eim_register_map_set_taxonomy');

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
            #eim-admin-map { height: 400px; width: 100%; margin-top: 10px; border: 1px solid #ddd; }
            .eim-map-instructions { background: #f0f6fc; padding: 10px; border-left: 4px solid #0073aa; margin-bottom: 10px; }
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
            <label for="event_address"><?php _e('Address', 'event-interactive-map'); ?></label>
            <input type="text" name="event_address" id="event_address" value="<?php echo esc_attr($event_address); ?>" placeholder="<?php _e('Enter address to search on map', 'event-interactive-map'); ?>">
            <button type="button" id="eim-search-address" class="button"><?php _e('Search Address', 'event-interactive-map'); ?></button>
        </div>

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
                    <li><?php _e('Click on the map to set the exact location', 'event-interactive-map'); ?></li>
                    <li><?php _e('Drag the marker to adjust the position', 'event-interactive-map'); ?></li>
                    <li><?php _e('Coordinates will update automatically', 'event-interactive-map'); ?></li>
                </ul>
            </div>
            <div id="eim-admin-map"></div>
        </div>

        <?php
        $program_raw = get_post_meta($post->ID, 'program', true);
        $program = ($program_raw) ? json_decode($program_raw, true) : [];
        if (!is_array($program)) $program = [];
        ?>
        <div class="eim-field" style="margin-top:28px;border-top:1px solid #ddd;padding-top:20px;">
            <label style="font-size:14px;font-weight:700;margin-bottom:12px;display:block;">
                📅 <?php _e('Programma / Schedule', 'event-interactive-map'); ?>
            </label>
            <p style="color:#666;font-size:12px;margin:-8px 0 12px;">
                <?php _e('Ogni riga è uno slot del programma (artista, giorno, ora, link). Salvare per confermare.', 'event-interactive-map'); ?>
            </p>
            <div id="eim-program-slots"></div>
            <button type="button" id="eim-add-slot" class="button button-secondary" style="margin-top:6px;">
                + <?php _e('Aggiungi slot', 'event-interactive-map'); ?>
            </button>
            <input type="hidden" name="eim_program_json" id="eim_program_json"
                   value="<?php echo esc_attr(wp_json_encode($program)); ?>">
        </div>

        <style>
            .eim-program-row { display:grid; grid-template-columns:110px 105px 72px 1fr 1fr 165px 34px; gap:5px; align-items:end; margin-bottom:8px; padding:9px 10px; background:#f9f9f9; border:1px solid #ddd; border-radius:4px; }
            .eim-program-row label { font-size:11px; font-weight:600; display:block; margin-bottom:3px; color:#444; }
            .eim-program-row input, .eim-program-row select { width:100% !important; max-width:none !important; }
            .eim-remove-slot { color:#a00 !important; padding:0 7px !important; font-size:15px !important; }
        </style>

        <script>
        jQuery(document).ready(function($) {
            var DAYS  = ['Venerdì','Sabato','Domenica'];
            var slots = <?php echo wp_json_encode($program); ?>;

            function esc(s) { return $('<div>').text(s || '').html(); }

            function renderSlots() {
                var $c = $('#eim-program-slots');
                $c.empty();
                if (!slots.length) {
                    $c.append('<p style="color:#888;font-style:italic;margin:0 0 8px;">Nessuno slot. Clicca "Aggiungi slot" per iniziare.</p>');
                    return;
                }
                slots.forEach(function(slot, idx) {
                    var dayOpts = DAYS.map(function(d) {
                        return '<option value="'+d+'"'+(slot.day===d?' selected':'')+'>'+d+'</option>';
                    }).join('');
                    var row = $('<div class="eim-program-row" data-idx="'+idx+'">'
                        +'<div><label>Giorno</label><select class="eim-slot-day">'+dayOpts+'</select></div>'
                        +'<div><label>Data</label><input type="date" class="eim-slot-date" value="'+esc(slot.date)+'"></div>'
                        +'<div><label>Ora</label><input type="time" class="eim-slot-time" value="'+esc(slot.time)+'"></div>'
                        +'<div><label>Artista / Evento</label><input type="text" class="eim-slot-band" value="'+esc(slot.band)+'" placeholder="Nome artista"></div>'
                        +'<div><label>Link</label><input type="text" class="eim-slot-link" value="'+esc(slot.link)+'" placeholder="https://..."></div>'
                        +'<div><label>Note (opz.)</label><input type="text" class="eim-slot-note" value="'+esc(slot.note)+'" placeholder="es. 3 band in gara"></div>'
                        +'<div><button type="button" class="button eim-remove-slot" title="Rimuovi">✕</button></div>'
                        +'</div>');
                    $c.append(row);
                });
            }

            function collectSlots() {
                slots = [];
                $('#eim-program-slots .eim-program-row').each(function() {
                    var $r = $(this);
                    var band = $r.find('.eim-slot-band').val();
                    if (!band.trim()) return; // skip empty rows
                    slots.push({
                        band: band,
                        day:  $r.find('.eim-slot-day').val(),
                        date: $r.find('.eim-slot-date').val(),
                        time: $r.find('.eim-slot-time').val(),
                        link: $r.find('.eim-slot-link').val(),
                        note: $r.find('.eim-slot-note').val(),
                    });
                });
                $('#eim_program_json').val(JSON.stringify(slots));
            }

            $('#eim-add-slot').on('click', function() {
                collectSlots();
                slots.push({band:'', day:'Venerdì', date:'', time:'', link:'', note:''});
                renderSlots();
            });

            $(document).on('click', '.eim-remove-slot', function() {
                collectSlots();
                var idx = $(this).closest('.eim-program-row').data('idx');
                slots.splice(idx, 1);
                renderSlots();
                $('#eim_program_json').val(JSON.stringify(slots));
            });

            $('#post').on('submit', function() { collectSlots(); });

            renderSlots();
        });
        </script>
    </div>

    <script>
    jQuery(document).ready(function($) {
        // Initialize map
        var defaultLat = <?php echo !empty($lat) ? floatval($lat) : 45.0; ?>;
        var defaultLng = <?php echo !empty($lng) ? floatval($lng) : 7.6; ?>;

        var map = L.map('eim-admin-map').setView([defaultLat, defaultLng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var marker = L.marker([defaultLat, defaultLng], {draggable: true}).addTo(map);

        // Update coordinates when marker is moved
        function updateCoordinates(lat, lng) {
            $('#lat').val(lat.toFixed(6));
            $('#lng').val(lng.toFixed(6));
        }

        marker.on('dragend', function(e) {
            var position = marker.getLatLng();
            updateCoordinates(position.lat, position.lng);
        });

        // Click on map to set position
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateCoordinates(e.latlng.lat, e.latlng.lng);
        });

        // Update marker when coordinates are manually changed
        $('#lat, #lng').on('change', function() {
            var lat = parseFloat($('#lat').val());
            var lng = parseFloat($('#lng').val());
            if (!isNaN(lat) && !isNaN(lng)) {
                marker.setLatLng([lat, lng]);
                map.setView([lat, lng]);
            }
        });

        // Address search using Nominatim
        $('#eim-search-address').on('click', function() {
            var address = $('#event_address').val();
            if (!address) {
                alert('<?php _e('Please enter an address', 'event-interactive-map'); ?>');
                return;
            }

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
                    } else {
                        alert('<?php _e('Address not found', 'event-interactive-map'); ?>');
                    }
                },
                error: function() {
                    alert('<?php _e('Error searching address', 'event-interactive-map'); ?>');
                }
            });
        });
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

    // Save scalar fields
    $fields = ['lat', 'lng', 'event_type', 'event_date', 'event_time', 'event_address'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }

    // Save program (JSON repeater)
    if (isset($_POST['eim_program_json'])) {
        $raw     = wp_unslash($_POST['eim_program_json']);
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            $clean = [];
            foreach ($decoded as $slot) {
                $band = sanitize_text_field($slot['band'] ?? '');
                if ($band === '') continue;
                $clean[] = [
                    'band' => $band,
                    'day'  => sanitize_text_field($slot['day']  ?? ''),
                    'date' => sanitize_text_field($slot['date'] ?? ''),
                    'time' => sanitize_text_field($slot['time'] ?? ''),
                    'link' => esc_url_raw($slot['link']         ?? ''),
                    'note' => sanitize_text_field($slot['note'] ?? ''),
                ];
            }
            usort($clean, fn($a, $b) => strcmp($a['date'] . $a['time'], $b['date'] . $b['time']));
            update_post_meta($post_id, 'program', wp_json_encode($clean, JSON_UNESCAPED_UNICODE));
        }
    }
}
add_action('save_post_event_poi', 'eim_save_poi_meta');

/**
 * Register meta fields as REST-accessible so the WP REST API can read/write them
 */
function eim_register_post_meta() {
    $string_fields = ['lat', 'lng', 'event_type', 'event_date', 'event_time', 'event_address'];
    foreach ($string_fields as $field) {
        register_post_meta('event_poi', $field, [
            'show_in_rest'  => true,
            'single'        => true,
            'type'          => 'string',
            'auth_callback' => function() { return current_user_can('edit_posts'); },
        ]);
    }
    // program is stored as a JSON string
    register_post_meta('event_poi', 'program', [
        'show_in_rest'  => true,
        'single'        => true,
        'type'          => 'string',
        'default'       => '[]',
        'auth_callback' => function() { return current_user_can('edit_posts'); },
    ]);
}
add_action('init', 'eim_register_post_meta');

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