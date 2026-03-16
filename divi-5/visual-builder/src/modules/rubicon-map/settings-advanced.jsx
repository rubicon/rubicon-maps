import React from 'react';

const {
  IdClassesGroup,
  PositionSettingsGroup,
  ScrollSettingsGroup,
  TransitionGroup,
  VisibilitySettingsGroup,
} = window?.divi?.module ?? {};

export const SettingsAdvanced = () => (
  <React.Fragment>
    <IdClassesGroup />
    <VisibilitySettingsGroup />
    <TransitionGroup />
    <PositionSettingsGroup />
    <ScrollSettingsGroup />
  </React.Fragment>
);
