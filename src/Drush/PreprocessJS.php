<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\AuditCheck;
use SiteAudit\Base\AuditResponse;

class PreprocessJS extends AuditCheck {
  public function check() {
    $output = (int) $this->getVariable('preprocess_js');

    $response = new AuditResponse();
    $response->setDescription('With JavaScript file aggregation disabled, your website visitors are experiencing slower page loads and the server load is increased.');
    $response->setRemediation("Enable JS optimization on Drupal's Performance page");
    if ($output === 1) {
      $response->setSuccess('JavaScript aggregation is enabled');
    }
    else {
      $response->setFailure('JavaScript aggregation is not enabled');
    }

    $this->output->writeln((string) $response);
  }
}
