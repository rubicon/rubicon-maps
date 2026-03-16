import React from 'react';

const {
  AnimationGroup,
  BackgroundGroup,
  BorderGroup,
  BoxShadowGroup,
  FiltersGroup,
  SizingGroup,
  SpacingGroup,
  TransformGroup,
} = window?.divi?.module ?? {};

export const SettingsDesign = () => (
  <React.Fragment>
    <BackgroundGroup />
    <SizingGroup />
    <SpacingGroup />
    <BorderGroup />
    <BoxShadowGroup />
    <FiltersGroup />
    <TransformGroup />
    <AnimationGroup />
  </React.Fragment>
);
