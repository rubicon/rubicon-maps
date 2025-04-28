document.addEventListener("DOMContentLoaded", function () {
  const locations = document.querySelectorAll(".rubicon-location-list-item");

  locations.forEach((item) => {
    item.addEventListener("click", function () {
      const lat = parseFloat(this.dataset.lat);
      const lng = parseFloat(this.dataset.lng);

      if (window.rubiconMaps && rubiconMaps.map) {
        rubiconMaps.map.setView([lat, lng], 14);
      }
    });
  });
});
document.addEventListener("DOMContentLoaded", function () {
  const mapContainer = document.getElementById("rubicon-maps-container");

  if (mapContainer) {
    const lat = parseFloat(mapContainer.dataset.lat);
    const lng = parseFloat(mapContainer.dataset.lng);
    const zoom = parseInt(mapContainer.dataset.zoom);
    const provider = mapContainer.dataset.provider;

    if (provider === "google" && typeof google !== "undefined") {
      const map = new google.maps.Map(document.getElementById("rubicon-maps-map"), {
        center: { lat: lat, lng: lng },
        zoom: zoom,
      });
    }

    if (provider === "leaflet" && typeof L !== "undefined") {
      const map = L.map("rubicon-maps-map").setView([lat, lng], zoom);
      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "&copy; OpenStreetMap contributors",
      }).addTo(map);
    }
  }
});
