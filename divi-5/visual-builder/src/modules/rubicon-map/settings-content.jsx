import React from 'react';

import { __ } from '@wordpress/i18n';

import { AdminLabelSettingsGroup } from '../shared/admin-label-settings-group';
import { SyncSettingsGroup } from '../shared/sync-settings-group';

const { TextContainer } = window?.divi?.fieldLibrary ?? {};
const { GroupContainer } = window?.divi?.modal ?? {};
const { FieldContainer } = window?.divi?.module ?? {};

export const SettingsContent = ({ defaultSettingsAttrs, attrs, id }) => {
  return (
    <React.Fragment>
    <GroupContainer
      id="mapFilters"
      title={__('Location Filters', 'rubicon-maps')}
    >
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
    <SyncSettingsGroup
      attrs={attrs}
      id={id}
      title={__('Map Sync', 'rubicon-maps')}
      helperText={__('This module is currently standalone. Enable sync if you want to pair it with a Rubicon Maps Listing.', 'rubicon-maps')}
      enableLabel={__('Enable Sync with Listing', 'rubicon-maps')}
      regenerateLabel={__('Regenerate Sync ID', 'rubicon-maps')}
      fieldDescription={__('Shared ID used to sync this module with a paired Rubicon Maps Listing. Use the same value in the listing module.', 'rubicon-maps')}
    />
    <AdminLabelSettingsGroup defaultLabel={__('Rubicon Maps Map', 'rubicon-maps')} />
  </React.Fragment>
  );
};
