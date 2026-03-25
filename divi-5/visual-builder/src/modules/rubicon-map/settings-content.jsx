import React from 'react';

import { __ } from '@wordpress/i18n';

import { SearchableFilterField } from '../shared/searchable-filter-field';
import { SyncSettingsGroup } from '../shared/sync-settings-group';

const { SelectContainer, TextContainer } = window?.divi?.fieldLibrary ?? {};
const { GroupContainer } = window?.divi?.modal ?? {};
const {
  AdminLabelGroup,
  FieldContainer,
} = window?.divi?.module ?? {};

const AdminLabelDefaults = {
  innerContent: {
    desktop: {
      value: __('Rubicon Maps Map', 'rubicon-maps'),
    },
  },
};

export const SettingsContent = ({ defaultSettingsAttrs, attrs, id }) => {
  const renderSelect = (options) => (
    SelectContainer ? <SelectContainer options={options} /> : <TextContainer />
  );

  return (
    <React.Fragment>
    <GroupContainer
      id="mapFilters"
      title={__('Location Filters', 'rubicon-maps')}
    >
      <SearchableFilterField
        attrName="category.innerContent"
        label={__('Categories', 'rubicon-maps')}
        description={__('Search and select category terms to include in this map.', 'rubicon-maps')}
        valueAttr={attrs?.category}
        kind="categories"
        placeholder={__('Search categories…', 'rubicon-maps')}
      />
      <SearchableFilterField
        attrName="region.innerContent"
        label={__('Regions', 'rubicon-maps')}
        description={__('Search and select region terms to include in this map.', 'rubicon-maps')}
        valueAttr={attrs?.region}
        kind="regions"
        placeholder={__('Search regions…', 'rubicon-maps')}
      />
      <SearchableFilterField
        attrName="locationIds.innerContent"
        label={__('Specific Locations', 'rubicon-maps')}
        description={__('Optionally limit this map to an explicit set of locations.', 'rubicon-maps')}
        valueAttr={attrs?.locationIds}
        kind="locations"
        placeholder={__('Search locations…', 'rubicon-maps')}
      />
    </GroupContainer>
    <GroupContainer
      id="mapDisplay"
      title={__('Map Display', 'rubicon-maps')}
    >
      <FieldContainer
        attrName="viewportMode.innerContent"
        label={__('Viewport Mode', 'rubicon-maps')}
        description={__('Choose whether the map automatically fits the displayed locations or uses a manual center and zoom. Use Plugin Default to follow the global Rubicon Maps settings.', 'rubicon-maps')}
        features={{ sticky: false }}
      >
        {renderSelect([
          { label: __('Use Plugin Default', 'rubicon-maps'), value: 'default' },
          { label: __('Auto-fit displayed locations', 'rubicon-maps'), value: 'auto_fit' },
          { label: __('Manual center and zoom', 'rubicon-maps'), value: 'manual' },
        ])}
      </FieldContainer>
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
        description={__('Override the default zoom for this map instance. Leave blank to use the plugin default.', 'rubicon-maps')}
        features={{ sticky: false }}
      >
        <TextContainer />
      </FieldContainer>
      <FieldContainer
        attrName="height.innerContent"
        label={__('Height', 'rubicon-maps')}
        description={__('CSS height for this map instance, such as 480px or 60vh. Leave blank to use the plugin default.', 'rubicon-maps')}
        features={{ sticky: false }}
      >
        <TextContainer />
      </FieldContainer>
      <FieldContainer
        attrName="autoFitPadding.innerContent"
        label={__('Auto-fit Padding', 'rubicon-maps')}
        description={__('Padding to apply when the map is fitting its viewport to the displayed locations. Leave blank to use the plugin default.', 'rubicon-maps')}
        features={{ sticky: false }}
      >
        <TextContainer />
      </FieldContainer>
    </GroupContainer>
    <GroupContainer
      id="mapSettings"
      title={__('Map Settings', 'rubicon-maps')}
    >
      <FieldContainer
        attrName="provider.innerContent"
        label={__('Provider', 'rubicon-maps')}
        description={__('Leaflet is the supported provider path for v1.', 'rubicon-maps')}
        features={{ sticky: false }}
      >
        {renderSelect([
          { label: __('Leaflet / OpenStreetMap', 'rubicon-maps'), value: 'leaflet' },
        ])}
      </FieldContainer>
      <FieldContainer
        attrName="tilePreset.innerContent"
        label={__('Tile Preset', 'rubicon-maps')}
        description={__('Choose the Leaflet tile preset for this map instance. Use Plugin Default to follow the global Rubicon Maps settings.', 'rubicon-maps')}
        features={{ sticky: false }}
      >
        {renderSelect([
          { label: __('Use Plugin Default', 'rubicon-maps'), value: 'default' },
          { label: __('OpenStreetMap', 'rubicon-maps'), value: 'openstreetmap' },
          { label: __('CARTO Light', 'rubicon-maps'), value: 'carto_light' },
          { label: __('CARTO Dark', 'rubicon-maps'), value: 'carto_dark' },
          { label: __('OpenTopoMap', 'rubicon-maps'), value: 'opentopomap' },
          { label: __('Custom Tile URL', 'rubicon-maps'), value: 'custom' },
        ])}
      </FieldContainer>
      <FieldContainer
        attrName="zoomControl.innerContent"
        label={__('Show Zoom Control', 'rubicon-maps')}
        description={__('Controls whether the built-in zoom buttons are visible. Use Plugin Default to follow the global Rubicon Maps settings.', 'rubicon-maps')}
        features={{ sticky: false }}
      >
        {renderSelect([
          { label: __('Use Plugin Default', 'rubicon-maps'), value: 'default' },
          { label: __('On', 'rubicon-maps'), value: 'on' },
          { label: __('Off', 'rubicon-maps'), value: 'off' },
        ])}
      </FieldContainer>
      <FieldContainer
        attrName="scrollWheelZoom.innerContent"
        label={__('Scroll Wheel Zoom', 'rubicon-maps')}
        description={__('Controls whether the mouse wheel zooms the map. Use Plugin Default to follow the global Rubicon Maps settings.', 'rubicon-maps')}
        features={{ sticky: false }}
      >
        {renderSelect([
          { label: __('Use Plugin Default', 'rubicon-maps'), value: 'default' },
          { label: __('On', 'rubicon-maps'), value: 'on' },
          { label: __('Off', 'rubicon-maps'), value: 'off' },
        ])}
      </FieldContainer>
      <FieldContainer
        attrName="doubleClickZoom.innerContent"
        label={__('Double-click Zoom', 'rubicon-maps')}
        description={__('Controls whether double-clicking zooms the map. Use Plugin Default to follow the global Rubicon Maps settings.', 'rubicon-maps')}
        features={{ sticky: false }}
      >
        {renderSelect([
          { label: __('Use Plugin Default', 'rubicon-maps'), value: 'default' },
          { label: __('On', 'rubicon-maps'), value: 'on' },
          { label: __('Off', 'rubicon-maps'), value: 'off' },
        ])}
      </FieldContainer>
    </GroupContainer>
    <GroupContainer
      id="popupSettings"
      title={__('Popup Settings', 'rubicon-maps')}
    >
      <FieldContainer
        attrName="popupTrigger.innerContent"
        label={__('Popup Trigger', 'rubicon-maps')}
        description={__('Choose how marker popups should open. Use Plugin Default to follow the global Rubicon Maps settings.', 'rubicon-maps')}
        features={{ sticky: false }}
      >
        {renderSelect([
          { label: __('Use Plugin Default', 'rubicon-maps'), value: 'default' },
          { label: __('On Click', 'rubicon-maps'), value: 'click' },
          { label: __('On Hover', 'rubicon-maps'), value: 'hover' },
        ])}
      </FieldContainer>
      <FieldContainer
        attrName="popupMaxWidth.innerContent"
        label={__('Popup Max Width', 'rubicon-maps')}
        description={__('Maximum width for marker popups in pixels. Leave blank to use the plugin default.', 'rubicon-maps')}
        features={{ sticky: false }}
      >
        <TextContainer />
      </FieldContainer>
      <FieldContainer
        attrName="closeOnMapClick.innerContent"
        label={__('Close Popup On Map Click', 'rubicon-maps')}
        description={__('When enabled, clicking the map closes the active popup. Use Plugin Default to follow the global Rubicon Maps settings.', 'rubicon-maps')}
        features={{ sticky: false }}
      >
        {renderSelect([
          { label: __('Use Plugin Default', 'rubicon-maps'), value: 'default' },
          { label: __('On', 'rubicon-maps'), value: 'on' },
          { label: __('Off', 'rubicon-maps'), value: 'off' },
        ])}
      </FieldContainer>
      <FieldContainer
        attrName="autoClosePopup.innerContent"
        label={__('Auto-close Popup', 'rubicon-maps')}
        description={__('When enabled, opening a popup closes the previously opened popup. Use Plugin Default to follow the global Rubicon Maps settings.', 'rubicon-maps')}
        features={{ sticky: false }}
      >
        {renderSelect([
          { label: __('Use Plugin Default', 'rubicon-maps'), value: 'default' },
          { label: __('On', 'rubicon-maps'), value: 'on' },
          { label: __('Off', 'rubicon-maps'), value: 'off' },
        ])}
      </FieldContainer>
      <FieldContainer
        attrName="openAllPopups.innerContent"
        label={__('Open All Popups', 'rubicon-maps')}
        description={__('When enabled, the map attempts to open all popups after rendering. Use Plugin Default to follow the global Rubicon Maps settings.', 'rubicon-maps')}
        features={{ sticky: false }}
      >
        {renderSelect([
          { label: __('Use Plugin Default', 'rubicon-maps'), value: 'default' },
          { label: __('Off', 'rubicon-maps'), value: 'off' },
          { label: __('On', 'rubicon-maps'), value: 'on' },
        ])}
      </FieldContainer>
    </GroupContainer>
    <GroupContainer
      id="markerClustering"
      title={__('Marker Clustering', 'rubicon-maps')}
    >
      <FieldContainer
        attrName="enableClustering.innerContent"
        label={__('Enable Clustering', 'rubicon-maps')}
        description={__('Groups nearby markers together on the Leaflet map. Use Plugin Default to follow the global Rubicon Maps settings.', 'rubicon-maps')}
        features={{ sticky: false }}
      >
        {renderSelect([
          { label: __('Use Plugin Default', 'rubicon-maps'), value: 'default' },
          { label: __('On', 'rubicon-maps'), value: 'on' },
          { label: __('Off', 'rubicon-maps'), value: 'off' },
        ])}
      </FieldContainer>
      <FieldContainer
        attrName="clusterRadius.innerContent"
        label={__('Cluster Radius', 'rubicon-maps')}
        description={__('Maximum radius used when grouping nearby markers. Leave blank to use the plugin default.', 'rubicon-maps')}
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
      mode="generate"
    />
    <AdminLabelGroup
      defaultGroupAttr={defaultSettingsAttrs?.module?.meta?.adminLabel ?? AdminLabelDefaults}
    />
  </React.Fragment>
  );
};
