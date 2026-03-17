import React from 'react';

import { __ } from '@wordpress/i18n';

import { setNativeFieldValue } from './native-field';
import { buildGeneratedSyncId, getSavedSyncId } from './sync-id';

const { TextContainer } = window?.divi?.fieldLibrary ?? {};
const { GroupContainer } = window?.divi?.modal ?? {};
const { FieldContainer } = window?.divi?.module ?? {};

const ButtonStyle = {
  appearance: 'none',
  border: '1px solid #0f766e',
  borderRadius: '8px',
  background: '#0f766e',
  color: '#ffffff',
  cursor: 'pointer',
  display: 'inline-flex',
  alignItems: 'center',
  justifyContent: 'center',
  fontSize: '13px',
  fontWeight: 600,
  lineHeight: 1.2,
  minHeight: '36px',
  padding: '0 14px',
};

const SecondaryButtonStyle = {
  ...ButtonStyle,
  background: '#ffffff',
  color: '#0f766e',
};

const NoticeStyle = {
  marginBottom: '14px',
  fontSize: '13px',
  color: '#cbd5e1',
  lineHeight: 1.6,
};

export const SyncSettingsGroup = ({
  attrs,
  id,
  title,
  enableLabel,
  regenerateLabel,
  helperText,
  fieldDescription,
}) => {
  const savedSyncId = getSavedSyncId(attrs?.instanceId);
  const hasSync = Boolean(savedSyncId);
  const [isSyncEnabled, setIsSyncEnabled] = React.useState(hasSync);
  const [pendingSyncId, setPendingSyncId] = React.useState('');
  const groupRef = React.useRef(null);

  React.useEffect(() => {
    if (hasSync) {
      setIsSyncEnabled(true);
    }
  }, [hasSync]);

  React.useEffect(() => {
    if (!pendingSyncId || !groupRef.current) {
      return;
    }

    const input = groupRef.current.querySelector('input, textarea');

    if (!input) {
      return;
    }

    if (setNativeFieldValue(input, pendingSyncId)) {
      setPendingSyncId('');
    }
  }, [pendingSyncId, isSyncEnabled]);

  const handleEnableSync = () => {
    const generated = buildGeneratedSyncId(id || '');
    setIsSyncEnabled(true);
    setPendingSyncId(generated);
  };

  return (
    <GroupContainer
      id="mapSync"
      title={title}
    >
      {!hasSync && !isSyncEnabled ? (
        <React.Fragment>
          <div style={NoticeStyle}>
            {helperText}
          </div>
          <button
            type="button"
            style={ButtonStyle}
            onClick={handleEnableSync}
          >
            {enableLabel}
          </button>
        </React.Fragment>
      ) : (
        <React.Fragment>
          <div ref={groupRef}>
            <FieldContainer
              attrName="instanceId.innerContent"
              label={__('Sync ID', 'rubicon-maps')}
              description={fieldDescription}
              features={{ sticky: false }}
            >
              <TextContainer />
            </FieldContainer>
          </div>
          <div style={{ margin: '-8px 0 14px', fontSize: '12px', color: '#94a3b8', lineHeight: 1.5 }}>
            {__('Saved Sync ID:', 'rubicon-maps')} <strong>{savedSyncId}</strong>
          </div>
          <button
            type="button"
            style={SecondaryButtonStyle}
            onClick={handleEnableSync}
          >
            {regenerateLabel}
          </button>
        </React.Fragment>
      )}
    </GroupContainer>
  );
};
