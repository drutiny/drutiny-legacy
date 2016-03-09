<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\Base\AuditResponse;
use SiteAudit\Executor\ResultException;

class ModuleStatistics extends Check {
  public function check() {
    $response = new AuditResponse();
    $response->setDescription('The statistics module tracks page views and logs access statistics for your site. Because it is triggered on every page load it can slow sites down. Consider using a client-side analytics solution (such as Google Analytics) instead.');
    $response->setRemediation("Disable Statistics on Drupal's module administration page");

    try {
      $enabled = $this->context->drush->getModuleStatus('statistics');
      if (!$enabled) {
        $response->setSuccess('Statistics module is disabled');
      }
      else {
        $response->setFailure('Statistics module is enabled');
      }
    }
    catch (ResultException $e) {
      $response->setFailure($e->getMessage());
    }
    return $response;
  }
}
