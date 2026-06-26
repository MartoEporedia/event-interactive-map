<?php
/**
 * Force classic editor for event_poi: the location meta box uses Leaflet
 * and needs the classic editing context to render correctly.
 */
add_filter('use_block_editor_for_post_type', function($use_block_editor, $post_type) {
    if ($post_type === 'event_poi') {
        return false;
    }
    return $use_block_editor;
}, 10, 2);

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
 * Register poi_category taxonomy for categorising POIs (palco, food, servizi…)
 */
function eim_register_poi_category_taxonomy() {
    register_taxonomy('poi_category', 'event_poi', [
        'labels' => [
            'name'              => __('POI Categories', 'event-interactive-map'),
            'singular_name'     => __('POI Category', 'event-interactive-map'),
            'add_new_item'      => __('Add New Category', 'event-interactive-map'),
            'edit_item'         => __('Edit Category', 'event-interactive-map'),
            'new_item'          => __('New Category', 'event-interactive-map'),
            'search_items'      => __('Search Categories', 'event-interactive-map'),
            'not_found'         => __('No categories found', 'event-interactive-map'),
            'all_items'         => __('All Categories', 'event-interactive-map'),
        ],
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'rewrite'           => ['slug' => 'poi-category'],
    ]);
}
add_action('init', 'eim_register_poi_category_taxonomy');

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
    add_meta_box(
        'eim_poi_content_hint',
        __('ℹ️ How fields appear in the map popup', 'event-interactive-map'),
        'eim_poi_content_hint_callback',
        'event_poi',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'eim_add_poi_meta_box');

