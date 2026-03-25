import React from 'react';

import { __ } from '@wordpress/i18n';

import { setNativeFieldValue } from './native-field';
import {
  parseStoredIntegerList,
  parseStoredStringList,
  serializeStoredIntegerList,
  serializeStoredStringList,
} from './filter-values';

const { TextContainer } = window?.divi?.fieldLibrary ?? {};
const { FieldContainer } = window?.divi?.module ?? {};

const WrapperStyle = {
  display: 'grid',
  gap: '10px',
};

const LabelStyle = {
  color: '#0f172a',
  fontSize: '14px',
  fontWeight: 600,
  lineHeight: 1.4,
};

const HiddenFieldStyle = {
  display: 'none',
};

const ControlStyle = {
  border: '1px solid rgba(15, 23, 42, 0.12)',
  borderRadius: '12px',
  background: '#ffffff',
  padding: '10px',
  display: 'grid',
  gap: '10px',
};

const PillRowStyle = {
  display: 'flex',
  flexWrap: 'wrap',
  gap: '6px',
};

const PillStyle = {
  display: 'inline-flex',
  alignItems: 'center',
  gap: '6px',
  minHeight: '28px',
  padding: '0 8px 0 10px',
  borderRadius: '999px',
  background: '#eff6ff',
  border: '1px solid rgba(29, 78, 216, 0.16)',
  color: '#1e3a8a',
  fontSize: '12px',
  fontWeight: 600,
  lineHeight: 1.2,
};

const PillButtonStyle = {
  appearance: 'none',
  border: 'none',
  background: 'transparent',
  color: 'inherit',
  cursor: 'pointer',
  padding: 0,
  fontSize: '14px',
  lineHeight: 1,
};

const SearchWrapStyle = {
  position: 'relative',
};

const SearchInputStyle = {
  width: '100%',
  minHeight: '38px',
  borderRadius: '10px',
  border: '1px solid rgba(15, 23, 42, 0.12)',
  background: '#f8fafc',
  color: '#0f172a',
  fontSize: '13px',
  lineHeight: 1.5,
  padding: '8px 12px',
  outline: 'none',
};

const ResultsStyle = {
  position: 'absolute',
  top: 'calc(100% + 6px)',
  left: 0,
  right: 0,
  zIndex: 20,
  borderRadius: '12px',
  border: '1px solid rgba(15, 23, 42, 0.12)',
  background: '#ffffff',
  boxShadow: '0 18px 40px rgba(15, 23, 42, 0.14)',
  maxHeight: '220px',
  overflowY: 'auto',
};

const ResultButtonStyle = {
  appearance: 'none',
  width: '100%',
  textAlign: 'left',
  border: 'none',
  borderBottom: '1px solid rgba(15, 23, 42, 0.06)',
  background: '#ffffff',
  padding: '10px 12px',
  cursor: 'pointer',
  color: '#0f172a',
  fontSize: '13px',
  lineHeight: 1.5,
};

const EmptyStateStyle = {
  padding: '10px 12px',
  color: '#64748b',
  fontSize: '13px',
};

const HelpStyle = {
  color: '#64748b',
  fontSize: '12px',
  lineHeight: 1.5,
};

const LOCATION_POST_TYPE = 'rubicon_location';
const CATEGORY_REST_BASE = 'rubicon_maps_category';
const REGION_REST_BASE = 'rubicon_maps_region';

const fetchJson = async (path) => {
  if (window?.wp?.apiFetch) {
    return window.wp.apiFetch({ path });
  }

  const response = await fetch(`/wp-json${path}`);

  if (!response.ok) {
    throw new Error(`Failed request for ${path}`);
  }

  return response.json();
};

const buildPath = (base, key, values) => {
  const params = new URLSearchParams();

  values.forEach((value) => {
    params.append(key, value);
  });

  return `${base}?${params.toString()}`;
};

const resolveLocationLabel = (location) => {
  if ('string' === typeof location?.title) {
    return location.title;
  }

  return location?.title?.rendered || `#${location?.id || ''}`;
};

