<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\AuditResponse\AuditResponse;

class PreprocessJS extends Check {
  public function check() {
    $response = new AuditResponse('variable/preprocess_js');
    $context = $this->context;
    $cache = $this->getOption('cache', 300);
    $response->test(function () use ($context, $cache) {
      $json = $context->drush->variableGet('preprocess_js', '--exact --format=json')->parseJson(TRUE);
      $output = (int) $json['preprocess_js'];
      return $output;
    });

    return $response;
  }
}
