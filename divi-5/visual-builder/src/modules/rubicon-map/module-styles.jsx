import React from 'react';

const { StyleContainer } = window?.divi?.module ?? {};

export const ModuleStyles = ({
  elements,
  settings,
  mode,
  state,
  noStyleTag,
}) => (
  <StyleContainer mode={mode} state={state} noStyleTag={noStyleTag}>
    {elements.style({
      attrName: 'module',
      styleProps: {
        disabledOn: {
          disabledModuleVisibility: settings?.disabledModuleVisibility,
        },
      },
    })}
  </StyleContainer>
);