const buildSearchConfig = (kind) => {
  if ('categories' === kind) {
    return {
      parseValue: parseStoredStringList,
      serialize: serializeStoredStringList,
      resolveSelected: async (values) => {
        if (!values.length) {
          return [];
        }

        const terms = await fetchJson(buildPath(`/wp/v2/${CATEGORY_REST_BASE}`, 'slug', values));
        const termMap = new Map(terms.map((term) => [term.slug, term.name]));

        return values.map((value) => ({ value, label: termMap.get(value) || value }));
      },
      search: async (query) => {
        const terms = await fetchJson(`/wp/v2/${CATEGORY_REST_BASE}?search=${encodeURIComponent(query)}&per_page=20&_fields=id,name,slug`);

        return terms.map((term) => ({
          value: term.slug,
          label: term.name,
        }));
      },
    };
  }

  if ('regions' === kind) {
    return {
      parseValue: parseStoredStringList,
      serialize: serializeStoredStringList,
      resolveSelected: async (values) => {
        if (!values.length) {
          return [];
        }

        const terms = await fetchJson(buildPath(`/wp/v2/${REGION_REST_BASE}`, 'slug', values));
        const termMap = new Map(terms.map((term) => [term.slug, term.name]));

        return values.map((value) => ({ value, label: termMap.get(value) || value }));
      },
      search: async (query) => {
        const terms = await fetchJson(`/wp/v2/${REGION_REST_BASE}?search=${encodeURIComponent(query)}&per_page=20&_fields=id,name,slug`);

        return terms.map((term) => ({
          value: term.slug,
          label: term.name,
        }));
      },
    };
  }

  return {
    parseValue: parseStoredIntegerList,
    serialize: serializeStoredIntegerList,
    resolveSelected: async (values) => {
      if (!values.length) {
        return [];
      }

      const locations = await fetchJson(`/rubicon-maps/v1/locations?location_ids=${encodeURIComponent(values.join(','))}`);
      const locationMap = new Map(locations.map((location) => [Number(location.id), resolveLocationLabel(location)]));

      return values.map((value) => ({ value, label: locationMap.get(value) || `#${value}` }));
    },
    search: async (query) => {
      const locations = await fetchJson(`/wp/v2/${LOCATION_POST_TYPE}?search=${encodeURIComponent(query)}&per_page=20&_fields=id,title`);

      return locations.map((location) => ({
        value: Number(location.id),
        label: resolveLocationLabel(location),
      }));
    },
  };
};

