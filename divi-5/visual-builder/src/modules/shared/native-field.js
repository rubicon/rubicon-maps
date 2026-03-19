const getValueSetter = (element) => {
  if (!element) {
    return null;
  }

  if (element instanceof HTMLTextAreaElement) {
    return Object.getOwnPropertyDescriptor(window.HTMLTextAreaElement.prototype, 'value')?.set ?? null;
  }

  return Object.getOwnPropertyDescriptor(window.HTMLInputElement.prototype, 'value')?.set ?? null;
};

export const setNativeFieldValue = (element, value) => {
  if (!element) {
    return false;
  }

  const setter = getValueSetter(element);

  if (!setter) {
    return false;
  }

  setter.call(element, value);
  element.dispatchEvent(new Event('input', { bubbles: true }));
  element.dispatchEvent(new Event('change', { bubbles: true }));

  return true;
};
