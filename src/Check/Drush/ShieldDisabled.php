<?php

namespace SiteAudit\Check\Drush;

use SiteAudit\Check\Check;

class ShieldDisabled extends Check {
  static public function getNamespace()
  {
    return 'variable/shield_disabled';
  }
  public function check() {
    $context = $this->context;

    // If the module is disabled, then no shield.
    if ($this->context->drush->moduleEnabled('shield')) {
      // Shield must be enabled, defaults to on.
      $shield_enabled = (bool) (int) $context->drush->getVariable('shield_enabled', 1);
      if ($shield_enabled) {
        // Shield user must be set.
        $shield_user = $context->drush->getVariable('shield_user', '');
        if (!empty($shield_user)) {
          return FALSE;
        }
      }
    }

    return TRUE;
  }
}
