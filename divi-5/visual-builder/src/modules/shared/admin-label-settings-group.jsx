import React from 'react';

import { __ } from '@wordpress/i18n';

import { setNativeFieldValue } from './native-field';

const { TextContainer } = window?.divi?.fieldLibrary ?? {};
const { GroupContainer } = window?.divi?.modal ?? {};
const { FieldContainer } = window?.divi?.module ?? {};

export const AdminLabelSettingsGroup = ({ defaultLabel }) => {
  const groupRef = React.useRef(null);

  React.useEffect(() => {
    const applyDefault = () => {
      const input = groupRef.current?.querySelector('input, textarea');

      if (!input || input.value.trim()) {
        return false;
      }

      return setNativeFieldValue(input, defaultLabel);
    };

    if (applyDefault()) {
      return;
    }

    const intervalId = window.setInterval(() => {
      if (applyDefault()) {
        window.clearInterval(intervalId);
      }
    }, 250);

    return () => {
      window.clearInterval(intervalId);
    };
  }, [defaultLabel]);

  return (
    <GroupContainer
      id="elementLabel"
      title={__('Element Label', 'rubicon-maps')}
    >
      <div ref={groupRef}>
        <FieldContainer
          attrName="module.meta.adminLabel"
          label={__('Element Label', 'rubicon-maps')}
          description={__('Admin-only label used to identify this Rubicon Maps module inside the builder.', 'rubicon-maps')}
          features={{ sticky: false }}
        >
          <TextContainer />
        </FieldContainer>
      </div>
    </GroupContainer>
  );
};
