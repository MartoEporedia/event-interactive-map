document.addEventListener("DOMContentLoaded", function() {
    const map = L.map('event-map').setView([45.0, 7.6], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    fetch(eimData.restUrl, {
        headers: { 'X-WP-Nonce': eimData.nonce }
    })
    .then(response => response.json())
    .then(data => {
        data.forEach(poi => {
            if (poi.lat && poi.lng) {
                const marker = L.marker([poi.lat, poi.lng]).addTo(map);
                marker.bindPopup(`<strong>${poi.title}</strong><br>${poi.content}`);
            }
        });
    });
});