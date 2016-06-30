<?php

namespace SiteAudit\Check\Drush;

use SiteAudit\Check\Check;
use SiteAudit\Annotation\CheckInfo;

/**
 * @CheckInfo(
 *  title = "Shield",
 *  description = "The shield module protects Drupal sites from prying eyes, often it is used to protect sites that are not yet live, but should never be enabled for live sites.",
 *  remediation = "Disable shield through the shield user interface.",
 *  success = "Shield is disabled.",
 *  failure = "Shield is enabled.",
 *  exception = "Could not determine shield setting.",
 * )
 */
class ShieldDisabled extends Check {
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
