<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\AuditResponse\AuditResponse;

class PreprocessCss extends Check {
  public function check() {
    $response = new AuditResponse('variable/preprocess_css', $this);

    $response->test(function ($check) {
      $context = $check->context;
      $json = $context->drush->variableGet('preprocess_css', '--exact --format=json')->parseJson(TRUE);
      if (is_int($json)) {
        return $json;
      }
      elseif (is_array($json)) {
        $output = (int) $json['preprocess_css'];
      }
      return FALSE;
    });

    return $response;
  }
}
