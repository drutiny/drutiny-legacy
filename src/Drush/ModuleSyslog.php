<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\Base\AuditResponse;

class ModuleSyslog extends Check {
  public function check() {
    $enabled = $this->context->drush->getModuleStatus('syslog');

    $response = new AuditResponse();
    $response->setDescription("Use the Syslog module instead of the Database logging module to log events and issues. The Syslog module saves website events to your server's syslog (or Windows eventlog).");
    $response->setRemediation("Enable Syslog on Drupal's module administration page");
    if ($enabled) {
      $response->setSuccess('Syslog module is enabled');
    }
    else {
      $response->setFailure('Syslog module is disabled');
    }

    return $response;
  }
}
