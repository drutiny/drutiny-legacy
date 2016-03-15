<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\AuditResponse\AuditResponse;

class ModuleStatistics extends Check {

  public function check() {
    $response = new AuditResponse('module/statistics', $this);

    $response->test(function ($check) {
      $context = $check->context;
      return !$context->drush->moduleEnabled('statistics');
    });

    return $response;
  }
}
