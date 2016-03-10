<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\Base\AuditResponse;
use SiteAudit\Executor\ResultException;
use Symfony\Component\Console\Output\OutputInterface;

class PreprocessCss extends Check {
  public function check() {
    $response = new AuditResponse();
    $response->setDescription('With CSS optimization disabled, your website visitors are experiencing slower page performance and the server load is increased.');
    $response->setRemediation("Enable CSS optimization on Drupal's Performance page");

    $enabled = (int) $this->context->drush->getVariable('preprocess_css', 0);
    if ($enabled) {
      $response->setSuccess('CSS aggregation is enabled');
    }
    else {
      $response->setFailure('CSS aggregation is not enabled');
    }

    return $response;
  }
}
