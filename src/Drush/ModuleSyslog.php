<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\AuditResponse;

class ModuleSyslog extends DrushCheck {
  public function check() {
    $output = $this->executeDrush('pm-info syslog', ['format' => 'json']);

    $response = new AuditResponse();
    $response->setDescription("Use the Syslog module instead of the Database logging module to log events and issues. The Syslog module saves website events to your server's syslog (or Windows eventlog).");
    $response->setRemediation("Enable Syslog on Drupal's module administration page");
    if ($output->syslog->status === "enabled") {
      $response->setSuccess('Syslog module is enabled');
    }
    else {
      $response->setFailure('Syslog module is disabled');
    }

    $this->output->writeln((string) $response);
  }
}
