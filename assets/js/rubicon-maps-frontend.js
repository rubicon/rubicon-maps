document.addEventListener('DOMContentLoaded', function () {
    const locations = document.querySelectorAll('.rubicon-location-list-item');

    locations.forEach(item => {
        item.addEventListener('click', function () {
            const lat = parseFloat(this.dataset.lat);
            const lng = parseFloat(this.dataset.lng);

            if (window.rubiconMaps && rubiconMaps.map) {
                rubiconMaps.map.setView([lat, lng], 14);
            }
        });
    });
});
