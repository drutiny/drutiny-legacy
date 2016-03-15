<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\AuditResponse\AuditResponse;

class PageCacheMaximumAge extends Check {
  public function check() {
    $response = new AuditResponse('variable/pagecache', $this);

    $response->test(function ($check) {
      $context = $check->context;
      $json = $context->drush->variableGet('page_cache_maximum_age', '--exact --format=json')->parseJson(TRUE);
      if (is_int($json) || is_string($json)) {
        $check->setToken('value', $json);
        return ((int)$json) >= $check->getOption('cache', 300);
      }
      elseif (is_array($json)) {
        $output = (int) $json['page_cache_maximum_age'];
        $check->setToken('value', $output);
        return $output >= $check->getOption('cache', 300);
      }
      return FALSE;
    });

    return $response;
  }
}
