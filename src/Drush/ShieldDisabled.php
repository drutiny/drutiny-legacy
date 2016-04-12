<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\AuditResponse\AuditResponse;

class ShieldDisabled extends Check {
  public function check() {
    $response = new AuditResponse('variable/shield_disabled', $this);

    $response->test(function ($check) {
      $context = $check->context;
      $json = (int) $context->drush->getVariable('shield_enabled', 0);
      return ! (bool) $json;
    });

    return $response;
  }
}
