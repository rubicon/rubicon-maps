import React from 'react';

import { __ } from '@wordpress/i18n';

import { PreviewShell } from '../shared/preview';
import { renderPills, usePreviewLabels } from '../shared/preview-data';
import { getSavedSyncId } from '../shared/sync-id';
import { ModuleClassnames } from './module-classnames';
import { ModuleScriptData } from './module-script-data';
import { ModuleStyles } from './module-styles';

const { ModuleContainer } = window?.divi?.module ?? {};

export const RubiconLocationListEdit = ({
  attrs,
  elements,
  id,
  name,
}) => {
  const syncId = getSavedSyncId(attrs?.instanceId);
  const labels = usePreviewLabels({
    categoryAttr: attrs?.category,
    regionAttr: attrs?.region,
    locationIdsAttr: attrs?.locationIds,
  });
  const items = [
    { label: __('Sync ID', 'rubicon-maps'), value: syncId || __('Standalone', 'rubicon-maps') },
    { label: __('Categories', 'rubicon-maps'), value: renderPills(labels.categories) },
    { label: __('Regions', 'rubicon-maps'), value: renderPills(labels.regions) },
    { label: __('Locations', 'rubicon-maps'), value: renderPills(labels.locations) },
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
        accent="#1d4ed8"
        eyebrow={__('Rubicon Maps', 'rubicon-maps')}
        title={__('Linked location list', 'rubicon-maps')}
        items={items}
      >
        <div
          style={{
            display: 'grid',
            gap: '10px',
          }}
        >
          {['Featured location', 'Regional office', 'Partner site'].map((label, index) => (
            <div
              key={label}
              style={{
                display: 'grid',
                gap: '4px',
                borderRadius: '14px',
                padding: '14px 16px',
                background: index === 0 ? 'linear-gradient(135deg, #dbeafe 0%, #ffffff 100%)' : '#f8fafc',
                border: '1px solid rgba(29, 78, 216, 0.12)',
              }}
            >
              <div style={{ fontSize: '15px', fontWeight: 700, color: '#0f172a' }}>{label}</div>
              <div style={{ fontSize: '13px', color: '#334155' }}>
                {__('The frontend runtime loads real locations matching this module instance and keeps them synced with the paired Rubicon map.', 'rubicon-maps')}
              </div>
            </div>
          ))}
        </div>
      </PreviewShell>
    </ModuleContainer>
  );
};
