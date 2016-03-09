<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\Base\AuditResponse;

class PageCacheMaximumAge extends Check {
  public function check() {
    $json = $this->context->drush->variableGet('page_cache_maximum_age', '--exact --format=json')->parseJson(TRUE);
    $output = (int) $json['page_cache_maximum_age'];

    $response = new AuditResponse();
    $response->setDescription("Using Pressflow 6 or 7, with a page cache max age set to less than 5 minutes, the server has to frequently regenerate pages which can decrease your site's performance.");
    $response->setRemediation("Enable page cache max-age on Drupal's Performance page");

    $cache = $this->getOption('cache', 300);

    if ($output >= $cache) {
      $response->setSuccess("Page cache max-age at least ${cache} seconds, actual value ${output} seconds");
    }
    else {
      $response->setFailure("Page cache max-age less than ${cache} seconds, actual value ${output} seconds");
    }

    return $response;
  }
}
