<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\AuditResponse\AuditResponse;

class PageCacheMaximumAge extends Check {
  public function check() {
    $response = new AuditResponse('variable/pagecache', $this);

    $response->test(function ($check) {
      $context = $check->context;
      $value = $context->drush->getVariable('page_cache_maximum_age', 0);

      if (is_int($value) || is_string($value)) {
        $check->setToken('value', $value);
        return ((int) $value) >= $check->getOption('cache', 300);
      }

      return FALSE;
    });

    return $response;
  }
}
