<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\AuditResponse\AuditResponse;


class ModulePHP extends Check {
  public function check() {
    $response = new AuditResponse('module/php');
    $context = $this->context;
    $response->test(function () use ($context) {
      return !$context->drush->moduleEnabled('php');
    });

    return $response;
  }
}
