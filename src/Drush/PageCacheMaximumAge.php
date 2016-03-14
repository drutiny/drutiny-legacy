<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\AuditResponse\AuditResponse;

class PageCacheMaximumAge extends Check {
  public function check() {
    $response = new AuditResponse('variable/pagecache');
    $context = $this->context;
    $cache = $this->getOption('cache', 300);
    $response->test(function () use ($context, $cache) {
      $json = $context->drush->variableGet('page_cache_maximum_age', '--exact --format=json')->parseJson(TRUE);
      $output = (int) $json['page_cache_maximum_age'];
      return $output >= $cache;
    });

    return $response;
  }
}
