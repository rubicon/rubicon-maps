import { getTextValue } from './value';

const uniqueStrings = (items) => {
  const values = [];

  items.forEach((item) => {
    const value = String(item || '').trim();

    if ('' === value || values.includes(value)) {
      return;
    }

    values.push(value);
  });

  return values;
};

const uniqueIntegers = (items) => {
  const values = [];

  items.forEach((item) => {
    const value = Number.parseInt(String(item || '').trim(), 10);

    if (!Number.isInteger(value) || value <= 0 || values.includes(value)) {
      return;
    }

    values.push(value);
  });

  return values;
};

const parseJsonArray = (value) => {
  if ('string' !== typeof value) {
    return null;
  }

  const normalized = value.trim();

  if (!normalized.startsWith('[')) {
    return null;
  }

  try {
    const parsed = JSON.parse(normalized);

    return Array.isArray(parsed) ? parsed : null;
  } catch (error) {
    return null;
  }
};

export const parseStoredStringList = (attr, fallbackAttr = null) => {
  const primaryValue = getTextValue(attr).trim();
  const fallbackValue = fallbackAttr ? getTextValue(fallbackAttr).trim() : '';
  const parsed = parseJsonArray(primaryValue);

  if (Array.isArray(parsed)) {
    return uniqueStrings(parsed);
  }

  if (primaryValue) {
    return uniqueStrings(primaryValue.split(','));
  }

  if (fallbackValue) {
    return uniqueStrings(fallbackValue.split(','));
  }

  return [];
};

export const parseStoredIntegerList = (attr, fallbackAttr = null) => {
  const primaryValue = getTextValue(attr).trim();
  const fallbackValue = fallbackAttr ? getTextValue(fallbackAttr).trim() : '';
  const parsed = parseJsonArray(primaryValue);

  if (Array.isArray(parsed)) {
    return uniqueIntegers(parsed);
  }

  if (primaryValue) {
    return uniqueIntegers(primaryValue.split(','));
  }

  if (fallbackValue) {
    return uniqueIntegers(fallbackValue.split(','));
  }

  return [];
};

export const serializeStoredStringList = (items) => JSON.stringify(uniqueStrings(items));

export const serializeStoredIntegerList = (items) => JSON.stringify(uniqueIntegers(items));
