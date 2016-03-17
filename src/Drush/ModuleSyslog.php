<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\AuditResponse\AuditResponse;

class ModuleSyslog extends Check {

  public function check() {
    $response = new AuditResponse('module/syslog', $this);
    
    $response->test(function ($check) {
      $context = $check->context;
      return $context->drush->moduleEnabled('syslog');
    });

    return $response;
  }
}
