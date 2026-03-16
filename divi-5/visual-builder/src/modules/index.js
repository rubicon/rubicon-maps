import { addAction } from '@wordpress/hooks';

import { rubiconLocationListModule, rubiconLocationListModuleMetadata } from './rubicon-location-list';
import { rubiconMapModule, rubiconMapModuleMetadata } from './rubicon-map';

const { registerModule } = window?.divi?.moduleLibrary ?? {};

addAction('divi.moduleLibrary.registerModuleLibraryStore.after', 'rubicon-maps', () => {
  if (typeof registerModule !== 'function') {
    return;
  }

  registerModule(rubiconMapModuleMetadata, rubiconMapModule);
  registerModule(rubiconLocationListModuleMetadata, rubiconLocationListModule);
});
