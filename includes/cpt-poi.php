<?php
function eim_register_poi_cpt() {
    register_post_type('event_poi', [
        'label' => 'Event POIs',
        'public' => true,
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'custom-fields'],
        'menu_icon' => 'dashicons-location-alt',
    ]);
}
add_action('init', 'eim_register_poi_cpt');