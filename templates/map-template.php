<div class="eim-wrapper">
    <div class="eim-map-controls">
        <div class="eim-controls-header">
            <span class="eim-controls-label"><?php esc_html_e('Cerca e filtra', 'event-interactive-map'); ?></span>
            <button id="eim-toggle-controls" class="eim-toggle-btn" title="<?php esc_attr_e('Mostra/nascondi filtri', 'event-interactive-map'); ?>" aria-expanded="true">
                <span class="dashicons dashicons-arrow-up-alt2"></span>
            </button>
        </div>

        <div class="eim-controls-body">
            <div class="eim-control-group">
                <input type="text" id="eim-search-input" placeholder="<?php echo esc_attr__('Search artist...', 'event-interactive-map'); ?>" />
                <button id="eim-search-btn" class="eim-btn" title="<?php esc_attr_e('Search', 'event-interactive-map'); ?>">
                    <span class="dashicons dashicons-search"></span>
                </button>
                <button id="eim-locate-btn" class="eim-btn" title="<?php esc_attr_e('Locate Me', 'event-interactive-map'); ?>">
                    <span class="dashicons dashicons-location"></span>
                </button>
                <button id="eim-reset-btn" class="eim-btn" title="<?php esc_attr_e('Reset View', 'event-interactive-map'); ?>">
                    <span class="dashicons dashicons-image-rotate"></span>
                </button>
            </div>

            <div id="eim-day-filter" class="eim-day-filter-group" style="display:none;"></div>
        </div>
    </div>

    <div class="eim-map-container" style="height: <?php echo esc_attr($atts['height']); ?>;">
        <div id="event-map" data-zoom="<?php echo esc_attr($atts['zoom']); ?>" data-center-lat="<?php echo esc_attr($atts['center_lat']); ?>" data-center-lng="<?php echo esc_attr($atts['center_lng']); ?>"></div>

        <div id="eim-loading" class="eim-loading">
            <div class="eim-spinner"></div>
            <p><?php _e('Loading events...', 'event-interactive-map'); ?></p>
        </div>

        <div id="eim-error" class="eim-error" style="display: none;">
            <p><?php _e('Error loading events. Please try again.', 'event-interactive-map'); ?></p>
            <button id="eim-retry-btn" class="eim-btn-primary"><?php _e('Retry', 'event-interactive-map'); ?></button>
        </div>
    </div>
</div>
