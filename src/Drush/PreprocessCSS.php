<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\AuditResponse\AuditResponse;

class PreprocessCss extends Check {
  public function check() {
    $response = new AuditResponse('variable/preprocess_css', $this);

    $response->test(function ($check) {
      $context = $check->context;
      $json = (int) $context->drush->getVariable('preprocess_css', 0);
      return (bool) $json;
    });

    return $response;
  }
}
