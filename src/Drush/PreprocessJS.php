<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\Base\AuditResponse;

class PreprocessJS extends Check {
  public function check() {
    $json = $this->context->drush->variableGet('preprocess_js', '--exact --format=json')->parseJson(TRUE);
    $output = (int) $json['preprocess_js'];

    $response = new AuditResponse();
    $response->setDescription('With JavaScript file aggregation disabled, your website visitors are experiencing slower page loads and the server load is increased.');
    $response->setRemediation("Enable JS optimization on Drupal's Performance page");
    if ($output === 1) {
      $response->setSuccess('JavaScript aggregation is enabled');
    }
    else {
      $response->setFailure('JavaScript aggregation is not enabled');
    }

    return $response;
  }
}
