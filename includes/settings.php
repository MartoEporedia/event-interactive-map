<?php
defined('ABSPATH') or die();

// ── Settings registration ──────────────────────────────────────────────────

add_action('admin_init', function() {
    register_setting('eim_settings', 'eim_marker_icon', [
        'type'              => 'string',
        'default'           => 'dashicons-tickets-alt',
        'sanitize_callback' => 'wp_kses_post',
    ]);

    add_settings_section('eim_markers', __('Map Markers', 'event-interactive-map'),
        function() { echo '<p>' . esc_html__('Default icon used for POIs without an assigned category. Icon and colour per category are set directly in POI → POI Categories.', 'event-interactive-map') . '</p>'; },
        'eim_settings');
    add_settings_field('eim_marker_icon', __('Default Icon', 'event-interactive-map'),
        'eim_marker_icon_field', 'eim_settings', 'eim_markers');
});

function eim_marker_icon_field() {
    $val = get_option('eim_marker_icon', 'dashicons-tickets-alt');

    $presets = [
        'dashicons-tickets-alt'  => 'Ticket',
        'dashicons-format-audio' => 'Audio',
        'dashicons-megaphone'    => 'Megaphone',
        'dashicons-star-filled'  => 'Star',
        'dashicons-location'     => 'Pin',
        'dashicons-businessman'  => 'Person',
        '🎸' => 'Guitar',
        '🎵' => 'Note',
        '🎤' => 'Microphone',
        '🍺' => 'Beer',
        '📍' => 'Pin',
    ];
    ?>
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px">
    <?php foreach ($presets as $icon => $label):
        $is_dash = str_starts_with($icon, 'dashicons-'); ?>
        <button type="button" class="button"
            onclick="document.getElementById('eim_marker_icon').value=<?php echo esc_attr(json_encode($icon)); ?>;eimIconPreview(<?php echo esc_attr(json_encode($icon)); ?>)"
            style="display:flex;flex-direction:column;align-items:center;gap:4px;height:auto;padding:8px 12px;min-width:64px">
            <?php if ($is_dash): ?>
                <span class="dashicons <?php echo esc_attr($icon); ?>" style="font-size:22px;width:22px;height:22px"></span>
            <?php else: ?>
                <span style="font-size:22px;line-height:1.2"><?php echo esc_html($icon); ?></span>
            <?php endif; ?>
            <span style="font-size:11px"><?php echo esc_html($label); ?></span>
        </button>
    <?php endforeach; ?>
    </div>

    <p>
        <input type="text" name="eim_marker_icon" id="eim_marker_icon"
            value="<?php echo esc_attr($val); ?>" class="regular-text"
            oninput="eimIconPreview(this.value)">
        <br><span class="description">
            <?php _e('Dashicon class (e.g. <code>dashicons-tickets-alt</code>) or custom emoji / HTML', 'event-interactive-map'); ?>
        </span>
    </p>

    <p>
        <strong><?php _e('Preview:', 'event-interactive-map'); ?></strong>&nbsp;
        <span id="eim-icon-preview" style="font-size:26px;vertical-align:middle"></span>
    </p>

    <script>
    function eimIconPreview(v) {
        const p = document.getElementById('eim-icon-preview');
        if (!v) { p.innerHTML = '&mdash;'; return; }
        if (v.startsWith('dashicons-')) {
            p.innerHTML = '<span class="dashicons ' + v.replace(/[^a-z0-9-]/g, '') + '" style="font-size:26px;width:26px;height:26px;vertical-align:middle"></span>';
        } else {
            p.textContent = v;
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        eimIconPreview(document.getElementById('eim_marker_icon').value);
    });
    </script>
    <?php
}

// ── Admin menu ─────────────────────────────────────────────────────────────

add_action('admin_menu', function() {
    add_options_page(
        __('Event Interactive Map', 'event-interactive-map'),
        __('Event Map', 'event-interactive-map'),
        'manage_options',
        'eim-settings',
        'eim_render_settings_page'
    );
});

function eim_render_settings_page() {
    if (!current_user_can('manage_options')) return;
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('eim_settings');
            do_settings_sections('eim_settings');
            submit_button(__('Save Settings', 'event-interactive-map'));
            ?>
        </form>
    </div>
    <?php
}
