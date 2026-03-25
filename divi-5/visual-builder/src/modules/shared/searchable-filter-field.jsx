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
  gap: '8px',
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
  overflow: 'hidden',
};

const ResultsHeaderStyle = {
  display: 'flex',
  alignItems: 'center',
  gap: '8px',
  padding: '10px 12px',
  borderBottom: '1px solid rgba(15, 23, 42, 0.08)',
  background: '#f8fafc',
};

const ResultsHeaderLabelStyle = {
  color: '#475569',
  fontSize: '12px',
  fontWeight: 600,
  lineHeight: 1.4,
};

const ResultsListStyle = {
  maxHeight: '180px',
  overflowY: 'auto',
};

const SelectAllRowStyle = {
  appearance: 'none',
  width: '100%',
  display: 'flex',
  alignItems: 'center',
  gap: '10px',
  textAlign: 'left',
  border: 'none',
  borderBottom: '1px solid rgba(15, 23, 42, 0.08)',
  background: '#ffffff',
  padding: '10px 12px',
  cursor: 'pointer',
  color: '#0f172a',
  fontSize: '13px',
  fontWeight: 600,
  lineHeight: 1.4,
};

const OptionRowStyle = {
  appearance: 'none',
  width: '100%',
  display: 'flex',
  alignItems: 'center',
  gap: '10px',
  textAlign: 'left',
  border: 'none',
  borderBottom: '1px solid rgba(15, 23, 42, 0.06)',
  background: '#ffffff',
  padding: '10px 12px',
  cursor: 'pointer',
  color: '#0f172a',
  fontSize: '13px',
  lineHeight: 1.4,
};

const CheckboxStyle = {
  width: '16px',
  minWidth: '16px',
  height: '16px',
  borderRadius: '4px',
  border: '1px solid rgba(15, 23, 42, 0.12)',
  background: '#ffffff',
  display: 'inline-flex',
  alignItems: 'center',
  justifyContent: 'center',
  fontSize: '11px',
  color: '#ffffff',
  lineHeight: 1,
};

const CheckboxCheckedStyle = {
  ...CheckboxStyle,
  background: '#2563eb',
  border: '1px solid #2563eb',
};

const FooterStyle = {
  display: 'flex',
  gap: '8px',
  flexWrap: 'wrap',
  padding: '10px 12px 12px',
  borderTop: '1px solid rgba(15, 23, 42, 0.08)',
  background: '#f8fafc',
};

const FooterButtonStyle = {
  appearance: 'none',
  border: '1px solid rgba(15, 23, 42, 0.12)',
  background: '#ffffff',
  color: '#0f172a',
  borderRadius: '999px',
  minHeight: '30px',
  padding: '0 12px',
  cursor: 'pointer',
  fontSize: '12px',
  fontWeight: 600,
  lineHeight: 1.2,
};

