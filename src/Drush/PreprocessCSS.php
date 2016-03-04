<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\AuditResponse;

class PreprocessCss extends DrushCheck {
  public function check() {
    $output = $this->executeDrush('vget preprocess_css');

    $response = new AuditResponse();
    $response->setDescription('With CSS optimization disabled, your website visitors are experiencing slower page performance and the server load is increased.');
    $response->setRemediation("Enable CSS optimization on Drupal's Performance page");
    if ($output === "preprocess_css: '1'") {
      $response->setSuccess('CSS aggregation is enabled');
    }
    else {
      $response->setFailure('CSS aggregation is not enabled');
    }

    $this->output->writeln((string) $response);
  }
}
