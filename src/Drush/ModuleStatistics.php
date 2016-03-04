<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\AuditResponse;

class ModuleStatistics extends DrushCheck {
  public function check() {
    $output = $this->executeDrush('pm-info statistics', ['format' => 'json']);

    $response = new AuditResponse();
    $response->setDescription('The statistics module tracks page views and logs access statistics for your site. Because it is triggered on every page load it can slow sites down. Consider using a client-side analytics solution (such as Google Analytics) instead.');
    $response->setRemediation("Disable Statistics on Drupal's module administration page");
    if ($output->statistics->status === "not installed") {
      $response->setSuccess('Statistics module is disabled');
    }
    else {
      $response->setFailure('Statistics module is enabled');
    }

    $this->output->writeln((string) $response);
  }
}