export const SearchableFilterField = ({
  attrName,
  label,
  description,
  valueAttr,
  kind,
  placeholder,
}) => {
  const fieldRef = React.useRef(null);
  const [selectedOptions, setSelectedOptions] = React.useState([]);
  const [query, setQuery] = React.useState('');
  const [searchResults, setSearchResults] = React.useState([]);
  const [isSearching, setIsSearching] = React.useState(false);
  const [isOpen, setIsOpen] = React.useState(false);
  const [isReady, setIsReady] = React.useState(false);

  const config = React.useMemo(() => buildSearchConfig(kind), [kind]);
  const selectedValues = React.useMemo(() => config.parseValue(valueAttr), [config, valueAttr]);

  React.useEffect(() => {
    let cancelled = false;

    const resolveSelected = async () => {
      const resolved = await config.resolveSelected(selectedValues);

      if (!cancelled) {
        setSelectedOptions(resolved);
        setIsReady(true);
      }
    };

    resolveSelected().catch(() => {
      if (!cancelled) {
        setSelectedOptions(selectedValues.map((value) => ({
          value,
          label: String(value),
        })));
        setIsReady(true);
      }
    });

    return () => {
      cancelled = true;
    };
  }, [config, selectedValues.join('|')]);

  React.useEffect(() => {
    if (!isReady) {
      return;
    }

    const input = fieldRef.current?.querySelector('input, textarea');

    if (!input) {
      return;
    }

    const nextValue = config.serialize(selectedOptions.map((option) => option.value));

    setNativeFieldValue(input, nextValue);
  }, [config, isReady, selectedOptions]);

  React.useEffect(() => {
    if (!isOpen) {
      return undefined;
    }

    const handleDocumentClick = (event) => {
      if (!event.target.closest?.(`[data-rubicon-filter-field="${attrName}"]`)) {
        setIsOpen(false);
      }
    };

    document.addEventListener('click', handleDocumentClick);

    return () => {
      document.removeEventListener('click', handleDocumentClick);
    };
  }, [attrName, isOpen]);

  React.useEffect(() => {
    let cancelled = false;

    if (!query.trim()) {
      setSearchResults([]);
      setIsSearching(false);
      return undefined;
    }

    const timer = window.setTimeout(async () => {
      setIsSearching(true);

      try {
        const results = await config.search(query.trim());

        if (cancelled) {
          return;
        }

        const selectedKeys = new Set(selectedOptions.map((option) => String(option.value)));

        setSearchResults(results.filter((result) => !selectedKeys.has(String(result.value))));
      } catch (error) {
        if (!cancelled) {
          setSearchResults([]);
        }
      } finally {
        if (!cancelled) {
          setIsSearching(false);
        }
      }
    }, 180);

    return () => {
      cancelled = true;
      window.clearTimeout(timer);
    };
  }, [config, query, selectedOptions]);

  const addOption = (option) => {
    setSelectedOptions((current) => {
      if (current.some((item) => String(item.value) === String(option.value))) {
        return current;
      }

      return [...current, option];
    });
    setQuery('');
    setSearchResults([]);
    setIsOpen(false);
  };

  const removeOption = (value) => {
    setSelectedOptions((current) => current.filter((option) => String(option.value) !== String(value)));
  };

  return (
    <div style={WrapperStyle}>
      <div
        ref={fieldRef}
        style={HiddenFieldStyle}
        aria-hidden="true"
      >
        <FieldContainer
          attrName={attrName}
          label={label}
          description={description}
          features={{ sticky: false }}
        >
          <TextContainer />
        </FieldContainer>
      </div>
      <div
        style={ControlStyle}
        data-rubicon-filter-field={attrName}
      >
        <div style={LabelStyle}>{label}</div>
        {selectedOptions.length ? (
          <div style={PillRowStyle}>
            {selectedOptions.map((option) => (
              <span
                key={`${attrName}-${option.value}`}
                style={PillStyle}
              >
                {option.label}
                <button
                  type="button"
                  style={PillButtonStyle}
                  onClick={() => removeOption(option.value)}
                  aria-label={__('Remove selection', 'rubicon-maps')}
                >
                  ×
                </button>
              </span>
            ))}
          </div>
        ) : null}
        <div style={SearchWrapStyle}>
          <input
            type="text"
            value={query}
            placeholder={placeholder}
            style={SearchInputStyle}
            onFocus={() => setIsOpen(true)}
            onChange={(event) => {
              setQuery(event.target.value);
              setIsOpen(true);
            }}
            onKeyDown={(event) => {
              if ('Backspace' === event.key && !query && selectedOptions.length) {
                removeOption(selectedOptions[selectedOptions.length - 1].value);
              }

              if ('Escape' === event.key) {
                setIsOpen(false);
              }
            }}
          />
          {isOpen && (query.trim() || isSearching || searchResults.length) ? (
            <div style={ResultsStyle}>
              {isSearching ? (
                <div style={EmptyStateStyle}>{__('Searching…', 'rubicon-maps')}</div>
              ) : searchResults.length ? (
                searchResults.map((option) => (
                  <button
                    key={`${attrName}-result-${option.value}`}
                    type="button"
                    style={ResultButtonStyle}
                    onClick={() => addOption(option)}
                  >
                    {option.label}
                  </button>
                ))
              ) : (
                <div style={EmptyStateStyle}>{__('No matches found.', 'rubicon-maps')}</div>
              )}
            </div>
          ) : null}
        </div>
        <div style={HelpStyle}>{description}</div>
      </div>
    </div>
  );
};
