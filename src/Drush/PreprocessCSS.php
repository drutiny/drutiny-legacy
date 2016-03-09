<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\Base\AuditResponse;

class PreprocessCss extends Check {
  public function check() {
    $json = $this->context->drush->variableGet('preprocess_css', '--exact --format=json')->parseJson(TRUE);
    $output = (int) $json['preprocess_css'];

    $response = new AuditResponse();
    $response->setDescription('With CSS optimization disabled, your website visitors are experiencing slower page performance and the server load is increased.');
    $response->setRemediation("Enable CSS optimization on Drupal's Performance page");
    if ($output === 1) {
      $response->setSuccess('CSS aggregation is enabled');
    }
    else {
      $response->setFailure('CSS aggregation is not enabled');
    }

    return $response;
  }
}