const FooterButtonDisabledStyle = {
  ...FooterButtonStyle,
  opacity: 0.45,
  cursor: 'default',
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

const OverflowPillStyle = {
  ...PillStyle,
  background: '#f8fafc',
  border: '1px solid rgba(15, 23, 42, 0.12)',
  color: '#475569',
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
        const searchParam = query ? `search=${encodeURIComponent(query)}&` : '';
        const terms = await fetchJson(`/wp/v2/${CATEGORY_REST_BASE}?${searchParam}per_page=20&_fields=id,name,slug`);

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
        const searchParam = query ? `search=${encodeURIComponent(query)}&` : '';
        const terms = await fetchJson(`/wp/v2/${REGION_REST_BASE}?${searchParam}per_page=20&_fields=id,name,slug`);

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
      const searchParam = query ? `search=${encodeURIComponent(query)}&` : '';
      const locations = await fetchJson(`/wp/v2/${LOCATION_POST_TYPE}?${searchParam}per_page=20&_fields=id,title`);

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
  const selectedValueSet = React.useMemo(
    () => new Set(selectedOptions.map((option) => String(option.value))),
    [selectedOptions],
  );

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

    if (!isOpen) {
      return undefined;
    }

    const timer = window.setTimeout(async () => {
      setIsSearching(true);

      try {
        const results = await config.search(query.trim());

        if (cancelled) {
          return;
        }

        setSearchResults(results);
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
  }, [config, isOpen, query, selectedOptions]);

  const toggleOption = (option) => {
    setSelectedOptions((current) => {
      if (current.some((item) => String(item.value) === String(option.value))) {
        return current.filter((item) => String(item.value) !== String(option.value));
      }

      return [...current, option];
    });
  };

  const removeOption = (value) => {
    setSelectedOptions((current) => current.filter((option) => String(option.value) !== String(value)));
  };

  const selectAllVisible = () => {
    if (!searchResults.length) {
      return;
    }

    setSelectedOptions((current) => {
      const seen = new Set(current.map((option) => String(option.value)));
      const visibleValues = searchResults.map((option) => String(option.value));
      const allVisibleSelected = visibleValues.every((value) => seen.has(value));

      if (allVisibleSelected) {
        return current.filter((option) => !visibleValues.includes(String(option.value)));
      }

      const additions = searchResults.filter((option) => !seen.has(String(option.value)));
      return additions.length ? [...current, ...additions] : current;
    });
  };

  const clearAll = () => {
    setSelectedOptions([]);
  };

  const visibleResultsLabel = query.trim()
    ? __('Filtered results', 'rubicon-maps')
    : __('Available options', 'rubicon-maps');
  const visibleSelectedOptions = selectedOptions.slice(0, 2);
  const hiddenSelectedCount = Math.max(0, selectedOptions.length - visibleSelectedOptions.length);
  const allVisibleSelected = searchResults.length
    ? searchResults.every((option) => selectedValueSet.has(String(option.value)))
    : false;

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
            {visibleSelectedOptions.map((option) => (
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
            {hiddenSelectedCount ? (
              <span style={OverflowPillStyle}>
                {`+${hiddenSelectedCount} ${__('more', 'rubicon-maps')}`}
              </span>
            ) : null}
          </div>
        ) : null}
        <div
          style={SearchWrapStyle}
          onMouseDownCapture={() => setIsOpen(true)}
          onFocusCapture={() => setIsOpen(true)}
        >
          <input
            type="text"
            value={query}
            placeholder={placeholder}
            style={SearchInputStyle}
            onPointerDown={() => setIsOpen(true)}
            onMouseDown={() => setIsOpen(true)}
            onFocus={() => setIsOpen(true)}
            onClick={() => setIsOpen(true)}
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
          {isOpen ? (
            <div style={ResultsStyle}>
              <div style={ResultsHeaderStyle}>
                <div style={ResultsHeaderLabelStyle}>{visibleResultsLabel}</div>
              </div>
              <div style={ResultsListStyle}>
                <button
                  type="button"
                  style={SelectAllRowStyle}
                  onClick={selectAllVisible}
                  disabled={!searchResults.length}
                >
                  <span style={allVisibleSelected ? CheckboxCheckedStyle : CheckboxStyle}>
                    {allVisibleSelected ? '✓' : ''}
                  </span>
                  {__('Select all', 'rubicon-maps')}
                </button>
                {isSearching ? (
                  <div style={EmptyStateStyle}>{__('Searching…', 'rubicon-maps')}</div>
                ) : searchResults.length ? (
                  searchResults.map((option) => {
                    const isSelected = selectedValueSet.has(String(option.value));

                    return (
                      <button
                        key={`${attrName}-result-${option.value}`}
                        type="button"
                        style={OptionRowStyle}
                        onClick={() => toggleOption(option)}
                      >
                        <span style={isSelected ? CheckboxCheckedStyle : CheckboxStyle}>
                          {isSelected ? '✓' : ''}
                        </span>
                        {option.label}
                      </button>
                    );
                  })
                ) : (
                  <div style={EmptyStateStyle}>{__('No matches found.', 'rubicon-maps')}</div>
                )}
              </div>
              <div style={FooterStyle}>
                <button
                  type="button"
                  style={selectedOptions.length ? FooterButtonStyle : FooterButtonDisabledStyle}
                  onClick={clearAll}
                  disabled={!selectedOptions.length}
                >
                  {__('Clear', 'rubicon-maps')}
                </button>
                <button
                  type="button"
                  style={FooterButtonStyle}
                  onClick={() => setIsOpen(false)}
                >
                  {__('Close', 'rubicon-maps')}
                </button>
              </div>
            </div>
          ) : null}
        </div>
        <div style={HelpStyle}>{description}</div>
      </div>
    </div>
  );
};
