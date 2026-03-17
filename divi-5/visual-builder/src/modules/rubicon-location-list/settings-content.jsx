import React from 'react';

import { __ } from '@wordpress/i18n';

import { SyncSettingsGroup } from '../shared/sync-settings-group';
import { getSavedSyncId } from '../shared/sync-id';

const { TextContainer } = window?.divi?.fieldLibrary ?? {};
const { GroupContainer } = window?.divi?.modal ?? {};
const {
  AdminLabelGroup,
  FieldContainer,
} = window?.divi?.module ?? {};

const SyncNoticeStyle = {
  fontSize: '13px',
  lineHeight: 1.6,
  color: '#cbd5e1',
};

export const SettingsContent = ({ defaultSettingsAttrs, attrs, id }) => {
  const hasSyncId = Boolean(getSavedSyncId(attrs?.instanceId));

  return (
    <React.Fragment>
      <GroupContainer
        id="listFilters"
        title={__('Location Filters', 'rubicon-maps')}
      >
        {hasSyncId ? (
          <div style={SyncNoticeStyle}>
            {__('This listing follows the synced Rubicon Maps Map at runtime. Clear the Sync ID in Map Sync to restore independent listing filters.', 'rubicon-maps')}
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
      <SyncSettingsGroup
        attrs={attrs}
        id={id}
        title={__('Map Sync', 'rubicon-maps')}
        helperText={__('Paste the Sync ID from the Rubicon Maps Map module you want this listing to follow.', 'rubicon-maps')}
        fieldDescription={__('Shared ID used to sync this module with a paired Rubicon Maps Map. Paste the value from the map module here.', 'rubicon-maps')}
        mode="manual"
      />
      <AdminLabelGroup
        defaultGroupAttr={defaultSettingsAttrs?.module?.meta?.adminLabel ?? {}}
      />
    </React.Fragment>
  );
};
