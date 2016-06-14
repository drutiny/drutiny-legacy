<?php

namespace SiteAudit\Check\Drush;

use SiteAudit\Check\Check;

class ModuleEnabled extends Check {

  static public function getNamespace()
  {
    return 'module/enabled';
  }

  public function check()
  {
    $modules = $this->getOption('modules');
    if (empty($modules)) {
      return TRUE;
    }
    $this->setToken('modules', '<code>' . implode('</code>, <code>', $modules) . '</code>');

    $not_enabled = [];
    foreach ($modules as $module_name) {
      try {
        if (!$this->context->drush->moduleEnabled($module_name)) {
          throw new \Exception($module_name);
        }
      }
      catch (\Exception $e) {
        $not_enabled[] = $module_name;
      }
    }

    if (!empty($not_enabled)) {
      $this->setToken('not_enabled', '<code>' . implode('</code>, <code>', $not_enabled) . '</code>');
      return FALSE;
    }

    return TRUE;
  }
}
