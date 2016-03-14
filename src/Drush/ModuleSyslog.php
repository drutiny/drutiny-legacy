<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\AuditResponse\AuditResponse;

class ModuleSyslog extends Check {

  public function check() {
    $response = new AuditResponse('module/syslog');
    $context = $this->context;
    $response->test(function () use ($context) {
      return $context->drush->moduleEnabled('syslog');
    });

    return $response;
  }
}
