<?php
/**
 * Plugin Name: Event Interactive Map
 * Description: WordPress plugin for interactive event maps with POI, filters, and mobile UX.
 * Version: 1.0.0
 * Author: MartoEporedia
 * Text Domain: event-interactive-map
 * Domain Path: /languages
 */

defined('ABSPATH') or die('No script kiddies please!');

// Define plugin constants
define('EIM_VERSION', '1.0.0');
define('EIM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('EIM_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once EIM_PLUGIN_DIR . 'includes/cpt-poi.php';
require_once EIM_PLUGIN_DIR . 'includes/map-rest-api.php';

/**
 * Load plugin textdomain for translations
 */
function eim_load_textdomain() {
    load_plugin_textdomain('event-interactive-map', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'eim_load_textdomain');

/**
 * Enqueue frontend scripts and styles
 */
function eim_enqueue_scripts() {
    // Leaflet CSS
    wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', [], '1.9.4');

    // Leaflet MarkerCluster CSS
    wp_enqueue_style('leaflet-markercluster-css', 'https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css', ['leaflet-css'], '1.5.3');
    wp_enqueue_style('leaflet-markercluster-default-css', 'https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css', ['leaflet-markercluster-css'], '1.5.3');

    // Plugin styles
    wp_enqueue_style('eim-style', EIM_PLUGIN_URL . 'assets/css/style.css', [], EIM_VERSION);

    // Leaflet JS
    wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', [], '1.9.4', true);

    // Leaflet MarkerCluster JS
    wp_enqueue_script('leaflet-markercluster-js', 'https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js', ['leaflet-js'], '1.5.3', true);

    // Plugin map script
    wp_enqueue_script('eim-map', EIM_PLUGIN_URL . 'assets/js/map.js', ['jquery', 'leaflet-js', 'leaflet-markercluster-js'], EIM_VERSION, true);

    // Localize script with data
    wp_localize_script('eim-map', 'eimData', [
        'restUrl' => esc_url_raw(rest_url('eim/v1/pois')),
        'nonce'   => wp_create_nonce('wp_rest'),
        'strings' => [
            'loading' => __('Loading events...', 'event-interactive-map'),
            'error' => __('Error loading events. Please try again.', 'event-interactive-map'),
            'noEvents' => __('No events found.', 'event-interactive-map'),
            'allTypes' => __('All Types', 'event-interactive-map'),
            'viewDetails' => __('View Details', 'event-interactive-map'),
            'locateMe' => __('Locate Me', 'event-interactive-map'),
            'searchPlaceholder' => __('Search location...', 'event-interactive-map'),
            'geolocationError' => __('Unable to get your location', 'event-interactive-map'),
        ],
        'types' => [
            'concert' => __('Concert', 'event-interactive-map'),
            'exhibition' => __('Exhibition', 'event-interactive-map'),
            'conference' => __('Conference', 'event-interactive-map'),
            'workshop' => __('Workshop', 'event-interactive-map'),
            'festival' => __('Festival', 'event-interactive-map'),
            'sports' => __('Sports', 'event-interactive-map'),
            'other' => __('Other', 'event-interactive-map'),
        ]
    ]);
}
add_action('wp_enqueue_scripts', 'eim_enqueue_scripts');

/**
 * Enqueue admin scripts and styles
 */
function eim_enqueue_admin_scripts($hook) {
    global $post_type;

    // Only load on event_poi edit pages
    if (('post.php' === $hook || 'post-new.php' === $hook) && 'event_poi' === $post_type) {
        // Enqueue jQuery first (required dependency)
        wp_enqueue_script('jquery');

        // Enqueue Leaflet CSS
        wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', [], '1.9.4');

        // Enqueue Leaflet.Draw CSS for drawing tools
        wp_enqueue_style('leaflet-draw-css', 'https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css', ['leaflet-css'], '1.0.4');

        // Enqueue Leaflet JS with jQuery as dependency
        wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', ['jquery'], '1.9.4', false);

        // Enqueue Leaflet.Draw JS for drawing tools
        wp_enqueue_script('leaflet-draw-js', 'https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js', ['leaflet-js'], '1.0.4', false);

        // Add inline style to ensure proper loading
        wp_add_inline_style('leaflet-css', '#eim-admin-map { height: 500px; width: 100%; }');
    }
}
add_action('admin_enqueue_scripts', 'eim_enqueue_admin_scripts');

/**
 * Shortcode to display the event map
 */
function eim_shortcode_map($atts) {
    $atts = shortcode_atts([
        'height' => '500px',
        'zoom' => '13',
        'center_lat' => '',
        'center_lng' => '',
    ], $atts);

    ob_start();
    include EIM_PLUGIN_DIR . 'templates/map-template.php';
    return ob_get_clean();
}
add_shortcode('event_map', 'eim_shortcode_map');

/**
 * Activation hook
 */
function eim_activate() {
    // Trigger CPT registration
    eim_register_poi_cpt();

    // Flush rewrite rules
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'eim_activate');

/**
 * Deactivation hook
 */
function eim_deactivate() {
    // Flush rewrite rules
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'eim_deactivate');