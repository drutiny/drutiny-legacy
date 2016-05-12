<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\AuditResponse\AuditResponse;

class SecurepagesBasepath extends Check {
  public function check() {
    $response = new AuditResponse('variable/securepages_basepath', $this);

    $response->test(function ($check) {
      $context = $check->context;

      // If the module is disabled, then no shield.
      if ($check->context->drush->moduleEnabled('securepages')) {
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
    });

    return $response;
  }
}
