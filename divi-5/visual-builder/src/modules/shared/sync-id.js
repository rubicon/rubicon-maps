import { getTextValue } from './value';

export const shortSeed = (value, fallback = 'new') => {
  const normalized = String(value || '')
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '')
    .slice(-6);

  return normalized || fallback;
};

const randomSeed = () => Math.random().toString(36).slice(2, 8);

export const getSavedSyncId = (attr) => getTextValue(attr).trim();

export const buildGeneratedSyncId = (seed = '') => `rtv_map_${shortSeed(seed, randomSeed())}`;

export const getEffectiveSyncId = (attr) => getSavedSyncId(attr);
