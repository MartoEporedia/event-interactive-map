<?php
/**
 * REST API endpoint for retrieving POIs
 */
function eim_get_pois_callback($request) {
    $type    = $request->get_param('type');
    $map_set = $request->get_param('map_set');

    $args = [
        'post_type'   => 'event_poi',
        'numberposts' => -1,
        'post_status' => 'publish',
        'orderby'     => 'date',
        'order'       => 'DESC',
    ];

    if (!empty($type)) {
        $args['meta_query'] = [[
            'key'     => 'event_type',
            'value'   => sanitize_text_field($type),
            'compare' => '=',
        ]];
    }

    if (!empty($map_set)) {
        $args['tax_query'] = [[
            'taxonomy' => 'map_set',
            'field'    => 'slug',
            'terms'    => sanitize_title($map_set),
        ]];
    }

    $posts = get_posts($args);
    $pois = [];

    foreach ($posts as $post) {
        $lat = get_post_meta($post->ID, 'lat', true);
        $lng = get_post_meta($post->ID, 'lng', true);

        // Only include POIs with valid coordinates
        if (!empty($lat) && !empty($lng) && is_numeric($lat) && is_numeric($lng)) {
            $event_date    = get_post_meta($post->ID, 'event_date', true);
            $event_time    = get_post_meta($post->ID, 'event_time', true);
            $event_type    = get_post_meta($post->ID, 'event_type', true);
            $program_raw   = get_post_meta($post->ID, 'program', true);
            $program       = $program_raw ? json_decode($program_raw, true) : [];

            // filter by day if requested
            $day = $request->get_param('day');
            if (!empty($day) && is_array($program)) {
                $has_day = false;
                foreach ($program as $slot) {
                    if (isset($slot['day']) && mb_strtolower($slot['day']) === mb_strtolower($day)) {
                        $has_day = true;
                        break;
                    }
                }
                if (!$has_day) continue;
            }

            $cat_terms = wp_get_post_terms($post->ID, 'poi_category');
            $category  = null;
            if (!empty($cat_terms) && !is_wp_error($cat_terms)) {
                $ct = $cat_terms[0];
                $category = [
                    'slug'  => $ct->slug,
                    'name'  => $ct->name,
                    'icon'  => get_term_meta($ct->term_id, 'eim_icon',  true) ?: '',
                    'color' => get_term_meta($ct->term_id, 'eim_color', true) ?: '',
                ];
            }

            $pois[] = [
                'id'        => absint($post->ID),
                'title'     => sanitize_text_field(get_the_title($post)),
                'content'   => wp_kses_post($post->post_content),
                'excerpt'   => wp_kses_post(get_the_excerpt($post)),
                'lat'       => floatval($lat),
                'lng'       => floatval($lng),
                'type'      => sanitize_text_field($event_type),
                'date'      => sanitize_text_field($event_date),
                'time'      => sanitize_text_field($event_time),
                'program'   => is_array($program) ? $program : [],
                'category'  => $category,
                'permalink' => esc_url(get_permalink($post)),
            ];
        }
    }

    return rest_ensure_response($pois);
}

/**
 * Validate REST API permissions
 */
function eim_get_pois_permissions() {
    // Allow anyone to read POIs (public data)
    return true;
}

/**
 * Validate REST API parameters
 */
function eim_get_pois_args() {
    return [
        'type' => [
            'description'       => __('Filter by event type', 'event-interactive-map'),
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'validate_callback' => fn($p) => is_string($p),
        ],
        'day' => [
            'description'       => __('Filter by festival day (Venerdì, Sabato, Domenica)', 'event-interactive-map'),
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'validate_callback' => fn($p) => is_string($p),
        ],
        'map_set' => [
            'description'       => __('Filter by map set slug', 'event-interactive-map'),
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_title',
            'validate_callback' => fn($p) => is_string($p),
        ],
    ];
}

/**
 * POST /eim/v1/pois — create or update a POI by title
 */
function eim_upsert_poi_callback($request) {
    $title   = sanitize_text_field($request->get_param('title'));
    $lat     = sanitize_text_field($request->get_param('lat'));
    $lng     = sanitize_text_field($request->get_param('lng'));
    $type    = sanitize_text_field($request->get_param('event_type') ?: 'concert');
    $address = sanitize_text_field($request->get_param('event_address'));
    $date    = sanitize_text_field($request->get_param('event_date') ?: '');
    $time    = sanitize_text_field($request->get_param('event_time') ?: '');
    $program  = $request->get_param('program') ?: '[]';
    $content  = wp_kses_post($request->get_param('content') ?: '');
    $map_set  = sanitize_title($request->get_param('map_set'));
    $category = sanitize_title($request->get_param('category') ?: '');

    if (empty($title) || empty($lat) || empty($lng)) {
        return new WP_Error('missing_fields', 'title, lat and lng are required', ['status' => 400]);
    }

    // find existing post by title
    $existing = get_posts([
        'post_type'   => 'event_poi',
        'post_status' => 'publish',
        'title'       => $title,
        'numberposts' => 1,
    ]);

    $post_data = [
        'post_title'   => $title,
        'post_status'  => 'publish',
        'post_type'    => 'event_poi',
        'post_content' => $content,
    ];

    if ($existing) {
        $post_data['ID'] = $existing[0]->ID;
        $post_id = wp_update_post($post_data, true);
    } else {
        $post_id = wp_insert_post($post_data, true);
    }

    if (is_wp_error($post_id)) {
        return $post_id;
    }

    update_post_meta($post_id, 'lat',           $lat);
    update_post_meta($post_id, 'lng',           $lng);
    update_post_meta($post_id, 'event_type',    $type);
    update_post_meta($post_id, 'event_address', $address);
    update_post_meta($post_id, 'event_date',    $date);
    update_post_meta($post_id, 'event_time',    $time);
    update_post_meta($post_id, 'program',       $program);

    if (!empty($map_set)) {
        $ms_term = get_term_by('slug', $map_set, 'map_set');
        $ms_id   = $ms_term && !is_wp_error($ms_term) ? $ms_term->term_id : null;
        wp_set_object_terms($post_id, $ms_id ? [$ms_id] : [$map_set], 'map_set');
    }
    if (!empty($category)) {
        $term = get_term_by('slug', $category, 'poi_category');
        if ($term && !is_wp_error($term)) {
            wp_set_object_terms($post_id, [$term->term_id], 'poi_category');
        }
    }

    return rest_ensure_response([
        'id'      => $post_id,
        'title'   => $title,
        'updated' => !empty($existing),
    ]);
}

add_action('rest_api_init', function() {
    register_rest_route('eim/v1', '/pois', [
        [
            'methods'             => 'GET',
            'callback'            => 'eim_get_pois_callback',
            'permission_callback' => 'eim_get_pois_permissions',
            'args'                => eim_get_pois_args(),
        ],
        [
            'methods'             => 'POST',
            'callback'            => 'eim_upsert_poi_callback',
            'permission_callback' => function() { return current_user_can('edit_posts'); },
        ],
    ]);
});