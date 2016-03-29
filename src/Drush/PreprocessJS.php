<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\AuditResponse\AuditResponse;

class PreprocessJS extends Check {
  public function check() {
    $response = new AuditResponse('variable/preprocess_js', $this);

    $response->test(function ($check) {
      $context = $check->context;
      $json = $context->drush->getVariable('preprocess_js', 0);
      if (is_int($json)) {
        return (bool) $json;
      }
      return FALSE;
    });

    return $response;
  }
}
