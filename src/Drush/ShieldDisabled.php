<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\AuditResponse\AuditResponse;

class ShieldDisabled extends Check {
  public function check() {
    $response = new AuditResponse('variable/shield_disabled', $this);

    $response->test(function ($check) {
      $context = $check->context;

      // If the module is disabled, then no shield.
      if ($check->context->drush->moduleEnabled('shield')) {
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
    });

    return $response;
  }
}
