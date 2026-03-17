import React from 'react';

import { __ } from '@wordpress/i18n';

const { TextContainer } = window?.divi?.fieldLibrary ?? {};
const { GroupContainer } = window?.divi?.modal ?? {};
const {
  AdminLabelGroup,
  FieldContainer,
} = window?.divi?.module ?? {};

export const SettingsContent = ({ defaultSettingsAttrs }) => (
  <React.Fragment>
    <GroupContainer
      id="mapFilters"
      title={__('Location Filters', 'rubicon-maps')}
    >
      <FieldContainer
        attrName="instanceId.innerContent"
        label={__('Map ID', 'rubicon-maps')}
        description={__('Optional shared ID used to sync this map with a Rubicon Location List module.', 'rubicon-maps')}
        features={{ sticky: false }}
      >
        <TextContainer />
      </FieldContainer>
      <FieldContainer
        attrName="category.innerContent"
        label={__('Categories', 'rubicon-maps')}
        description={__('Comma-separated category slugs to include.', 'rubicon-maps')}
        features={{ sticky: false }}
      >
        <TextContainer />
      </FieldContainer>
      <FieldContainer
        attrName="region.innerContent"
        label={__('Regions', 'rubicon-maps')}
        description={__('Comma-separated region slugs to include.', 'rubicon-maps')}
        features={{ sticky: false }}
      >
        <TextContainer />
      </FieldContainer>
      <FieldContainer
        attrName="locationIds.innerContent"
        label={__('Specific Location IDs', 'rubicon-maps')}
        description={__('Optional comma-separated post IDs to limit this map to explicit locations.', 'rubicon-maps')}
        features={{ sticky: false }}
      >
        <TextContainer />
      </FieldContainer>
    </GroupContainer>
    <GroupContainer
      id="mapDisplay"
      title={__('Map Display', 'rubicon-maps')}
    >
      <FieldContainer
        attrName="latitude.innerContent"
        label={__('Latitude', 'rubicon-maps')}
        description={__('Override the default map latitude for this instance.', 'rubicon-maps')}
        features={{ sticky: false }}
      >
        <TextContainer />
      </FieldContainer>
      <FieldContainer
        attrName="longitude.innerContent"
        label={__('Longitude', 'rubicon-maps')}
        description={__('Override the default map longitude for this instance.', 'rubicon-maps')}
        features={{ sticky: false }}
      >
        <TextContainer />
      </FieldContainer>
      <FieldContainer
        attrName="zoom.innerContent"
        label={__('Zoom', 'rubicon-maps')}
        description={__('Override the default zoom for this map instance.', 'rubicon-maps')}
        features={{ sticky: false }}
      >
        <TextContainer />
      </FieldContainer>
      <FieldContainer
        attrName="height.innerContent"
        label={__('Height', 'rubicon-maps')}
        description={__('CSS height for this map instance, such as 480px or 60vh.', 'rubicon-maps')}
        features={{ sticky: false }}
      >
        <TextContainer />
      </FieldContainer>
    </GroupContainer>
    <AdminLabelGroup
      defaultGroupAttr={defaultSettingsAttrs?.module?.meta?.adminLabel ?? {}}
    />
  </React.Fragment>
);
