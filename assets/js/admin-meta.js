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

  const searchInput = document.getElementById("rubicon-geocode-query");
  const searchButton = document.getElementById("rubicon-geocode-button");
  const status = document.getElementById("rubicon-geocode-status");
  const results = document.getElementById("rubicon-geocode-results");
  let searchTimer = null;
  let latestResults = [];

  const setStatus = (message, isError = false) => {
    if (!status) {
      return;
    }

    status.textContent = message;
    status.style.color = isError ? "#b42318" : "";
  };

  if (searchInput && searchButton && window.rubiconMapsAdmin) {
    const clearResults = () => {
      latestResults = [];
      if (!results) {
        return;
      }

      results.innerHTML = "";
      results.hidden = true;
    };

    const applyResult = (place) => {
      Object.keys(fields).forEach((key) => {
        if (fields[key] && place[key]) {
          fields[key].value = place[key];
        }
      });

      clearResults();
      setStatus(place.display_name || window.rubiconMapsAdmin.strings.searchPrompt);
    };

    const renderResults = (places) => {
      latestResults = Array.isArray(places) ? places : [];

      if (!results) {
        return;
      }

      if (!latestResults.length) {
        results.innerHTML = "";
        results.hidden = true;
        return;
      }

      results.innerHTML = latestResults
        .map(
          (place, index) => `
            <button type="button" class="rubicon-geocode-result" data-result-index="${index}">
              ${escapeHtml(place.display_name || "")}
            </button>
          `
        )
        .join("");
      results.hidden = false;
    };

    const geocode = (preferFirstResult = false) => {
      const query = searchInput.value.trim();
      if (!query) {
        clearResults();
        return;
      }

      setStatus(window.rubiconMapsAdmin.strings.searching);

      const body = new URLSearchParams({
        action: "rubicon_maps_geocode",
        nonce: window.rubiconMapsAdmin.geocodeNonce,
        query,
      });

      fetch(window.rubiconMapsAdmin.ajaxUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
        },
        body: body.toString(),
      })
        .then((res) => res.json())
        .then((response) => {
          if (!response.success) {
            throw new Error(response.data && response.data.message ? response.data.message : window.rubiconMapsAdmin.strings.searchError);
          }

          const places = Array.isArray(response.data?.results) ? response.data.results : [];

          if (!places.length) {
            clearResults();
            setStatus(window.rubiconMapsAdmin.strings.searchEmpty, true);
            return;
          }

          if (preferFirstResult) {
            applyResult(places[0]);
            return;
          }

          renderResults(places);
          setStatus(window.rubiconMapsAdmin.strings.searchPrompt);
        })
        .catch((error) => {
          console.error("Rubicon Maps geocode error:", error);
          clearResults();
          setStatus(error.message || window.rubiconMapsAdmin.strings.searchError, true);
        });
    };

    searchButton.addEventListener("click", () => geocode(true));
    searchInput.addEventListener("keydown", (event) => {
      if (event.key === "Enter") {
        event.preventDefault();
        geocode(true);
      }
    });
    searchInput.addEventListener("input", () => {
      window.clearTimeout(searchTimer);

      if (searchInput.value.trim().length < 3) {
        clearResults();
        setStatus(window.rubiconMapsAdmin.strings.searchPrompt);
        return;
      }

      searchTimer = window.setTimeout(() => {
        geocode(false);
      }, 250);
    });

    if (results) {
      results.addEventListener("click", (event) => {
        const trigger = event.target.closest("[data-result-index]");

        if (!trigger) {
          return;
        }

        const index = Number.parseInt(trigger.getAttribute("data-result-index") || "-1", 10);
        const place = latestResults[index];

        if (place) {
          applyResult(place);
        }
      });
    }
  }

  document.querySelectorAll(".rubicon-upload").forEach((button) => {
    button.addEventListener("click", (event) => {
      event.preventDefault();
      const target = button.dataset.target;

      if (!target || typeof wp === "undefined" || !wp.media) {
        return;
      }

      const uploader = wp.media({
        title: window.rubiconMapsAdmin?.strings?.selectImage || "Select Icon",
        button: { text: window.rubiconMapsAdmin?.strings?.useImage || "Use this image" },
        multiple: false,
      });

      uploader.on("select", function () {
        const attachment = uploader.state().get("selection").first().toJSON();
        const input = document.getElementById(target);
        const preview = document.getElementById(`${target}-preview`);

        if (input) {
          input.value = attachment.id;
        }

        if (preview) {
          preview.setAttribute("src", attachment.url);
          preview.classList.remove("is-hidden");
        }
      });

      uploader.open();
    });
  });

  function escapeHtml(value) {
    return String(value)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }
});
