<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\AuditResponse\AuditResponse;

class PreprocessCss extends Check {
  public function check() {
    $response = new AuditResponse('variable/preprocess_css');
    $context = $this->context;
    $cache = $this->getOption('cache', 300);
    $response->test(function () use ($context, $cache) {
      $json = $context->drush->variableGet('preprocess_css', '--exact --format=json')->parseJson(TRUE);
      $output = (int) $json['preprocess_css'];
      return $output;
    });

    return $response;
  }
}
