<?php

namespace SiteAudit\Check\Drush;

use SiteAudit\Check\Check;
use SiteAudit\Annotation\CheckInfo;
use SiteAudit\Executor\DoesNotApplyException;

/**
 * @CheckInfo(
 *  title = "Securepages redirect to HTTPS",
 *  description = "The securepages module redirects the user to HTTPS given certain conditions. This check ensures that all traffic is sent to HTTPS.",
 *  remediation = "Set <code>securepages_enable</code> to 1, <code>securepages_switch</code> to 0, and <code>securepages_secure</code> to 0.",
 *  success = "Securepages is redirectly all requests to HTTPS.",
 *  failure = "Securepages is not configured correctly to redirect all requests to HTTPS.",
 *  exception = "Could not determine securepages settings.",
 * )
 */
class SecurepagesRedirectToHttps extends Check {
  public function check()
  {
    if ($this->context->drush->moduleEnabled('securepages')) {
      $securepages_enable = (bool) $this->context->drush->getVariable('securepages_enable', 0);
      $securepages_switch = (bool) $this->context->drush->getVariable('securepages_switch', FALSE);
      $securepages_secure = (bool) $this->context->drush->getVariable('securepages_secure', 1);

      // Securepages must:
      // * be enabled
      // * not switch back to HTTP
      // * make all pages secure by default.
      if ($securepages_enable && !$securepages_switch && !$securepages_secure) {
        return TRUE;
      }
      else {
        return FALSE;
      }
    }

    throw new DoesNotApplyException('securepages is not enabled.');
  }
}
