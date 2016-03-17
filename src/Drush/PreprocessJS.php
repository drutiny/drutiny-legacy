<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\AuditResponse\AuditResponse;

class PreprocessJS extends Check {
  public function check() {
    $response = new AuditResponse('variable/preprocess_js', $this);

    $response->test(function ($check) {
      $context = $check->context;
      $json = $context->drush->variableGet('preprocess_js', '--exact --format=json')->parseJson(TRUE);
      if (is_int($json)) {
        return $json;
      }
      elseif (is_array($json)) {
        $output = (int) $json['preprocess_js'];
      }
      return FALSE;
    });

    return $response;
  }
}
