<?php
/**
 * Plugin Name: Event Interactive Map
 * Description: WordPress plugin for interactive event maps with POI, filters, and mobile UX.
 * Version: 0.1
 * Author: MartoEporedia
 */

defined('ABSPATH') or die('No script kiddies please!');

require_once plugin_dir_path(__FILE__) . 'includes/cpt-poi.php';
require_once plugin_dir_path(__FILE__) . 'includes/map-rest-api.php';

function eim_enqueue_scripts() {
    wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet/dist/leaflet.css');
    wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet/dist/leaflet.js', [], null, true);
    wp_enqueue_script('eim-map', plugins_url('assets/js/map.js', __FILE__), ['leaflet-js'], null, true);
    wp_enqueue_style('eim-style', plugins_url('assets/css/style.css', __FILE__));
    wp_localize_script('eim-map', 'eimData', [
        'restUrl' => esc_url_raw(rest_url('eim/v1/pois')),
        'nonce'   => wp_create_nonce('wp_rest')
    ]);
}
add_action('wp_enqueue_scripts', 'eim_enqueue_scripts');

function eim_shortcode_map() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/map-template.php';
    return ob_get_clean();
}
add_shortcode('event_map', 'eim_shortcode_map');