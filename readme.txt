=== Event Interactive Map ===
Contributors: martoEporedia
Tags: map, event, leaflet, poi, festival, interactive
Requires at least: 6.0
Tested up to: 6.7
Stable tag: 1.6.5
Requires PHP: 8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

An interactive Leaflet map for events and festivals: add Points of Interest with programmes, filter by day, and search artists on the map.

== Description ==

**Event Interactive Map** lets you publish an interactive map on any page or post using a simple shortcode. It is built around a **Points of Interest (POI)** custom post type and integrates with [Leaflet.js](https://leafletjs.com/) and MarkerCluster for a smooth, mobile-friendly experience.

**Key features:**

* **Custom Post Type** – *Event POI* with title, description, coordinates (set via an admin map click), address geocoding (Nominatim), event type, date and time.
* **Programme / Schedule repeater** – Attach multiple artist/event slots to each POI, each with a day label, date, time, performer name, link and optional notes. Day values are customisable via the `eim_program_days` filter.
* **POI Categories** – A hierarchical taxonomy (*poi_category*) lets you group POIs (stages, food stands, toilets, parking…). Each category has a custom **icon** (emoji or Dashicon class) and **colour** set directly on the category edit page.
* **Map Sets** – Group POIs into separate maps using the *map_set* taxonomy. Display a specific set with `[event_map group="my-set"]`.
* **Day filter** – Buttons automatically appear for every unique day found in the programme data. Click a day to show only the POIs active that day.
* **Artist autocomplete search** – Type an artist name to get suggestions with venue and time; clicking focuses the map and opens the popup.
* **Geolocation** – A "Locate Me" button centres the map on the visitor's position.
* **Mobile-friendly** – Controls bar collapses with a toggle button; uses `position: sticky` so the map remains fully visible.
* **Default marker icon** – Configurable per-site under *Settings → Event Map*; falls back to a clean SVG location pin for uncategorised POIs.
* **REST API** – A custom endpoint (`/eim/v1/pois`) powers the frontend; supports importing POIs programmatically via POST.
* **Translations ready** – Ships with an Italian (`it_IT`) translation; a `.pot` template is included for other languages.

**Shortcode:**

```
[event_map height="500px" zoom="14" center_lat="45.07" center_lng="7.68" group="my-set"]
```

All parameters are optional. Omit `group` to display all POIs.

== Installation ==

1. Upload the `event-interactive-map` folder to `/wp-content/plugins/`.
2. Activate the plugin through the *Plugins* menu in WordPress.
3. Go to *POI → Add New Event POI* to create your first point of interest.
4. Add the `[event_map]` shortcode to any page or post.
5. *(Optional)* Go to *Settings → Event Map* to set a default marker icon.
6. *(Optional)* Create categories under *POI → POI Categories* and assign an icon and colour to each.

**Customising programme days:**

By default the admin day selector shows *Friday, Saturday, Sunday*. Override this with a filter in your theme or plugin:

```php
add_filter( 'eim_program_days', function() {
    return [ 'Thursday', 'Friday', 'Saturday' ];
} );
```

== Frequently Asked Questions ==

= How do I display only some POIs on a given page? =

Create a *Map Set* term (POI → Map Sets), assign it to the relevant POIs, then use the `group` parameter: `[event_map group="my-set-slug"]`.

= Can I use emoji as category icons? =

Yes. On the category edit page, click any emoji preset or type your own emoji directly in the *Icon* field. Alternatively, use any Dashicon class (e.g. `dashicons-location`).

= The map is not showing on my page. =

Make sure Leaflet CSS and JS are not blocked by your theme. If you see a grey box, check the browser console for errors. The plugin enqueues Leaflet from a CDN (`unpkg.com`); if your server blocks external scripts, host Leaflet locally and dequeue the plugin's version.

= How do I add POIs programmatically? =

Send a POST request to `/wp-json/eim/v1/pois` with a valid nonce and the fields `title`, `lat`, `lng`, `map_set` (slug), and optionally `category` (slug). Authentication requires an application password.

= Does it work without an internet connection? =

The map tiles come from OpenStreetMap (CDN). The plugin scripts and Leaflet itself are loaded from `unpkg.com`. For offline use, self-host these assets and update the enqueue URLs.

== Screenshots ==

1. Frontend map with day filter and artist search.
2. Mobile view with collapsed filter bar.
3. POI edit screen with programme repeater and map picker.
4. POI Category edit page with icon and colour presets.
5. Settings page for the default marker icon.

== Changelog ==

= 1.6.0 =
* All UI strings are now in English; Italian translation ships in `languages/it_IT`.
* Added `eim_program_days` filter to customise the programme day list.
* Fixed JavaScript syntax error on the POI Category edit page (emoji in `onclick` attribute broke HTML parsing).
* Fixed same issue in the Settings page marker-icon presets.
* Existing day values not in the DAYS list are preserved as a fallback option in the slot dropdown.
* `.pot` translation template added.

= 1.5.1 =
* Default marker for uncategorised POIs changed to an inline SVG location pin.
* Category icon/colour moved from central settings table to the taxonomy term edit page (term meta).
* Mobile filter bar: sticky positioning, collapsible with smooth animation.

= 1.5.0 =
* POI Categories taxonomy (`poi_category`) with per-category icon and colour.
* Admin settings page for default marker icon.

= 1.4.0 =
* MarkerCluster integration.
* Autocomplete artist search with day/time metadata.

= 1.3.0 =
* Day filter buttons generated dynamically from programme data.
* Map Set taxonomy for multi-map support.

= 1.2.0 =
* Programme/Schedule repeater meta box.
* REST API endpoint (`eim/v1/pois`) with nonce authentication.

= 1.1.0 =
* Admin map picker with Nominatim address search.
* Geolocation button.

= 1.0.0 =
* Initial release: Event POI CPT, Leaflet map, shortcode.

== Upgrade Notice ==

= 1.6.0 =
If your site uses Italian day names (Venerdì, Sabato, Domenica) in POI programmes, add the `eim_program_days` filter to your theme's `functions.php` to restore them in the admin dropdown. Existing stored values are unaffected.
