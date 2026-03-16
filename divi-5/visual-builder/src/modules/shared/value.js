export const getTextValue = (attr, fallback = '') => {
  if (typeof attr === 'string') {
    return attr;
  }

  return attr?.innerContent?.desktop?.value ?? attr?.innerContent ?? fallback;
};
