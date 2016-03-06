<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\AuditCheck;
use SiteAudit\Base\AuditResponse;

class PreprocessCss extends AuditCheck {
  public function check() {
    $output = (int) $this->getVariable('preprocess_css');

    $response = new AuditResponse();
    $response->setDescription('With CSS optimization disabled, your website visitors are experiencing slower page performance and the server load is increased.');
    $response->setRemediation("Enable CSS optimization on Drupal's Performance page");
    if ($output === 1) {
      $response->setSuccess('CSS aggregation is enabled');
    }
    else {
      $response->setFailure('CSS aggregation is not enabled');
    }

    $this->output->writeln((string) $response);
  }
}
