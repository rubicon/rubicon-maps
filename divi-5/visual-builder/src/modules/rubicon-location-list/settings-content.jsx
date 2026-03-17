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
      id="listFilters"
      title={__('Location Filters', 'rubicon-maps')}
    >
      <FieldContainer
        attrName="category.innerContent"
        label={__('Categories', 'rubicon-maps')}
        description={__('Comma-separated category slugs to include in the list.', 'rubicon-maps')}
        features={{ sticky: false }}
      >
        <TextContainer />
      </FieldContainer>
      <FieldContainer
        attrName="region.innerContent"
        label={__('Regions', 'rubicon-maps')}
        description={__('Comma-separated region slugs to include in the list.', 'rubicon-maps')}
        features={{ sticky: false }}
      >
        <TextContainer />
      </FieldContainer>
      <FieldContainer
        attrName="locationIds.innerContent"
        label={__('Specific Location IDs', 'rubicon-maps')}
        description={__('Optional comma-separated post IDs to limit this list to explicit locations.', 'rubicon-maps')}
        features={{ sticky: false }}
      >
        <TextContainer />
      </FieldContainer>
    </GroupContainer>
    <SyncSettingsGroup
      attrs={attrs}
      id={id}
      title={__('Map Sync', 'rubicon-maps')}
      helperText={__('This module is currently standalone. Enable sync if you want to pair it with a Rubicon Maps Map.', 'rubicon-maps')}
      enableLabel={__('Enable Sync with Map', 'rubicon-maps')}
      regenerateLabel={__('Regenerate Sync ID', 'rubicon-maps')}
      fieldDescription={__('Shared ID used to sync this module with a paired Rubicon Maps Map. Use the same value in the map module.', 'rubicon-maps')}
    />
    <AdminLabelSettingsGroup defaultLabel={__('Rubicon Maps Listing', 'rubicon-maps')} />
  </React.Fragment>
  );
};
