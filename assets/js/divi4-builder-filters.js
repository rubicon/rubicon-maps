(function () {
  const FIELD_CONFIGS = [
    {
      id: "rubicon-map-category-filter",
      type: "term",
      restBase: "rubicon_maps_category",
      placeholder: "Search categories…",
    },
    {
      id: "rubicon-map-region-filter",
      type: "term",
      restBase: "rubicon_maps_region",
      placeholder: "Search regions…",
    },
    {
      id: "rubicon-map-location-filter",
      type: "location",
      placeholder: "Search locations…",
    },
    {
      id: "rubicon-list-category-filter",
      type: "term",
      restBase: "rubicon_maps_category",
      placeholder: "Search categories…",
    },
    {
      id: "rubicon-list-region-filter",
      type: "term",
      restBase: "rubicon_maps_region",
      placeholder: "Search regions…",
    },
    {
      id: "rubicon-list-location-filter",
      type: "location",
      placeholder: "Search locations…",
    },
  ];

  const STYLE_ID = "rubicon-maps-divi4-filter-fields-style";

  const ensureStyles = () => {
    if (document.getElementById(STYLE_ID)) {
      return;
    }

    const style = document.createElement("style");
    style.id = STYLE_ID;
    style.textContent = `
      .rubicon-filter-field{display:grid;gap:10px;margin-top:8px}
      .rubicon-filter-field__control{border:1px solid rgba(15,23,42,.12);border-radius:12px;background:#fff;padding:10px;display:grid;gap:10px}
      .rubicon-filter-field__pills{display:flex;flex-wrap:wrap;gap:6px}
      .rubicon-filter-field__pill{display:inline-flex;align-items:center;gap:6px;min-height:28px;padding:0 8px 0 10px;border-radius:999px;background:#eff6ff;border:1px solid rgba(29,78,216,.16);color:#1e3a8a;font-size:12px;font-weight:600;line-height:1.2}
      .rubicon-filter-field__pill button{appearance:none;border:none;background:transparent;color:inherit;cursor:pointer;padding:0;font-size:14px;line-height:1}
      .rubicon-filter-field__searchwrap{position:relative}
      .rubicon-filter-field__search{width:100%;min-height:38px;border-radius:10px;border:1px solid rgba(15,23,42,.12);background:#f8fafc;color:#0f172a;font-size:13px;line-height:1.5;padding:8px 12px;outline:none}
      .rubicon-filter-field__results{position:absolute;top:calc(100% + 6px);left:0;right:0;z-index:1000;border-radius:12px;border:1px solid rgba(15,23,42,.12);background:#fff;box-shadow:0 18px 40px rgba(15,23,42,.14);max-height:220px;overflow-y:auto}
      .rubicon-filter-field__results button{appearance:none;width:100%;text-align:left;border:none;border-bottom:1px solid rgba(15,23,42,.06);background:#fff;padding:10px 12px;cursor:pointer;color:#0f172a;font-size:13px;line-height:1.5}
      .rubicon-filter-field__empty{padding:10px 12px;color:#64748b;font-size:13px}
    `;
    document.head.appendChild(style);
  };

  const apiFetch = async (path) => {
    const base = (window.rubiconMapsDivi4Filters && window.rubiconMapsDivi4Filters.restBase) || "/wp-json/";
    const url = path.startsWith("http") ? path : `${String(base).replace(/\/?$/, "/")}${path.replace(/^\//, "")}`;
    const response = await fetch(url, { credentials: "same-origin" });

    if (!response.ok) {
      throw new Error(`Request failed for ${url}`);
    }

    return response.json();
  };

  const parseStoredValues = (value, type) => {
    const normalized = String(value || "").trim();

    if (!normalized) {
      return [];
    }

    if (normalized.startsWith("[")) {
      try {
        const parsed = JSON.parse(normalized);

        if (Array.isArray(parsed)) {
          return type === "location"
            ? parsed.map((item) => parseInt(item, 10)).filter((item, index, items) => Number.isInteger(item) && item > 0 && items.indexOf(item) === index)
            : parsed.map((item) => String(item || "").trim()).filter((item, index, items) => item && items.indexOf(item) === index);
        }
      } catch (error) {}
    }

    return type === "location"
      ? normalized.split(",").map((item) => parseInt(item.trim(), 10)).filter((item, index, items) => Number.isInteger(item) && item > 0 && items.indexOf(item) === index)
      : normalized.split(",").map((item) => item.trim()).filter((item, index, items) => item && items.indexOf(item) === index);
  };

  const serializeStoredValues = (values) => JSON.stringify(values);

  const resolveLocationLabel = (location) => {
    if (typeof location.title === "string") {
      return location.title;
    }

    return (location.title && location.title.rendered) || `#${location.id}`;
  };

  const resolveSelected = async (config, values) => {
    if (!values.length) {
      return [];
    }

    if (config.type === "location") {
      const locations = await apiFetch(`/rubicon-maps/v1/locations?location_ids=${encodeURIComponent(values.join(","))}`);
      const locationMap = new Map(locations.map((location) => [Number(location.id), resolveLocationLabel(location)]));

      return values.map((value) => ({ value, label: locationMap.get(value) || `#${value}` }));
    }

    const params = new URLSearchParams();
    values.forEach((value) => params.append("slug", value));
    const terms = await apiFetch(`/wp/v2/${config.restBase}?${params.toString()}&_fields=id,name,slug`);
    const termMap = new Map(terms.map((term) => [term.slug, term.name]));

    return values.map((value) => ({ value, label: termMap.get(value) || value }));
  };

  const searchOptions = async (config, query) => {
    if (config.type === "location") {
      const locations = await apiFetch(`/wp/v2/rubicon_location?search=${encodeURIComponent(query)}&per_page=20&_fields=id,title`);

      return locations.map((location) => ({
        value: Number(location.id),
        label: resolveLocationLabel(location),
      }));
    }

    const terms = await apiFetch(`/wp/v2/${config.restBase}?search=${encodeURIComponent(query)}&per_page=20&_fields=id,name,slug`);

    return terms.map((term) => ({
      value: term.slug,
      label: term.name,
    }));
  };

  const enhanceField = (input, config) => {
    if (!input || input.dataset.rubiconFilterEnhanced === "1") {
      return;
    }

    input.dataset.rubiconFilterEnhanced = "1";
    input.style.display = "none";

    const wrapper = document.createElement("div");
    wrapper.className = "rubicon-filter-field";

    const control = document.createElement("div");
    control.className = "rubicon-filter-field__control";

    const pills = document.createElement("div");
    pills.className = "rubicon-filter-field__pills";

    const searchWrap = document.createElement("div");
    searchWrap.className = "rubicon-filter-field__searchwrap";

    const searchInput = document.createElement("input");
    searchInput.type = "text";
    searchInput.className = "rubicon-filter-field__search";
    searchInput.placeholder = config.placeholder;

    const results = document.createElement("div");
    results.className = "rubicon-filter-field__results";
    results.hidden = true;

    searchWrap.appendChild(searchInput);
    searchWrap.appendChild(results);
    control.appendChild(pills);
    control.appendChild(searchWrap);
    wrapper.appendChild(control);
    input.insertAdjacentElement("afterend", wrapper);

    let selected = [];
    let searchTimer = null;

    const syncInput = () => {
      input.value = serializeStoredValues(selected.map((item) => item.value));
      input.dispatchEvent(new Event("input", { bubbles: true }));
      input.dispatchEvent(new Event("change", { bubbles: true }));
    };

    const renderPills = () => {
      pills.innerHTML = "";

      selected.forEach((item) => {
        const pill = document.createElement("span");
        pill.className = "rubicon-filter-field__pill";
        pill.append(document.createTextNode(item.label));

        const removeButton = document.createElement("button");
        removeButton.type = "button";
        removeButton.setAttribute("aria-label", "Remove selection");
        removeButton.textContent = "×";
        removeButton.addEventListener("click", () => {
          selected = selected.filter((current) => String(current.value) !== String(item.value));
          renderPills();
          syncInput();
        });

        pill.appendChild(removeButton);
        pills.appendChild(pill);
      });
    };

    const renderResults = (items, emptyText) => {
      results.innerHTML = "";
      results.hidden = false;

      if (!items.length) {
        const empty = document.createElement("div");
        empty.className = "rubicon-filter-field__empty";
        empty.textContent = emptyText;
        results.appendChild(empty);
        return;
      }

      items.forEach((item) => {
        const button = document.createElement("button");
        button.type = "button";
        button.textContent = item.label;
        button.addEventListener("click", () => {
          if (!selected.some((current) => String(current.value) === String(item.value))) {
            selected = [...selected, item];
            renderPills();
            syncInput();
          }

          searchInput.value = "";
          results.hidden = true;
        });
        results.appendChild(button);
      });
    };

    searchInput.addEventListener("input", () => {
      const query = searchInput.value.trim();

      window.clearTimeout(searchTimer);

      if (!query) {
        results.hidden = true;
        results.innerHTML = "";
        return;
      }

      searchTimer = window.setTimeout(async () => {
        renderResults([], "Searching…");

        try {
          const options = await searchOptions(config, query);
          const selectedKeys = new Set(selected.map((item) => String(item.value)));
          renderResults(
            options.filter((item) => !selectedKeys.has(String(item.value))),
            "No matches found."
          );
        } catch (error) {
          renderResults([], "No matches found.");
        }
      }, 180);
    });

    searchInput.addEventListener("keydown", (event) => {
      if (event.key === "Backspace" && !searchInput.value && selected.length) {
        selected = selected.slice(0, -1);
        renderPills();
        syncInput();
      }

      if (event.key === "Escape") {
        results.hidden = true;
      }
    });

    document.addEventListener("click", (event) => {
      if (!wrapper.contains(event.target)) {
        results.hidden = true;
      }
    });

    const initialValues = parseStoredValues(input.value, config.type);

    resolveSelected(config, initialValues)
      .then((resolved) => {
        selected = resolved;
        renderPills();
        syncInput();
      })
      .catch(() => {
        selected = initialValues.map((value) => ({ value, label: String(value) }));
        renderPills();
        syncInput();
      });
  };

  const enhanceKnownFields = () => {
    ensureStyles();

    FIELD_CONFIGS.forEach((config) => {
      enhanceField(document.getElementById(config.id), config);
    });
  };

  document.addEventListener("DOMContentLoaded", enhanceKnownFields);
  window.setTimeout(enhanceKnownFields, 300);

  const observer = new MutationObserver(() => {
    enhanceKnownFields();
  });

  observer.observe(document.documentElement, {
    childList: true,
    subtree: true,
  });
})();
