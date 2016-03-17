<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\AuditResponse\AuditResponse;

class UpdateDBStatus extends Check {
  public function check() {
    $response = new AuditResponse('system/updatedb', $this);
    $context = $this->context;
    $cache = $this->getOption('cache', 300);
    $response->test(function () use ($context, $cache) {
      $output = $context->drush->updatedbStatus()->getOutput();
      return strpos($output[0], "No database updates required") === 0;
    });

    return $response;
  }
}
