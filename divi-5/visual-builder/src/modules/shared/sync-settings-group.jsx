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

const IconButtonStyle = {
  appearance: 'none',
  border: '1px solid rgba(15, 23, 42, 0.12)',
  borderRadius: '8px',
  background: '#ffffff',
  color: '#0f766e',
  cursor: 'pointer',
  display: 'inline-flex',
  alignItems: 'center',
  justifyContent: 'center',
  width: '36px',
  minWidth: '36px',
  height: '36px',
  padding: 0,
  fontSize: '16px',
  lineHeight: 1,
};

const IconStyle = {
  width: '16px',
  height: '16px',
  fill: 'currentColor',
};

const NoticeStyle = {
  marginBottom: '14px',
  fontSize: '13px',
  color: '#cbd5e1',
  lineHeight: 1.6,
};

const FieldRowStyle = {
  display: 'flex',
  alignItems: 'flex-end',
  gap: '10px',
};

const FieldWrapStyle = {
  flex: '1 1 auto',
  minWidth: 0,
};

const MetaTextStyle = {
  margin: '-8px 0 14px',
  fontSize: '12px',
  color: '#94a3b8',
  lineHeight: 1.5,
};

export const SyncSettingsGroup = ({
  attrs,
  id,
  title,
  enableLabel,
  regenerateLabel,
  helperText,
  fieldDescription,
  mode = 'generator',
}) => {
  const savedSyncId = getSavedSyncId(attrs?.instanceId);
  const hasSync = Boolean(savedSyncId);
  const isGeneratorMode = 'generator' === mode;
  const [isSyncEnabled, setIsSyncEnabled] = React.useState(hasSync || !isGeneratorMode);
  const [pendingSyncId, setPendingSyncId] = React.useState('');
  const [copyFeedback, setCopyFeedback] = React.useState('');
  const groupRef = React.useRef(null);

  React.useEffect(() => {
    if (hasSync) {
      setIsSyncEnabled(true);
    }
  }, [hasSync, isGeneratorMode]);

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

  React.useEffect(() => {
    if (!copyFeedback) {
      return undefined;
    }

    const timer = window.setTimeout(() => {
      setCopyFeedback('');
    }, 1600);

    return () => window.clearTimeout(timer);
  }, [copyFeedback]);

  const handleEnableSync = () => {
    const generated = buildGeneratedSyncId(id || '');
    setIsSyncEnabled(true);
    setPendingSyncId(generated);
  };

  const copyCurrentSyncId = async () => {
    const input = groupRef.current?.querySelector('input, textarea');
    const syncValue = input?.value?.trim?.() || savedSyncId;

    if (!syncValue) {
      setCopyFeedback(__('Nothing to copy yet.', 'rubicon-maps'));
      return;
    }

    try {
      if (navigator?.clipboard?.writeText) {
        await navigator.clipboard.writeText(syncValue);
      } else if (input?.select) {
        input.select();
        document.execCommand('copy');
      } else {
        throw new Error('Clipboard API unavailable');
      }

      setCopyFeedback(__('Copied.', 'rubicon-maps'));
    } catch (error) {
      setCopyFeedback(__('Copy failed.', 'rubicon-maps'));
    }
  };

  return (
    <GroupContainer
      id="mapSync"
      title={title}
    >
      {!hasSync && !isSyncEnabled && isGeneratorMode ? (
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
          {!!helperText && (!isGeneratorMode || !hasSync) ? (
            <div style={NoticeStyle}>
              {helperText}
            </div>
          ) : null}
          <div style={FieldRowStyle}>
            <div
              ref={groupRef}
              style={FieldWrapStyle}
            >
              <FieldContainer
                attrName="instanceId.innerContent"
                label={__('Sync ID', 'rubicon-maps')}
                description={fieldDescription}
                features={{ sticky: false }}
              >
                <TextContainer />
              </FieldContainer>
            </div>
            <button
              type="button"
              style={IconButtonStyle}
              onClick={copyCurrentSyncId}
              aria-label={__('Copy Sync ID', 'rubicon-maps')}
              title={__('Copy Sync ID', 'rubicon-maps')}
            >
              <svg
                aria-hidden="true"
                viewBox="0 0 24 24"
                style={IconStyle}
              >
                <path d="M16 1H6a2 2 0 0 0-2 2v12h2V3h10V1zm3 4H10a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h9a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2zm0 16H10V7h9v14z" />
              </svg>
            </button>
          </div>
          {savedSyncId || copyFeedback ? (
            <div style={MetaTextStyle}>
              {savedSyncId ? (
                <React.Fragment>
                  {__('Saved Sync ID:', 'rubicon-maps')} <strong>{savedSyncId}</strong>
                </React.Fragment>
              ) : null}
              {copyFeedback ? ` ${copyFeedback}` : ''}
            </div>
          ) : null}
          {isGeneratorMode ? (
            <button
              type="button"
              style={SecondaryButtonStyle}
              onClick={handleEnableSync}
            >
              {regenerateLabel}
            </button>
          ) : null}
        </React.Fragment>
      )}
    </GroupContainer>
  );
};
