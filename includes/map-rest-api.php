<?php
/**
 * REST API endpoint for retrieving POIs
 */
function eim_get_pois_callback($request) {
    $type = $request->get_param('type');

    $args = [
        'post_type' => 'event_poi',
        'numberposts' => -1,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC'
    ];

    // Filter by type if provided
    if (!empty($type)) {
        $args['meta_query'] = [
            [
                'key' => 'event_type',
                'value' => sanitize_text_field($type),
                'compare' => '='
            ]
        ];
    }

    $posts = get_posts($args);
    $pois = [];

    foreach ($posts as $post) {
        $lat = get_post_meta($post->ID, 'lat', true);
        $lng = get_post_meta($post->ID, 'lng', true);
        $location_type = get_post_meta($post->ID, 'location_type', true);
        $geometry_data = get_post_meta($post->ID, 'geometry_data', true);

        // Only include POIs with valid coordinates OR geometry data
        if ((!empty($lat) && !empty($lng) && is_numeric($lat) && is_numeric($lng)) || !empty($geometry_data)) {
            $event_date = get_post_meta($post->ID, 'event_date', true);
            $event_time = get_post_meta($post->ID, 'event_time', true);
            $event_type = get_post_meta($post->ID, 'event_type', true);

            $poi = [
                'id' => absint($post->ID),
                'title' => sanitize_text_field(get_the_title($post)),
                'content' => wp_kses_post($post->post_content),
                'excerpt' => wp_kses_post(get_the_excerpt($post)),
                'lat' => floatval($lat),
                'lng' => floatval($lng),
                'type' => sanitize_text_field($event_type),
                'date' => sanitize_text_field($event_date),
                'time' => sanitize_text_field($event_time),
                'location_type' => sanitize_text_field($location_type ?: 'point'),
                'permalink' => esc_url(get_permalink($post))
            ];

            // Add geometry data if available (for polygons/areas/circles)
            if (!empty($geometry_data)) {
                $poi['geometry'] = json_decode($geometry_data, true);
            }

            $pois[] = $poi;
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
            'description' => __('Filter by event type', 'event-interactive-map'),
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'validate_callback' => function($param, $request, $key) {
                return is_string($param);
            }
        ]
    ];
}

add_action('rest_api_init', function() {
    register_rest_route('eim/v1', '/pois', [
        'methods' => 'GET',
        'callback' => 'eim_get_pois_callback',
        'permission_callback' => 'eim_get_pois_permissions',
        'args' => eim_get_pois_args()
    ]);
});