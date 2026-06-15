<?php
defined('ABSPATH') or die();

// ── Settings registration ──────────────────────────────────────────────────

add_action('admin_init', function() {
    register_setting('eim_settings', 'eim_marker_icon', [
        'type'              => 'string',
        'default'           => 'dashicons-tickets-alt',
        'sanitize_callback' => 'wp_kses_post',
    ]);

    register_setting('eim_settings', 'eim_category_icons', [
        'type'              => 'string',
        'default'           => '{}',
        'sanitize_callback' => function($val) {
            $decoded = json_decode(wp_unslash($val), true);
            if (!is_array($decoded)) return '{}';
            $clean = [];
            foreach ($decoded as $slug => $cfg) {
                $clean[sanitize_title($slug)] = [
                    'icon'  => wp_kses_post($cfg['icon']  ?? ''),
                    'color' => sanitize_hex_color($cfg['color'] ?? '') ?: '#e67e22',
                ];
            }
            return wp_json_encode($clean, JSON_UNESCAPED_UNICODE);
        },
    ]);

    add_settings_section('eim_markers', __('Marcatori mappa', 'event-interactive-map'), null, 'eim_settings');
    add_settings_field('eim_marker_icon', __('Icona predefinita', 'event-interactive-map'),
        'eim_marker_icon_field', 'eim_settings', 'eim_markers');

    add_settings_section('eim_categories', __('Categorie POI', 'event-interactive-map'),
        function() { echo '<p>' . esc_html__('Per ogni categoria puoi impostare un\'icona e un colore personalizzati. Le categorie si gestiscono da POI → Categorie POI.', 'event-interactive-map') . '</p>'; },
        'eim_settings');
    add_settings_field('eim_category_icons', __('Icone per categoria', 'event-interactive-map'),
        'eim_category_icons_field', 'eim_settings', 'eim_categories');
});

