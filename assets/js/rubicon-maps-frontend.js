document.addEventListener("DOMContentLoaded", function () {
  const mapContainer = document.getElementById("rubicon-maps-container");

  if (mapContainer) {
    const lat = parseFloat(mapContainer.dataset.lat);
    const lng = parseFloat(mapContainer.dataset.lng);
    const zoom = parseInt(mapContainer.dataset.zoom);
    const provider = mapContainer.dataset.provider;
    const region = mapContainer.dataset.region;

    let map;

    if (provider === "google" && typeof google !== "undefined") {
      map = new google.maps.Map(document.getElementById("rubicon-maps-map"), {
        center: { lat: lat, lng: lng },
        zoom: zoom,
      });

      loadMarkers("google", map, region);
    } else if (provider === "leaflet" && typeof L !== "undefined") {
      map = L.map("rubicon-maps-map").setView([lat, lng], zoom);
      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "&copy; OpenStreetMap contributors",
      }).addTo(map);

      loadMarkers("leaflet", map, region);
    }
  }

  function loadMarkers(provider, map, region) {
    const url = "/wp-json/rubicon-maps/v1/locations" + (region ? "?region=" + encodeURIComponent(region) : "");

    fetch(url)
      .then((response) => response.json())
      .then((locations) => {
        locations.forEach((location) => {
          if (provider === "google") {
            const marker = new google.maps.Marker({
              position: { lat: parseFloat(location.latitude), lng: parseFloat(location.longitude) },
              map: map,
              title: location.title,
            });

            const infoWindow = new google.maps.InfoWindow({
              content: `<strong>${location.title}</strong><br>${location.address}`,
            });

            marker.addListener("click", function () {
              infoWindow.open(map, marker);
            });
          }

          if (provider === "leaflet") {
            const marker = L.marker([location.latitude, location.longitude]).addTo(map);
            marker.bindPopup(`<strong>${location.title}</strong><br>${location.address}`);
          }
        });
      })
      .catch((error) => console.error("Error loading locations:", error));
  }
});
