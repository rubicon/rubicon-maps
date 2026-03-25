import React from 'react';

import { __ } from '@wordpress/i18n';

import { PreviewShell } from '../shared/preview';
import { getProviderLabel, publishPreviewCount, renderPills, usePreviewLocationData } from '../shared/preview-data';
import { getSavedSyncId } from '../shared/sync-id';
import { getTextValue } from '../shared/value';
import { ModuleClassnames } from './module-classnames';
import { ModuleScriptData } from './module-script-data';
import { ModuleStyles } from './module-styles';

const { ModuleContainer } = window?.divi?.module ?? {};

export const RubiconMapEdit = ({
  attrs,
  elements,
  id,
  name,
}) => {
  const syncId = getSavedSyncId(attrs?.instanceId);
  const providerValue = getTextValue(attrs?.provider);
  const previewData = usePreviewLocationData({
    categoryAttr: attrs?.category,
    regionAttr: attrs?.region,
    locationIdsAttr: attrs?.locationIds,
  });
  const viewportValue = getTextValue(attrs?.viewportMode);
  const clusteringValue = getTextValue(attrs?.enableClustering);
  const provider = getProviderLabel(attrs?.provider) || providerValue || __('Leaflet', 'rubicon-maps');
  const viewportMode = viewportValue === 'manual'
    ? __('Manual', 'rubicon-maps')
    : viewportValue === 'default' || !viewportValue
      ? __('Plugin default', 'rubicon-maps')
      : __('Auto-fit', 'rubicon-maps');
  const clustering = clusteringValue === 'off'
    ? __('Off', 'rubicon-maps')
    : clusteringValue === 'default' || !clusteringValue
      ? __('Plugin default', 'rubicon-maps')
      : __('On', 'rubicon-maps');
  const zoomHeight = [getTextValue(attrs?.zoom), getTextValue(attrs?.height)].filter(Boolean).join(' / ')
    || __('Plugin defaults', 'rubicon-maps');

  React.useEffect(() => {
    publishPreviewCount(syncId, previewData.matchedLocationCount);
  }, [previewData.matchedLocationCount, syncId]);

  const items = [
    { label: __('Sync ID', 'rubicon-maps'), value: syncId || __('Standalone', 'rubicon-maps') },
    { label: __('Provider', 'rubicon-maps'), value: provider },
    { label: __('Viewport', 'rubicon-maps'), value: viewportMode },
    { label: __('# of Locations', 'rubicon-maps'), value: String(previewData.matchedLocationCount) },
    { label: __('Categories', 'rubicon-maps'), value: renderPills(previewData.categories) },
    { label: __('Regions', 'rubicon-maps'), value: renderPills(previewData.regions) },
    { label: __('Locations', 'rubicon-maps'), value: renderPills(previewData.locations) },
    { label: __('Center', 'rubicon-maps'), value: [getTextValue(attrs?.latitude), getTextValue(attrs?.longitude)].filter(Boolean).join(', ') || __('Displayed locations', 'rubicon-maps') },
    { label: __('Zoom / Height', 'rubicon-maps'), value: zoomHeight },
    { label: __('Clustering', 'rubicon-maps'), value: clustering },
  ];

  return (
    <ModuleContainer
      attrs={attrs}
      elements={elements}
      id={id}
      name={name}
      scriptDataComponent={ModuleScriptData}
      stylesComponent={ModuleStyles}
      classnamesFunction={ModuleClassnames}
    >
      {elements.styleComponents({
        attrName: 'module',
      })}
      <PreviewShell
        accent="#0f766e"
        eyebrow={__('Rubicon Maps', 'rubicon-maps')}
        title={__('Interactive map instance', 'rubicon-maps')}
        items={items}
      >
        <div
          style={{
            borderRadius: '16px',
            minHeight: '220px',
            background: 'radial-gradient(circle at top left, rgba(15,118,110,0.18), transparent 38%), linear-gradient(135deg, #d9f3f0 0%, #f8fafc 55%, #ffffff 100%)',
            border: '1px dashed rgba(15,118,110,0.35)',
            padding: '18px',
            display: 'flex',
            alignItems: 'end',
            justifyContent: 'space-between',
            gap: '16px',
          }}
        >
          <div>
            <div style={{ color: '#0f172a', fontSize: '18px', fontWeight: 700, marginBottom: '8px' }}>
              {__('Rubicon Maps map module', 'rubicon-maps')}
            </div>
            <div style={{ color: '#334155', fontSize: '14px', maxWidth: '28rem', lineHeight: 1.6 }}>
              {__('This preview reflects the module filters, provider choice, and map defaults. The saved page renders the live Rubicon Maps frontend runtime.', 'rubicon-maps')}
            </div>
          </div>
          <div
            style={{
              minWidth: '96px',
              textAlign: 'center',
              borderRadius: '14px',
              background: '#ffffff',
              padding: '12px',
              boxShadow: '0 10px 20px rgba(15, 23, 42, 0.08)',
            }}
          >
            <div style={{ fontSize: '12px', color: '#64748b', textTransform: 'uppercase', letterSpacing: '0.08em' }}>
              {__('Provider', 'rubicon-maps')}
            </div>
            <div style={{ fontSize: '16px', color: '#0f766e', fontWeight: 700, marginTop: '6px' }}>
              {provider}
            </div>
          </div>
        </div>
      </PreviewShell>
    </ModuleContainer>
  );
};
