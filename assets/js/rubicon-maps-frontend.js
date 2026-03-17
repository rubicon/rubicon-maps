document.addEventListener("DOMContentLoaded", function () {
  const maps = document.querySelectorAll("[data-rubicon-map='1']");

  maps.forEach((mapRoot) => {
    const configJson = mapRoot.getAttribute("data-rubicon-config");

    if (!configJson) {
      return;
    }

    let config = {};

    try {
      config = JSON.parse(configJson);
    } catch (error) {
      console.error("Rubicon Maps config parse error", error);
      return;
    }

    const canvas = mapRoot.querySelector(".rubicon-maps__canvas");

    if (!canvas) {
      return;
    }

    initializeMap(mapRoot, canvas, config);
  });

  function initializeMap(mapRoot, canvas, config) {
    const provider = config.provider === "google" && typeof google !== "undefined" ? "google" : "leaflet";
    const lat = Number.parseFloat(config.lat || 0);
    const lng = Number.parseFloat(config.lng || 0);
    const zoom = Number.parseInt(config.zoom || 9, 10);
    const instanceId = config.instanceId;
    const scrollWheelZoom = Boolean(config.scrollWheelZoom);
    const tileUrl = config.tileUrl || "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png";

    let map = null;
    let markerIndex = new Map();

    if (provider === "google") {
      map = new google.maps.Map(canvas, {
        center: { lat, lng },
        zoom,
        scrollwheel: scrollWheelZoom,
      });
    } else if (typeof L !== "undefined") {
      map = L.map(canvas, { scrollWheelZoom }).setView([lat, lng], zoom);
      L.tileLayer(tileUrl, {
        attribution: "&copy; OpenStreetMap contributors",
      }).addTo(map);
    }

    if (!map) {
      return;
    }

    fetchLocations(config)
      .then((locations) => {
        markerIndex = renderMarkers(provider, map, locations);
        fitMapToMarkers(provider, map, markerIndex);
        bindListInteractions(instanceId, provider, map, markerIndex);
      })
      .catch((error) => console.error("Rubicon Maps location load error", error));
  }

  function fetchLocations(config) {
    const params = new URLSearchParams();

    if (config.category) {
      params.set("category", config.category);
    }

    if (config.region) {
      params.set("region", config.region);
    }

    if (config.locationIds) {
      params.set("location_ids", config.locationIds);
    }

    const baseUrl = (window.rubiconMapsConfig && window.rubiconMapsConfig.restBase) || config.endpoint;
    const url = params.toString() ? `${baseUrl}?${params.toString()}` : baseUrl;

    return fetch(url).then((response) => response.json());
  }

  function renderMarkers(provider, map, locations) {
    const markerIndex = new Map();

    locations.forEach((location) => {
      if (!location.latitude || !location.longitude) {
        return;
      }

      if (provider === "google") {
        const marker = new google.maps.Marker({
          position: {
            lat: Number.parseFloat(location.latitude),
            lng: Number.parseFloat(location.longitude),
          },
          map,
          title: location.title,
          icon: location.marker_icon_url || undefined,
        });

        const infoWindow = new google.maps.InfoWindow({
          content: buildPopupHtml(location),
        });

        marker.addListener("click", function () {
          infoWindow.open(map, marker);
          dispatchLocationClick(location);
        });

        markerIndex.set(String(location.id), { marker, infoWindow, location });
      } else {
        const markerOptions = {};

        if (location.marker_icon_url) {
          markerOptions.icon = L.icon({
            iconUrl: location.marker_icon_url,
            iconSize: [36, 36],
            iconAnchor: [18, 36],
            popupAnchor: [0, -32],
            shadowUrl: typeof rubiconMapsConfig !== "undefined" ? rubiconMapsConfig.leafletMarkerShadow : undefined,
          });
        }

        const marker = L.marker([Number.parseFloat(location.latitude), Number.parseFloat(location.longitude)], markerOptions).addTo(map);
        marker.bindPopup(buildPopupHtml(location));
        marker.on("click", function () {
          dispatchLocationClick(location);
        });
        markerIndex.set(String(location.id), { marker, location });
      }
    });

    return markerIndex;
  }

  function fitMapToMarkers(provider, map, markerIndex) {
    const entries = Array.from(markerIndex.values());

    if (entries.length < 1) {
      return;
    }

    if (entries.length === 1) {
      const { location } = entries[0];
      const lat = Number.parseFloat(location.latitude);
      const lng = Number.parseFloat(location.longitude);

      if (provider === "google") {
        map.panTo({ lat, lng });
        return;
      }

      map.setView([lat, lng], map.getZoom());
      return;
    }

    if (provider === "google") {
      const bounds = new google.maps.LatLngBounds();
      entries.forEach(({ location }) => {
        bounds.extend({
          lat: Number.parseFloat(location.latitude),
          lng: Number.parseFloat(location.longitude),
        });
      });
      map.fitBounds(bounds);
      return;
    }

    const bounds = entries.map(({ location }) => [
      Number.parseFloat(location.latitude),
      Number.parseFloat(location.longitude),
    ]);
    map.fitBounds(bounds, { padding: [24, 24] });
  }

  function bindListInteractions(instanceId, provider, map, markerIndex) {
    const listRoot = document.querySelector(`[data-rubicon-location-list='1'][data-instance-id='${instanceId}']`);

    if (!listRoot) {
      return;
    }

    const items = listRoot.querySelectorAll("[data-location-id]");

    items.forEach((item) => {
      const activate = () => {
        const markerEntry = markerIndex.get(item.getAttribute("data-location-id"));

        if (!markerEntry) {
          return;
        }

        const location = markerEntry.location;

        if (provider === "google") {
          map.panTo({
            lat: Number.parseFloat(location.latitude),
            lng: Number.parseFloat(location.longitude),
          });
          map.setZoom(Math.max(map.getZoom(), 12));
          markerEntry.infoWindow.open(map, markerEntry.marker);
        } else {
          map.setView([Number.parseFloat(location.latitude), Number.parseFloat(location.longitude)], Math.max(map.getZoom(), 12));
          markerEntry.marker.openPopup();
        }

        highlightListItem(listRoot, item);
        dispatchLocationClick(location);
      };

      item.addEventListener("click", activate);
      item.addEventListener("keydown", (event) => {
        if (event.key === "Enter" || event.key === " ") {
          event.preventDefault();
          activate();
        }
      });
    });
  }

  function highlightListItem(listRoot, activeItem) {
    listRoot.querySelectorAll(".rubicon-location-list__item").forEach((item) => {
      item.classList.toggle("is-active", item === activeItem);
    });
  }

  function buildPopupHtml(location) {
    const address = location.formatted_address ? `<div class="rubicon-maps__popup-address">${escapeHtml(location.formatted_address)}</div>` : "";
    const excerpt = location.excerpt ? `<div class="rubicon-maps__popup-excerpt">${escapeHtml(location.excerpt)}</div>` : "";

    return `<div class="rubicon-maps__popup"><strong>${escapeHtml(location.title)}</strong>${address}${excerpt}</div>`;
  }

  function dispatchLocationClick(location) {
    window.dispatchEvent(
      new CustomEvent("rubiconMaps.locationClick", {
        detail: location,
      })
    );
  }

  function escapeHtml(value) {
    return String(value)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }
});