function eim_poi_content_hint_callback($post) {
    echo '<div style="background:#f0f6fc;border-left:4px solid #2271b1;padding:12px 16px;border-radius:0 4px 4px 0;font-size:13px;line-height:1.6;">';
    echo '<p style="margin:0 0 8px 0;"><strong>' . __('Description / Content (editor above)', 'event-interactive-map') . '</strong><br>';
    echo __('Shown in the map popup beneath the title. Use it for anything that describes the POI itself: food menus, opening info, general notes, links. Leave it empty for concert stages — the programme repeater below is enough.', 'event-interactive-map') . '</p>';
    echo '<p style="margin:0;"><strong>' . __('Programme (repeater below)', 'event-interactive-map') . '</strong><br>';
    echo __('Use this for timed slots: concerts, DJ sets, speakers. Each slot has a day, time, performer name and optional link. For non-event POIs (food stands, entrances, toilets…) you can leave this empty or use the day/time fields to indicate opening hours.', 'event-interactive-map') . '</p>';
    echo '</div>';
}

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
            <label><?php _e('Location', 'event-interactive-map'); ?></label>
            <p class="description" style="margin-bottom:8px;">
                <?php _e('Search by address, use your current location, or click directly on the map.', 'event-interactive-map'); ?>
            </p>

            <div class="eim-location-toolbar">
                <input type="text" name="event_address" id="event_address"
                       value="<?php echo esc_attr($event_address); ?>"
                       placeholder="<?php esc_attr_e('Enter an address…', 'event-interactive-map'); ?>"
                       style="max-width:none;">
                <button type="button" id="eim-geocode-btn" class="button button-secondary">
                    <?php _e('Search', 'event-interactive-map'); ?>
                </button>
                <button type="button" id="eim-locate-me" class="button button-secondary" title="<?php esc_attr_e('Use my current location', 'event-interactive-map'); ?>">
                    &#x1F4CD; <?php _e('Locate me', 'event-interactive-map'); ?>
                </button>
            </div>
            <div id="eim-geocode-result"></div>

            <div class="eim-admin-map-wrap">
                <div id="eim-admin-map"></div>
                <div id="eim-no-coords-notice">
                    <?php _e('Click on the map to place the marker', 'event-interactive-map'); ?>
                </div>
            </div>

            <div class="eim-coords-row">
                <div class="eim-field" style="margin-bottom:0">
                    <label for="lat"><?php _e('Latitude', 'event-interactive-map'); ?></label>
                    <input type="number" step="any" name="lat" id="lat"
                           value="<?php echo esc_attr($lat); ?>" placeholder="45.000000">
                </div>
                <div class="eim-field" style="margin-bottom:0">
                    <label for="lng"><?php _e('Longitude', 'event-interactive-map'); ?></label>
                    <input type="number" step="any" name="lng" id="lng"
                           value="<?php echo esc_attr($lng); ?>" placeholder="7.600000">
                </div>
            </div>
            <p class="eim-map-hint"><?php _e('Drag the marker to fine-tune the position. Coordinates update automatically.', 'event-interactive-map'); ?></p>
        </div>

        <?php
        $program_raw = get_post_meta($post->ID, 'program', true);
        $program = ($program_raw) ? json_decode($program_raw, true) : [];
        if (!is_array($program)) $program = [];
        ?>
        <div class="eim-field" style="margin-top:28px;border-top:1px solid #ddd;padding-top:20px;">
            <label style="font-size:14px;font-weight:700;margin-bottom:12px;display:block;">
                📅 <?php _e('Programme / Schedule', 'event-interactive-map'); ?>
            </label>
            <p style="color:#666;font-size:12px;margin:-8px 0 12px;">
                <?php _e('Each row is one programme slot (artist, day, time, link). Save post to confirm.', 'event-interactive-map'); ?>
            </p>
            <div id="eim-program-slots"></div>
            <button type="button" id="eim-add-slot" class="button button-secondary" style="margin-top:6px;">
                + <?php _e('Add slot', 'event-interactive-map'); ?>
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
            var DAYS  = <?php echo wp_json_encode(apply_filters('eim_program_days', ['Friday', 'Saturday', 'Sunday'])); ?>;
            var slots = <?php echo wp_json_encode($program); ?>;

            function esc(s) { return $('<div>').text(s || '').html(); }

            function renderSlots() {
                var $c = $('#eim-program-slots');
                $c.empty();
                if (!slots.length) {
                    $c.append('<p style="color:#888;font-style:italic;margin:0 0 8px;"><?php echo esc_js(__('No slots yet. Click "Add slot" to start.', 'event-interactive-map')); ?></p>');
                    return;
                }
                slots.forEach(function(slot, idx) {
                    var dayOpts = DAYS.map(function(d) {
                        return '<option value="'+d+'"'+(slot.day===d?' selected':'')+'>'+d+'</option>';
                    }).join('');
                    if (slot.day && !DAYS.includes(slot.day)) {
                        dayOpts = '<option value="'+esc(slot.day)+'" selected>'+esc(slot.day)+'</option>' + dayOpts;
                    }
                    var row = $('<div class="eim-program-row" data-idx="'+idx+'">'
                        +'<div><label><?php echo esc_js(__('Day', 'event-interactive-map')); ?></label><select class="eim-slot-day">'+dayOpts+'</select></div>'
                        +'<div><label><?php echo esc_js(__('Date', 'event-interactive-map')); ?></label><input type="date" class="eim-slot-date" value="'+esc(slot.date)+'"></div>'
                        +'<div><label><?php echo esc_js(__('Time', 'event-interactive-map')); ?></label><input type="time" class="eim-slot-time" value="'+esc(slot.time)+'"></div>'
                        +'<div><label><?php echo esc_js(__('Artist / Event', 'event-interactive-map')); ?></label><input type="text" class="eim-slot-band" value="'+esc(slot.band)+'" placeholder="<?php echo esc_js(__('Artist name', 'event-interactive-map')); ?>"></div>'
                        +'<div><label><?php echo esc_js(__('Link', 'event-interactive-map')); ?></label><input type="text" class="eim-slot-link" value="'+esc(slot.link)+'" placeholder="https://..."></div>'
                        +'<div><label><?php echo esc_js(__('Notes (opt.)', 'event-interactive-map')); ?></label><input type="text" class="eim-slot-note" value="'+esc(slot.note)+'" placeholder=""></div>'
                        +'<div><button type="button" class="button eim-remove-slot" title="<?php echo esc_js(__('Remove', 'event-interactive-map')); ?>">✕</button></div>'
                        +'</div>');
                    $c.append(row);
                });
            }

            function collectSlots() {
                slots = [];
                $('#eim-program-slots .eim-program-row').each(function() {
                    var $r = $(this);
                    var band = $r.find('.eim-slot-band').val();
                    if (!band.trim()) return;
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
                slots.push({band:'', day: DAYS[0] || '', date:'', time:'', link:'', note:''});
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

// ── Campi icona/colore sulla pagina della categoria ───────────────────────

function eim_cat_icon_presets() {
    return [
        '🎵' => 'Music',   '🎸' => 'Guitar',  '🎤' => 'Microphone',
        '🍺' => 'Beer',    '🍕' => 'Food',    '☕' => 'Bar',
        '🚻' => 'Toilets', '🅿️' => 'Parking', 'ℹ️' => 'Info',
        '🛍️' => 'Stand',   '🎪' => 'Tent',    '🌳' => 'Park',
    ];
}

function eim_cat_fields_html($icon = '', $color = '#e67e22') {
    $presets = eim_cat_icon_presets();
    ob_start(); ?>
    <div class="eim-cat-icon-row" style="margin-bottom:6px;display:flex;gap:4px;flex-wrap:wrap">
    <?php foreach ($presets as $ic => $lbl): ?>
        <button type="button" class="button button-small"
            title="<?php echo esc_attr($lbl); ?>"
            onclick="(function(){var i=document.getElementById('eim_cat_icon');i.value=<?php echo esc_attr(json_encode($ic)); ?>;eimCatPrev()})()"
            style="padding:2px 6px;font-size:16px;line-height:1.5"><?php echo esc_html($ic); ?></button>
    <?php endforeach; ?>
    </div>
    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
        <input type="text" id="eim_cat_icon" name="eim_cat_icon"
            value="<?php echo esc_attr($icon); ?>"
            style="width:160px" placeholder="emoji o dashicons-*"
            oninput="eimCatPrev()">
        <input type="color" id="eim_cat_color" name="eim_cat_color"
            value="<?php echo esc_attr($color ?: '#e67e22'); ?>"
            style="width:52px;height:36px;cursor:pointer;border:none;padding:0"
            oninput="eimCatPrev()">
        <span id="eim_cat_preview" style="display:inline-flex;align-items:center;justify-content:center;
            width:36px;height:36px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);
            border:3px solid <?php echo esc_attr($color ?: '#e67e22'); ?>;
            background:<?php echo esc_attr($color ?: '#e67e22'); ?>22;font-size:16px">
            <span id="eim_cat_preview_inner" style="transform:rotate(45deg)"><?php echo esc_html($icon ?: '📍'); ?></span>
        </span>
    </div>
    <script>
    function eimCatPrev(){
        var icon=document.getElementById('eim_cat_icon').value||'📍';
        var color=document.getElementById('eim_cat_color').value;
        var p=document.getElementById('eim_cat_preview');
        p.style.borderColor=color; p.style.background=color+'33';
        document.getElementById('eim_cat_preview_inner').textContent=
            icon.startsWith('dashicons-')?'':icon;
    }
    document.addEventListener('DOMContentLoaded',eimCatPrev);
    </script>
    <?php
    return ob_get_clean();
}

// Form "Aggiungi categoria"
add_action('poi_category_add_form_fields', function() {
    echo '<div class="form-field">';
    echo '<label>' . esc_html__('Icon & Colour', 'event-interactive-map') . '</label>';
    echo eim_cat_fields_html();
    echo '</div>';
});

// Form "Modifica categoria"
add_action('poi_category_edit_form_fields', function($term) {
    $icon  = get_term_meta($term->term_id, 'eim_icon',  true);
    $color = get_term_meta($term->term_id, 'eim_color', true);
    echo '<tr class="form-field"><th scope="row">';
    echo '<label>' . esc_html__('Icon & Colour', 'event-interactive-map') . '</label></th><td>';
    echo eim_cat_fields_html($icon, $color ?: '#e67e22');
    echo '</td></tr>';
});

// Salvataggio
function eim_save_cat_meta($term_id) {
    if (isset($_POST['eim_cat_icon'])) {
        update_term_meta($term_id, 'eim_icon',  wp_kses_post(wp_unslash($_POST['eim_cat_icon'])));
    }
    if (isset($_POST['eim_cat_color'])) {
        update_term_meta($term_id, 'eim_color', sanitize_hex_color(wp_unslash($_POST['eim_cat_color'])) ?: '#e67e22');
    }
}
add_action('created_poi_category', 'eim_save_cat_meta');
add_action('edited_poi_category',  'eim_save_cat_meta');

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