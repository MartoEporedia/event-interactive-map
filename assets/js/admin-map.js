/* global L, eimAdminData */
(function ($) {
    'use strict';

    var map, marker;

    function init() {
        if (!$('#eim-admin-map').length) return;

        var lat = parseFloat($('#lat').val());
        var lng = parseFloat($('#lng').val());
        var hasCoords = !isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0;

        var startLat = hasCoords ? lat : parseFloat(eimAdminData.defaultLat);
        var startLng = hasCoords ? lng : parseFloat(eimAdminData.defaultLng);

        map = L.map('eim-admin-map', { zoomControl: true }).setView([startLat, startLng], hasCoords ? 15 : 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        if (hasCoords) {
            placeMarker(lat, lng, false);
        } else {
            showNoCoordsNotice(true);
        }

        map.on('click', function (e) {
            placeMarker(e.latlng.lat, e.latlng.lng, true);
        });

        $('#lat, #lng').on('change', function () {
            var newLat = parseFloat($('#lat').val());
            var newLng = parseFloat($('#lng').val());
            if (!isNaN(newLat) && !isNaN(newLng)) {
                placeMarker(newLat, newLng, false);
            }
        });

        $('#eim-geocode-btn').on('click', doGeocode);
        $('#event_address').on('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); doGeocode(); }
        });

        $('#eim-locate-me').on('click', geolocate);

        $(document).on('click', function (e) {
            if (!$(e.target).closest('#eim-geocode-result, #event_address, #eim-geocode-btn').length) {
                $('#eim-geocode-result').hide().empty();
            }
        });
    }

    function placeMarker(lat, lng, centerMap) {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], { draggable: true }).addTo(map);
            marker.on('dragend', function () {
                var pos = marker.getLatLng();
                updateFields(pos.lat, pos.lng);
            });
        }
        if (centerMap !== false) {
            map.setView([lat, lng], Math.max(map.getZoom(), 15));
        }
        updateFields(lat, lng);
        showNoCoordsNotice(false);
    }

    function updateFields(lat, lng) {
        $('#lat').val(lat.toFixed(6));
        $('#lng').val(lng.toFixed(6));
    }

    function showNoCoordsNotice(show) {
        if (show) {
            $('#eim-no-coords-notice').show();
        } else {
            $('#eim-no-coords-notice').hide();
        }
    }

    function doGeocode() {
        var address = $('#event_address').val().trim();
        if (!address) {
            $('#event_address').addClass('eim-field-error').focus();
            setTimeout(function () { $('#event_address').removeClass('eim-field-error'); }, 1500);
            return;
        }

        var $btn = $('#eim-geocode-btn');
        $btn.prop('disabled', true).text(eimAdminData.strings.searching);
        $('#eim-geocode-result').hide().empty();

        $.ajax({
            url: 'https://nominatim.openstreetmap.org/search',
            data: { q: address, format: 'json', limit: 5, addressdetails: 1 },
            headers: { 'Accept-Language': navigator.language || 'it,en' },
            success: function (data) {
                if (!data || !data.length) {
                    $('#eim-geocode-result')
                        .html('<p class="eim-geocode-msg eim-geocode-error">' + eimAdminData.strings.notFound + '</p>')
                        .show();
                    return;
                }
                if (data.length === 1) {
                    applyGeoResult(data[0]);
                } else {
                    showGeoResultsList(data);
                }
            },
            error: function () {
                $('#eim-geocode-result')
                    .html('<p class="eim-geocode-msg eim-geocode-error">' + eimAdminData.strings.searchError + '</p>')
                    .show();
            },
            complete: function () {
                $btn.prop('disabled', false).text(eimAdminData.strings.search);
            }
        });
    }

    function showGeoResultsList(results) {
        var $res = $('#eim-geocode-result').empty().show();
        var $ul = $('<ul class="eim-geocode-list"></ul>');
        results.slice(0, 5).forEach(function (r) {
            var $li = $('<li></li>').text(r.display_name);
            $li.on('mousedown', function (e) {
                e.preventDefault();
                applyGeoResult(r);
                $res.hide().empty();
            });
            $ul.append($li);
        });
        $res.append($ul);
    }

    function applyGeoResult(r) {
        var lat = parseFloat(r.lat);
        var lng = parseFloat(r.lon);
        placeMarker(lat, lng, true);
        if (!$('#event_address').val().trim()) {
            $('#event_address').val(r.display_name);
        }
    }

    function geolocate() {
        if (!navigator.geolocation) {
            showGeoLocateError(eimAdminData.strings.geoNotSupported);
            return;
        }
        var $btn = $('#eim-locate-me');
        $btn.prop('disabled', true).addClass('eim-btn-loading');
        navigator.geolocation.getCurrentPosition(
            function (pos) {
                placeMarker(pos.coords.latitude, pos.coords.longitude, true);
                $btn.prop('disabled', false).removeClass('eim-btn-loading');
            },
            function () {
                showGeoLocateError(eimAdminData.strings.geoError);
                $btn.prop('disabled', false).removeClass('eim-btn-loading');
            },
            { enableHighAccuracy: true, timeout: 8000 }
        );
    }

    function showGeoLocateError(msg) {
        $('#eim-geocode-result').html('<p class="eim-geocode-msg eim-geocode-error">' + msg + '</p>').show();
    }

    $(document).ready(init);

})(jQuery);
