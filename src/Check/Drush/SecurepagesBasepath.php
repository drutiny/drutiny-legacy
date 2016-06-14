<?php

namespace SiteAudit\Check\Drush;

use SiteAudit\Check\Check;

class SecurepagesBasepath extends Check {
  protected function getNamespace()
  {
    return 'variable/securepages_basepath';
  }
  public function check()
  {
    $context = $this->context;

    // If the module is disabled, then no shield.
    if ($this->context->drush->moduleEnabled('securepages')) {
      // Shield must be enabled, defaults to on.
      $basepath = $context->drush->getVariable('securepages_basepath', '');
      $basepath_ssl = $context->drush->getVariable('securepages_basepath_ssl', '');
      if (empty($basepath) && empty($basepath_ssl)) {
        return TRUE;
      }
      else {
        return FALSE;
      }
    }

    return TRUE;
  }
}
