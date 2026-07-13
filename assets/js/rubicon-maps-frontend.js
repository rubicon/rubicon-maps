document.addEventListener("DOMContentLoaded", function () {
  const prefersReducedMotion = Boolean(
    window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches
  );

  const maps = document.querySelectorAll("[data-rubicon-map='1']");
  const lists = document.querySelectorAll("[data-rubicon-location-list='1']");

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

  lists.forEach((listRoot) => {
    if (listRoot.getAttribute("data-use-fixed-height") === "1" && !listRoot.style.height) {
      applyListHeight(listRoot, listRoot.getAttribute("data-default-height") || "480px");
    }
  });

  function initializeMap(mapRoot, canvas, config) {
    const provider = config.provider === "google" && typeof google !== "undefined" ? "google" : "leaflet";
    const lat = Number.parseFloat(config.lat || 0);
    const lng = Number.parseFloat(config.lng || 0);
    const zoom = Number.parseInt(config.zoom || 9, 10);
    const syncKey = mapRoot.getAttribute("data-sync-id") || config.syncId || config.instanceId;
    const scrollWheelZoom = Boolean(config.scrollWheelZoom);
    const zoomControl = config.zoomControl !== false;
    const doubleClickZoom = config.doubleClickZoom !== false;
    const tileUrl = config.tileUrl || "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png";
    const viewportMode = config.viewportMode || "auto_fit";

    let map = null;
    let markerBundle = { markerIndex: new Map(), clusterGroup: null };

    if (provider === "google") {
      map = new google.maps.Map(canvas, {
        center: { lat, lng },
        zoom,
        scrollwheel: scrollWheelZoom,
        zoomControl,
        disableDoubleClickZoom: !doubleClickZoom,
      });
    } else if (typeof L !== "undefined") {
      map = L.map(canvas, {
        scrollWheelZoom,
        zoomControl,
        doubleClickZoom,
      }).setView([lat, lng], zoom, { animate: !prefersReducedMotion });
      L.tileLayer(tileUrl, {
        attribution: "&copy; OpenStreetMap contributors",
      }).addTo(map);

      if (config.closeOnMapClick) {
        map.on("click", function () {
          map.closePopup();
        });
      }
    }

    if (!map) {
      return;
    }

    function loadAndRenderLocations() {
      const strings = getStrings();

      setState(mapRoot, "loading", strings.loading);
      syncLocationListsLoading(syncKey);

      return fetchLocations(config)
        .then((locations) => {
          markerBundle = renderMarkers(provider, map, locations, config);

          if (viewportMode === "auto_fit") {
            fitMapToMarkers(provider, map, markerBundle.markerIndex, config);
          }

          setState(mapRoot, locations.length ? "ready" : "empty", locations.length ? "" : strings.emptyStandalone);
          syncLocationLists(syncKey, locations, config.height);
          bindListInteractions(syncKey, provider, map, markerBundle, mapRoot);
        })
        .catch((error) => {
          console.error("Rubicon Maps location load error", error);
          showMapError();
        });
    }

    function showMapError() {
      const strings = getStrings();

      setState(mapRoot, "error", strings.error);
      appendRetryButton(mapRoot.querySelector(".rubicon-maps__status"), loadAndRenderLocations);
      syncLocationListsError(syncKey, loadAndRenderLocations);
    }

    loadAndRenderLocations();
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

  function renderMarkers(provider, map, locations, config) {
    const markerIndex = new Map();
    const enableClustering = provider === "leaflet" && Boolean(config.enableClustering) && typeof L.markerClusterGroup === "function";
    const clusterGroup = enableClustering
      ? L.markerClusterGroup({
          maxClusterRadius: Number.parseInt(config.clusterRadius || 100, 10),
          iconCreateFunction: buildClusterIcon,
        })
      : null;

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
          maxWidth: Number.parseInt(config.popupMaxWidth || 320, 10),
        });

        bindGooglePopupEvents(marker, infoWindow, map, location, config);

        markerIndex.set(String(location.id), { marker, infoWindow, location });
      } else {
        const markerOptions = {};
        const iconUrl = location.marker_icon_url || defaultMarkerUrl();

        if (iconUrl) {
          markerOptions.icon = L.icon({
            iconUrl,
            iconSize: [36, 36],
            iconAnchor: [18, 36],
            popupAnchor: [0, -32],
            shadowUrl: typeof rubiconMapsConfig !== "undefined" ? rubiconMapsConfig.leafletMarkerShadow : undefined,
          });
        }

        const marker = L.marker([Number.parseFloat(location.latitude), Number.parseFloat(location.longitude)], markerOptions);
        marker.bindPopup(buildPopupHtml(location), {
          maxWidth: Number.parseInt(config.popupMaxWidth || 320, 10),
          closeOnClick: Boolean(config.closeOnMapClick),
          autoClose: Boolean(config.autoClosePopup) && !Boolean(config.openAllPopups),
        });
        bindLeafletPopupEvents(marker, location, config);
        markerIndex.set(String(location.id), { marker, location });

        if (clusterGroup) {
          clusterGroup.addLayer(marker);
        } else {
          marker.addTo(map);
        }
      }
    });

    if (clusterGroup) {
      map.addLayer(clusterGroup);
    }

    if (config.openAllPopups) {
      openAllMarkerPopups(provider, markerIndex, clusterGroup);
    }

    return { markerIndex, clusterGroup };
  }

  function buildClusterIcon(cluster) {
    const count = cluster.getChildCount();
    let sizeClass = "rtv-rm-cluster--sm";
    let size = 32;

    if (count >= 50) {
      sizeClass = "rtv-rm-cluster--lg";
      size = 48;
    } else if (count >= 10) {
      sizeClass = "rtv-rm-cluster--md";
      size = 40;
    }

    return L.divIcon({
      html: "<div>" + count + "</div>",
      className: "rtv-rm-cluster " + sizeClass,
      iconSize: [size, size],
      iconAnchor: [size / 2, size / 2],
    });
  }

  function defaultMarkerUrl() {
    return typeof rubiconMapsConfig !== "undefined" ? rubiconMapsConfig.defaultMarkerUrl : undefined;
  }

  function fitMapToMarkers(provider, map, markerIndex, config) {
    const entries = Array.from(markerIndex.values());
    const padding = Number.parseInt(config.autoFitPadding || 24, 10);

    if (entries.length < 1) {
      return;
    }

    if (entries.length === 1) {
      const { location } = entries[0];
      const lat = Number.parseFloat(location.latitude);
      const lng = Number.parseFloat(location.longitude);

      if (provider === "google") {
        map.panTo({ lat, lng });
        map.setZoom(Number.parseInt(config.zoom || map.getZoom(), 10));
        return;
      }

      map.setView([lat, lng], Number.parseInt(config.zoom || map.getZoom(), 10), { animate: !prefersReducedMotion });
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
    map.fitBounds(bounds, { padding: [padding, padding], animate: !prefersReducedMotion });
  }

  function bindListInteractions(syncKey, provider, map, markerBundle, mapRoot) {
    const markerIndex = markerBundle.markerIndex;
    const clusterGroup = markerBundle.clusterGroup;
    const listRoots = document.querySelectorAll(
      `[data-rubicon-location-list='1'][data-sync-id='${escapeSelector(syncKey)}']`
    );

    if (!listRoots.length) {
      return;
    }

    listRoots.forEach((listRoot) => {
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
            focusLeafletMarker(map, markerEntry.marker, clusterGroup);
          }

          highlightListItem(listRoot, item);
          dispatchLocationClick(location);
          announceFocusedLocation(mapRoot, location);
        };

        item.addEventListener("click", activate);
        item.addEventListener("keydown", (event) => {
          if (event.key === "Enter" || event.key === " ") {
            event.preventDefault();
            activate();
          }
        });
      });
    });
  }

  function announceFocusedLocation(mapRoot, location) {
    if (!mapRoot) {
      return;
    }

    const strings = getStrings();

    setState(mapRoot, "ready", formatString(strings.focusOnLocation, location.title));
  }

  function syncLocationListsLoading(syncKey) {
    const strings = getStrings();

    findSyncedListRoots(syncKey).forEach((listRoot) => {
      setState(listRoot, "loading", strings.loading);
    });
  }

  function syncLocationListsError(syncKey, retryHandler) {
    const strings = getStrings();

    findSyncedListRoots(syncKey).forEach((listRoot) => {
      setState(listRoot, "error", strings.error);
      appendRetryButton(listRoot.querySelector(".rubicon-location-list__status"), retryHandler);
    });
  }

  function syncLocationLists(syncKey, locations, mapHeight) {
    const strings = getStrings();

    findSyncedListRoots(syncKey).forEach((listRoot) => {
      const itemsContainer = ensureListItemsContainer(listRoot);
      const emptyState = listRoot.querySelector(".rubicon-location-list__empty");
      const showThumbnail = listRoot.getAttribute("data-show-thumbnail") === "1";

      if (emptyState) {
        emptyState.remove();
      }

      if (!locations.length) {
        itemsContainer.innerHTML = "";
        const empty = document.createElement("p");
        empty.className = "rubicon-location-list__empty";
        empty.textContent = strings.emptySynced;
        listRoot.appendChild(empty);
        setState(listRoot, "empty", strings.emptySynced);
      } else {
        itemsContainer.innerHTML = locations
          .map((location) => buildListItemHtml(location, showThumbnail))
          .join("");
        setState(listRoot, "ready", "");
      }

      if (listRoot.getAttribute("data-use-fixed-height") === "1" && !listRoot.getAttribute("data-height")) {
        applyListHeight(listRoot, mapHeight || listRoot.getAttribute("data-default-height") || "480px");
      }
    });
  }

  function findSyncedListRoots(syncKey) {
    if (!syncKey) {
      return [];
    }

    return Array.from(
      document.querySelectorAll(
        `[data-rubicon-location-list='1'][data-sync-id='${escapeSelector(syncKey)}'][data-sync-mode='follow-map']`
      )
    );
  }

  function highlightListItem(listRoot, activeItem) {
    listRoot.querySelectorAll(".rubicon-location-list__item").forEach((item) => {
      item.classList.toggle("is-active", item === activeItem);
    });
  }

  function applyListHeight(listRoot, height) {
    if (!height) {
      return;
    }

    listRoot.style.height = height;
    listRoot.classList.add("rubicon-location-list--scrollable");
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

  function bindLeafletPopupEvents(marker, location, config) {
    if (config.popupTrigger === "hover") {
      marker.on("mouseover", function () {
        marker.openPopup();
        dispatchLocationClick(location);
      });
      marker.on("mouseout", function () {
        if (!config.openAllPopups) {
          marker.closePopup();
        }
      });
      return;
    }

    marker.on("click", function () {
      dispatchLocationClick(location);
    });
  }

  function bindGooglePopupEvents(marker, infoWindow, map, location, config) {
    const open = () => {
      infoWindow.open(map, marker);
      dispatchLocationClick(location);
    };

    if (config.popupTrigger === "hover") {
      marker.addListener("mouseover", open);
      return;
    }

    marker.addListener("click", open);
  }

  function focusLeafletMarker(map, marker, clusterGroup) {
    const openMarker = () => {
      map.panTo(marker.getLatLng(), { animate: !prefersReducedMotion });
      marker.openPopup();
    };

    if (clusterGroup && typeof clusterGroup.zoomToShowLayer === "function") {
      clusterGroup.zoomToShowLayer(marker, openMarker);
      return;
    }

    map.setView(marker.getLatLng(), Math.max(map.getZoom(), 12), { animate: !prefersReducedMotion });
    openMarker();
  }

  function openAllMarkerPopups(provider, markerIndex, clusterGroup) {
    markerIndex.forEach((entry) => {
      if (provider === "google") {
        entry.infoWindow.open(entry.marker.getMap(), entry.marker);
        return;
      }

      if (clusterGroup && typeof clusterGroup.zoomToShowLayer === "function") {
        clusterGroup.zoomToShowLayer(entry.marker, () => {
          entry.marker.openPopup();
        });
        return;
      }

      entry.marker.openPopup();
    });
  }

  function ensureListItemsContainer(listRoot) {
    let itemsContainer = listRoot.querySelector(".rubicon-location-list__items");

    if (!itemsContainer) {
      itemsContainer = document.createElement("ul");
      itemsContainer.className = "rubicon-location-list__items";
      itemsContainer.setAttribute("role", "list");
      listRoot.appendChild(itemsContainer);
    }

    return itemsContainer;
  }

  function buildListItemHtml(location, showThumbnail) {
    const strings = getStrings();
    const thumbnail = showThumbnail && location.image_url
      ? `<img class="rubicon-location-list__thumb" src="${escapeHtml(escapeUrl(location.image_url))}" alt="">`
      : "";
    const address = location.formatted_address
      ? `<div class="rubicon-location-list__meta">${escapeHtml(location.formatted_address)}</div>`
      : "";
    const excerpt = location.excerpt
      ? `<div class="rubicon-location-list__excerpt">${escapeHtml(location.excerpt)}</div>`
      : "";

    return `
      <li
        class="rubicon-location-list__item"
        data-location-id="${escapeHtml(location.id)}"
        data-lat="${escapeHtml(location.latitude || "")}"
        data-lng="${escapeHtml(location.longitude || "")}"
        tabindex="0"
        role="button"
        aria-label="${escapeHtml(formatString(strings.focusOnLocation, location.title))}"
      >
        ${thumbnail}
        <strong class="rubicon-location-list__title">${escapeHtml(location.title)}</strong>
        ${address}
        ${excerpt}
      </li>
    `;
  }

  function getStrings() {
    return window.rubiconMapsStrings || {};
  }

  function formatString(template, value) {
    return String(template || "").replace("%s", value);
  }

  function defaultStateMessage(state) {
    const strings = getStrings();

    switch (state) {
      case "loading":
        return strings.loading || "";
      case "error":
        return strings.error || "";
      case "empty":
        return strings.emptyStandalone || "";
      default:
        return "";
    }
  }

  function setState(root, state, message) {
    if (!root) {
      return;
    }

    root.setAttribute("data-state", state);

    const statusNode = root.querySelector(".rubicon-maps__status, .rubicon-location-list__status");

    if (!statusNode) {
      return;
    }

    statusNode.textContent = typeof message === "string" ? message : defaultStateMessage(state);
  }

  function appendRetryButton(statusNode, onRetry) {
    if (!statusNode) {
      return;
    }

    const strings = getStrings();
    const retryButton = document.createElement("button");

    retryButton.type = "button";
    retryButton.className = "rtv-rm-retry-button";
    retryButton.textContent = strings.retry || "";
    retryButton.addEventListener("click", onRetry);

    statusNode.appendChild(retryButton);
  }

  function escapeSelector(value) {
    if (typeof CSS !== "undefined" && typeof CSS.escape === "function") {
      return CSS.escape(String(value));
    }

    return String(value).replace(/"/g, '\\"');
  }

  function escapeUrl(value) {
    const url = String(value || "");

    if (/^https?:\/\//i.test(url) || url.charAt(0) === "/") {
      return url;
    }

    return "";
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
