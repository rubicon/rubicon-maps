import { RubiconMapEdit } from './edit';
import metadata from './module.json';
import { ModuleClassnames } from './module-classnames';
import { ModuleScriptData } from './module-script-data';
import { ModuleStyles } from './module-styles';
import { SettingsAdvanced } from './settings-advanced';
import { SettingsContent } from './settings-content';
import { SettingsDesign } from './settings-design';

export const rubiconMapModuleMetadata = metadata;

export const rubiconMapModule = {
  renderers: {
    edit: RubiconMapEdit,
  },
  settings: {
    content: SettingsContent,
    design: SettingsDesign,
    advanced: SettingsAdvanced,
  },
  classnamesFunction: ModuleClassnames,
  scriptDataComponent: ModuleScriptData,
  stylesComponent: ModuleStyles,
};
