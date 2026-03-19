import React from 'react';

import { __ } from '@wordpress/i18n';

import { SyncSettingsGroup } from '../shared/sync-settings-group';
import { getSavedSyncId } from '../shared/sync-id';

const { SelectContainer, TextContainer } = window?.divi?.fieldLibrary ?? {};
const { GroupContainer } = window?.divi?.modal ?? {};
const {
  AdminLabelGroup,
  FieldContainer,
} = window?.divi?.module ?? {};

const AdminLabelDefaults = {
  innerContent: {
    desktop: {
      value: __('Rubicon Maps Listing', 'rubicon-maps'),
    },
  },
};

export const SettingsContent = ({ defaultSettingsAttrs, attrs, id }) => {
  const savedSyncId = getSavedSyncId(attrs?.instanceId);
  const hasSync = Boolean(savedSyncId);
  const renderSelect = (options) => (
    SelectContainer ? <SelectContainer options={options} /> : <TextContainer />
  );

  return (
    <React.Fragment>
    <GroupContainer
      id="listFilters"
      title={__('Location Filters', 'rubicon-maps')}
    >
      {hasSync ? (
        <div style={{ fontSize: '13px', color: '#cbd5e1', lineHeight: 1.6 }}>
          {__('This listing is synced to a Rubicon Maps Map. Clear the Sync ID to restore independent listing filters.', 'rubicon-maps')}
        </div>
      ) : (
        <React.Fragment>
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
        </React.Fragment>
      )}
    </GroupContainer>
    <GroupContainer
      id="listDisplay"
      title={__('List Display', 'rubicon-maps')}
    >
      <FieldContainer
        attrName="useFixedHeight.innerContent"
        label={__('Use Fixed List Height', 'rubicon-maps')}
        description={__('When enabled, the listing uses a fixed height with scrolling instead of growing endlessly.', 'rubicon-maps')}
        features={{ sticky: false }}
      >
        {renderSelect([
          { label: __('Off', 'rubicon-maps'), value: 'off' },
          { label: __('On', 'rubicon-maps'), value: 'on' },
        ])}
      </FieldContainer>
      <FieldContainer
        attrName="height.innerContent"
        label={__('List Height', 'rubicon-maps')}
        description={__('Optional CSS height for the listing. When left blank, synced lists can adopt the matched map height the first time fixed height is enabled.', 'rubicon-maps')}
        features={{ sticky: false }}
      >
        <TextContainer />
      </FieldContainer>
    </GroupContainer>
    <SyncSettingsGroup
      attrs={attrs}
      id={id}
      title={__('Map Sync', 'rubicon-maps')}
      helperText={__('Paste the Sync ID from a Rubicon Maps Map if you want this listing to follow that map at runtime.', 'rubicon-maps')}
      enableLabel=""
      regenerateLabel=""
      fieldDescription={__('Shared ID used to sync this listing with a paired Rubicon Maps Map. Paste the same value used in the map module.', 'rubicon-maps')}
      mode="paste"
    />
    <AdminLabelGroup
      defaultGroupAttr={defaultSettingsAttrs?.module?.meta?.adminLabel ?? AdminLabelDefaults}
    />
  </React.Fragment>
  );
};
