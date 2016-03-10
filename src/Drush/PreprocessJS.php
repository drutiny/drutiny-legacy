<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\Base\AuditResponse;
use SiteAudit\Executor\ResultException;
use Symfony\Component\Console\Output\OutputInterface;

class PreprocessJS extends Check {
  public function check() {

    $response = new AuditResponse();
    $response->setDescription('With JavaScript file aggregation disabled, your website visitors are experiencing slower page loads and the server load is increased.');
    $response->setRemediation("Enable JS optimization on Drupal's Performance page");

    $enabled = (int) $this->context->drush->getVariable('preprocess_js', 0);
    if ($enabled) {
      $response->setSuccess('JS aggregation is enabled');
    }
    else {
      $response->setFailure('JS aggregation is not enabled');
    }

    return $response;
  }
}
