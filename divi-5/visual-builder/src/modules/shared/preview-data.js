import React from 'react';

import { __ } from '@wordpress/i18n';

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

const parseList = (value) =>
  getTextValue(value)
    .split(',')
    .map((item) => item.trim())
    .filter(Boolean);

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

export const usePreviewLabels = ({ categoryAttr, regionAttr, locationIdsAttr }) => {
  const categories = React.useMemo(() => parseList(categoryAttr), [categoryAttr]);
  const regions = React.useMemo(() => parseList(regionAttr), [regionAttr]);
  const locationIds = React.useMemo(() => parseList(locationIdsAttr), [locationIdsAttr]);

  const [labels, setLabels] = React.useState({
    categories,
    regions,
    locations: locationIds,
  });

  React.useEffect(() => {
    let cancelled = false;

    setLabels({
      categories,
      regions,
      locations: locationIds,
    });

    const resolveLabels = async () => {
      try {
        const [categoryTerms, regionTerms, locations] = await Promise.all([
          categories.length ? fetchJson(buildPath(`/wp/v2/${CATEGORY_REST_BASE}`, 'slug', categories)) : [],
          regions.length ? fetchJson(buildPath(`/wp/v2/${REGION_REST_BASE}`, 'slug', regions)) : [],
          locationIds.length
            ? fetchJson(`/rubicon-maps/v1/locations?location_ids=${encodeURIComponent(locationIds.join(','))}`)
            : [],
        ]);

        if (cancelled) {
          return;
        }

        const categoryMap = new Map(categoryTerms.map((term) => [term.slug, term.name]));
        const regionMap = new Map(regionTerms.map((term) => [term.slug, term.name]));
        const locationMap = new Map(locations.map((location) => [String(location.id), location.title]));

        setLabels({
          categories: categories.map((slug) => categoryMap.get(slug) || slug),
          regions: regions.map((slug) => regionMap.get(slug) || slug),
          locations: locationIds.map((id) => locationMap.get(id) || `#${id}`),
        });
      } catch (error) {
        if (!cancelled) {
          setLabels({
            categories,
            regions,
            locations: locationIds,
          });
        }
      }
    };

    resolveLabels();

    return () => {
      cancelled = true;
    };
  }, [categories.join(','), regions.join(','), locationIds.join(',')]);

  return labels;
};

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
