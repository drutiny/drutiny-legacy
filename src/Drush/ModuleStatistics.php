<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\AuditResponse\AuditResponse;

class ModuleStatistics extends Check {

  public function check() {
    $response = new AuditResponse('module/statistics');
    $context = $this->context;
    $response->test(function () use ($context) {
      return !$context->drush->moduleEnabled('statistics');
    });

    return $response;
  }
}
