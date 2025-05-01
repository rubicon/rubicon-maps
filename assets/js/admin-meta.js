document.addEventListener("DOMContentLoaded", function () {
  const fields = {
    street: document.getElementById("street"),
    city: document.getElementById("city"),
    state: document.getElementById("state"),
    zip: document.getElementById("zip"),
    country: document.getElementById("country"),
    latitude: document.getElementById("latitude"),
    longitude: document.getElementById("longitude")
  };

  const searchInput = document.createElement("input");
  searchInput.setAttribute("type", "text");
  searchInput.setAttribute("placeholder", "Search address...");
  searchInput.classList.add("regular-text");
  searchInput.style.marginBottom = "12px";
  searchInput.style.display = "block";

  const metaBox = document.getElementById("rubicon_location_meta");
  if (metaBox) metaBox.prepend(searchInput);

  searchInput.addEventListener("change", function () {
    const query = encodeURIComponent(searchInput.value.trim());
    if (!query) return;

    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}`)
      .then((res) => res.json())
      .then((data) => {
        if (data.length === 0) return;

        const place = data[0];
        fields.latitude.value = place.lat;
        fields.longitude.value = place.lon;

        const parts = (place.display_name || "").split(",");
        fields.street.value = parts[0] || "";
        fields.city.value = parts[1] || "";
        fields.state.value = parts[2] || "";
        fields.zip.value = place.postcode || "";
        fields.country.value = parts[parts.length - 1] || "";
      })
      .catch((err) => console.error("Nominatim error:", err));
  });
});
