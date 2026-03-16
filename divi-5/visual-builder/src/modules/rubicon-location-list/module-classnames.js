const { elementClassnames } = window?.divi?.module ?? {};

export const ModuleClassnames = ({ classnamesInstance, attrs }) => {
  if (typeof elementClassnames !== 'function') {
    return;
  }

  classnamesInstance.add(
    elementClassnames({
      attrs: attrs?.module?.decoration ?? {},
    }),
  );
};
