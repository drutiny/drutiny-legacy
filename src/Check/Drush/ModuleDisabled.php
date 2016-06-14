<?php

namespace SiteAudit\Check\Drush;

use SiteAudit\Check\Check;

class ModuleDisabled extends Check {
  static public function getNamespace()
  {
    return 'module/disabled';
  }


  public function check() {
    $modules = $this->getOption('modules');
    $this->setToken('modules', '<code>' . implode('</code>, <code>', $modules) . '</code>');

    $enabled = [];
    foreach ($modules as $module_name) {
      try {
        if ($this->context->drush->moduleEnabled($module_name)) {
          throw new \Exception($module_name);
        }
      }
      catch (\Exception $e) {
        $enabled[] = $module_name;
      }
    }

    if (!empty($enabled)) {
      $this->setToken('enabled', '<code>' . implode('</code>, <code>', $enabled) . '</code>');
      return FALSE;
    }

    return TRUE;
  }
}