function eim_marker_icon_field() {
    $val = get_option('eim_marker_icon', 'dashicons-tickets-alt');

    $presets = [
        'dashicons-tickets-alt'  => 'Biglietto',
        'dashicons-format-audio' => 'Audio',
        'dashicons-megaphone'    => 'Megafono',
        'dashicons-star-filled'  => 'Stella',
        'dashicons-location'     => 'Pin',
        'dashicons-businessman'  => 'Persona',
        '🎸' => 'Chitarra',
        '🎵' => 'Nota',
        '🎤' => 'Microfono',
        '🍺' => 'Birra',
        '📍' => 'Segnalino',
    ];
    ?>
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px">
    <?php foreach ($presets as $icon => $label):
        $is_dash = str_starts_with($icon, 'dashicons-'); ?>
        <button type="button" class="button"
            onclick="document.getElementById('eim_marker_icon').value=<?php echo json_encode($icon); ?>;eimIconPreview(<?php echo json_encode($icon); ?>)"
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
            <?php _e('Classe Dashicon (es. <code>dashicons-tickets-alt</code>) oppure emoji/HTML personalizzato', 'event-interactive-map'); ?>
        </span>
    </p>

    <p>
        <strong><?php _e('Anteprima:', 'event-interactive-map'); ?></strong>&nbsp;
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

function eim_category_icons_field() {
    $saved = json_decode(get_option('eim_category_icons', '{}'), true) ?: [];
    $terms = get_terms(['taxonomy' => 'poi_category', 'hide_empty' => false]);

    if (empty($terms) || is_wp_error($terms)) {
        echo '<p class="description">' . esc_html__('Nessuna categoria trovata. Aggiungile da POI → Categorie POI, poi torna qui.', 'event-interactive-map') . '</p>';
        echo '<input type="hidden" name="eim_category_icons" value="{}"/>';
        return;
    }

    $icon_presets = [
        '🎵' => 'Musica', '🎸' => 'Chitarra', '🎤' => 'Microfono',
        '🍺' => 'Birra',  '🍕' => 'Cibo',     '☕' => 'Bar',
        '🚻' => 'Servizi','🅿️' => 'Parcheggio','ℹ️' => 'Info',
        '🛍️' => 'Stand',  '🎪' => 'Tenda',    '🌳' => 'Parco',
    ];
    ?>
    <table class="widefat fixed" style="max-width:680px">
        <thead><tr>
            <th style="width:180px"><?php _e('Categoria', 'event-interactive-map'); ?></th>
            <th style="width:260px"><?php _e('Icona', 'event-interactive-map'); ?></th>
            <th style="width:130px"><?php _e('Colore marcatore', 'event-interactive-map'); ?></th>
            <th><?php _e('Anteprima', 'event-interactive-map'); ?></th>
        </tr></thead>
        <tbody>
    <?php foreach ($terms as $term):
        $slug  = $term->slug;
        $cfg   = $saved[$slug] ?? [];
        $icon  = $cfg['icon']  ?? '';
        $color = $cfg['color'] ?? '#e67e22';
        $fid   = 'eim_cat_' . esc_attr($slug);
        ?>
        <tr>
            <td><strong><?php echo esc_html($term->name); ?></strong><br>
                <small style="color:#888"><?php echo esc_html($slug); ?></small></td>
            <td>
                <div style="display:flex;gap:4px;flex-wrap:wrap;margin-bottom:6px">
                <?php foreach ($icon_presets as $ic => $lbl): ?>
                    <button type="button" class="button button-small"
                        title="<?php echo esc_attr($lbl); ?>"
                        onclick="(function(){var i=document.getElementById('<?php echo esc_js($fid); ?>');i.value=<?php echo json_encode($ic); ?>;eimCatPreview('<?php echo esc_js($slug); ?>')})()"
                        style="padding:2px 5px;font-size:15px;line-height:1.4"><?php echo esc_html($ic); ?></button>
                <?php endforeach; ?>
                </div>
                <input type="text" id="<?php echo $fid; ?>"
                    name="eim_category_icons[<?php echo esc_attr($slug); ?>][icon]"
                    value="<?php echo esc_attr($icon); ?>"
                    class="regular-text" style="width:100%;max-width:220px"
                    oninput="eimCatPreview('<?php echo esc_js($slug); ?>')"
                    placeholder="emoji o dashicons-*">
            </td>
            <td>
                <input type="color"
                    name="eim_category_icons[<?php echo esc_attr($slug); ?>][color]"
                    id="eim_cat_color_<?php echo esc_attr($slug); ?>"
                    value="<?php echo esc_attr($color); ?>"
                    oninput="eimCatPreview('<?php echo esc_js($slug); ?>')"
                    style="width:60px;height:36px;cursor:pointer;border:none">
            </td>
            <td>
                <span id="eim_cat_preview_<?php echo esc_attr($slug); ?>"
                    style="display:inline-flex;align-items:center;justify-content:center;
                           width:36px;height:36px;border-radius:50% 50% 50% 0;
                           transform:rotate(-45deg);border:3px solid <?php echo esc_attr($color); ?>;
                           background:<?php echo esc_attr($color); ?>22;font-size:16px">
                    <span style="transform:rotate(45deg)"><?php echo esc_html($icon ?: '📍'); ?></span>
                </span>
            </td>
        </tr>
    <?php endforeach; ?>
        </tbody>
    </table>

    <script>
    function eimCatPreview(slug) {
        var icon  = document.getElementById('eim_cat_' + slug).value || '📍';
        var color = document.getElementById('eim_cat_color_' + slug).value;
        var prev  = document.getElementById('eim_cat_preview_' + slug);
        prev.style.borderColor = color;
        prev.style.background  = color + '33';
        prev.querySelector('span').textContent = icon.startsWith('dashicons-') ? '' : icon;
    }
    </script>
    <?php

    // Hidden field to carry slugs even when no terms match (handles empty tables)
    echo '<input type="hidden" name="eim_category_icons[__check]" value="1">';
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
            submit_button(__('Salva impostazioni', 'event-interactive-map'));
            ?>
        </form>
    </div>
    <?php
}
