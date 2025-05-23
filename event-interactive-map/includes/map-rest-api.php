<?php
add_action('rest_api_init', function() {
    register_rest_route('eim/v1', '/pois', [
        'methods' => 'GET',
        'callback' => function() {
            $posts = get_posts(['post_type' => 'event_poi', 'numberposts' => -1]);
            return array_map(function($post) {
                return [
                    'id' => $post->ID,
                    'title' => get_the_title($post),
                    'content' => apply_filters('the_content', $post->post_content),
                    'lat' => get_post_meta($post->ID, 'lat', true),
                    'lng' => get_post_meta($post->ID, 'lng', true),
                    'type' => get_post_meta($post->ID, 'type', true),
                ];
            }, $posts);
        }
    ]);
});