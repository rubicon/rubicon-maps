import React from 'react';

import { __ } from '@wordpress/i18n';

import { parseStoredIntegerList, parseStoredStringList } from './filter-values';
import { getTextValue } from './value';

const CATEGORY_REST_BASE = 'rubicon_maps_category';
const REGION_REST_BASE = 'rubicon_maps_region';

const PillRowStyle = {
  display: 'flex',
  flexWrap: 'wrap',
  gap: '6px',
};

const PillStyle = {
  display: 'inline-flex',
  alignItems: 'center',
  minHeight: '28px',
  padding: '0 10px',
  borderRadius: '999px',
  background: '#ffffff',
  border: '1px solid rgba(15, 23, 42, 0.08)',
  color: '#0f172a',
  fontSize: '12px',
  fontWeight: 600,
  lineHeight: 1.2,
};

const buildLocationPath = ({ categories, regions, locationIds }) => {
  const params = new URLSearchParams();

  if (categories.length) {
    params.set('category', categories.join(','));
  }

  if (regions.length) {
    params.set('region', regions.join(','));
  }

  if (locationIds.length) {
    params.set('location_ids', locationIds.join(','));
  }

  const query = params.toString();

  return query ? `/rubicon-maps/v1/locations?${query}` : '/rubicon-maps/v1/locations';
};

const buildPath = (base, key, values) => {
  const params = new URLSearchParams();

  values.forEach((value) => {
    params.append(key, value);
  });

  return `${base}?${params.toString()}`;
};

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

export const getProviderKey = (attr) => {
  const value = String(getTextValue(attr, '') || '').trim().toLowerCase();

  if ('' === value || 'default' === value || 'leaflet' === value || '1' === value) {
    return 'leaflet';
  }

  if ('google' === value || 'google maps' === value || '2' === value) {
    return 'google';
  }

  return value;
};

export const getProviderLabel = (attr) => {
  const provider = getProviderKey(attr);

  if ('google' === provider) {
    return __('Google Maps', 'rubicon-maps');
  }

  return __('Leaflet', 'rubicon-maps');
};

export const usePreviewLocationData = ({ categoryAttr, regionAttr, locationIdsAttr }) => {
  const categories = React.useMemo(() => parseStoredStringList(categoryAttr), [categoryAttr]);
  const regions = React.useMemo(() => parseStoredStringList(regionAttr), [regionAttr]);
  const locationIds = React.useMemo(() => parseStoredIntegerList(locationIdsAttr).map(String), [locationIdsAttr]);

  const [state, setState] = React.useState({
    categories,
    regions,
    locations: locationIds,
    matchedLocationCount: locationIds.length,
  });

  React.useEffect(() => {
    let cancelled = false;

    setState({
      categories,
      regions,
      locations: locationIds,
      matchedLocationCount: locationIds.length,
    });

    const resolveLabels = async () => {
      try {
        const [categoryTerms, regionTerms, explicitLocations, matchedLocations] = await Promise.all([
          categories.length ? fetchJson(buildPath(`/wp/v2/${CATEGORY_REST_BASE}`, 'slug', categories)) : [],
          regions.length ? fetchJson(buildPath(`/wp/v2/${REGION_REST_BASE}`, 'slug', regions)) : [],
          locationIds.length
            ? fetchJson(`/rubicon-maps/v1/locations?location_ids=${encodeURIComponent(locationIds.join(','))}`)
            : [],
          fetchJson(buildLocationPath({ categories, regions, locationIds })),
        ]);

        if (cancelled) {
          return;
        }

        const categoryMap = new Map(categoryTerms.map((term) => [term.slug, term.name]));
        const regionMap = new Map(regionTerms.map((term) => [term.slug, term.name]));
        const locationMap = new Map(explicitLocations.map((location) => [String(location.id), location.title]));

        setState({
          categories: categories.map((slug) => categoryMap.get(slug) || slug),
          regions: regions.map((slug) => regionMap.get(slug) || slug),
          locations: locationIds.map((id) => locationMap.get(id) || `#${id}`),
          matchedLocationCount: Array.isArray(matchedLocations) ? matchedLocations.length : 0,
        });
      } catch (error) {
        if (!cancelled) {
          setState({
            categories,
            regions,
            locations: locationIds,
            matchedLocationCount: locationIds.length,
          });
        }
      }
    };

    resolveLabels();

    return () => {
      cancelled = true;
    };
  }, [categories.join(','), regions.join(','), locationIds.join(',')]);

  return state;
};

const previewRegistry = () => {
  if (!window.__rubiconMapsPreviewRegistry) {
    window.__rubiconMapsPreviewRegistry = {
      counts: new Map(),
      listeners: new Map(),
    };
  }

  return window.__rubiconMapsPreviewRegistry;
};

export const publishPreviewCount = (syncId, count) => {
  if (!syncId) {
    return;
  }

  const registry = previewRegistry();

  registry.counts.set(syncId, count);

  const listeners = registry.listeners.get(syncId) || new Set();
  listeners.forEach((listener) => listener(count));
};

export const usePublishedPreviewCount = (syncId) => {
  const [count, setCount] = React.useState(() => {
    if (!syncId) {
      return null;
    }

    return previewRegistry().counts.get(syncId) ?? null;
  });

  React.useEffect(() => {
    if (!syncId) {
      setCount(null);
      return undefined;
    }

    const registry = previewRegistry();
    const listeners = registry.listeners.get(syncId) || new Set();
    const update = (nextCount) => setCount(nextCount);

    listeners.add(update);
    registry.listeners.set(syncId, listeners);

    if (registry.counts.has(syncId)) {
      setCount(registry.counts.get(syncId));
    }

    return () => {
      listeners.delete(update);

      if (0 === listeners.size) {
        registry.listeners.delete(syncId);
      }
    };
  }, [syncId]);

  return count;
};

export const usePreviewLabels = usePreviewLocationData;

export const renderPills = (items) => {
  if (!items.length) {
    return __('All', 'rubicon-maps');
  }

  return (
    <span style={PillRowStyle}>
      {items.map((item) => (
        <span
          key={item}
          style={PillStyle}
        >
          {item}
        </span>
      ))}
    </span>
  );
};
