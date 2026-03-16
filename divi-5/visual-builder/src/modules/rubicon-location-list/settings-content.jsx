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
      id="listFilters"
      title={__('Location Filters', 'rubicon-maps')}
    >
      <FieldContainer
        attrName="instanceId.innerContent"
        label={__('Map ID', 'rubicon-maps')}
        description={__('Optional shared ID used to sync this list with a Rubicon Map module.', 'rubicon-maps')}
        features={{ sticky: false }}
      >
        <TextContainer />
      </FieldContainer>
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
    <AdminLabelGroup
      defaultGroupAttr={defaultSettingsAttrs?.module?.meta?.adminLabel ?? {}}
    />
  </React.Fragment>
);
