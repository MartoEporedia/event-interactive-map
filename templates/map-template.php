<div class="eim-map-container" style="height: <?php echo esc_attr($atts['height']); ?>;">
    <div class="eim-map-controls">
        <div class="eim-control-group">
            <input type="text" id="eim-search-input" placeholder="<?php echo esc_attr__('Search location...', 'event-interactive-map'); ?>" />
            <button id="eim-search-btn" class="eim-btn" title="<?php esc_attr_e('Search', 'event-interactive-map'); ?>">
                <span class="dashicons dashicons-search"></span>
            </button>
        </div>

        <div class="eim-control-group">
            <select id="eim-type-filter">
                <option value=""><?php _e('All Types', 'event-interactive-map'); ?></option>
                <option value="concert"><?php _e('Concert', 'event-interactive-map'); ?></option>
                <option value="exhibition"><?php _e('Exhibition', 'event-interactive-map'); ?></option>
                <option value="conference"><?php _e('Conference', 'event-interactive-map'); ?></option>
                <option value="workshop"><?php _e('Workshop', 'event-interactive-map'); ?></option>
                <option value="festival"><?php _e('Festival', 'event-interactive-map'); ?></option>
                <option value="sports"><?php _e('Sports', 'event-interactive-map'); ?></option>
                <option value="other"><?php _e('Other', 'event-interactive-map'); ?></option>
            </select>
        </div>

        <button id="eim-locate-btn" class="eim-btn" title="<?php esc_attr_e('Locate Me', 'event-interactive-map'); ?>">
            <span class="dashicons dashicons-location"></span>
        </button>

        <button id="eim-reset-btn" class="eim-btn" title="<?php esc_attr_e('Reset View', 'event-interactive-map'); ?>">
            <span class="dashicons dashicons-image-rotate"></span>
        </button>
    </div>

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