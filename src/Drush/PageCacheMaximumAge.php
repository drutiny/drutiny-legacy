<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\Base\AuditResponse;
use SiteAudit\Executor\ResultException;
use Symfony\Component\Console\Output\OutputInterface;

class PageCacheMaximumAge extends Check {
  public function check() {
    $response = new AuditResponse();
    $response->setDescription("Using Pressflow 6 or 7, with a page cache max age set to less than 5 minutes, the server has to frequently regenerate pages which can decrease your site's performance.");
    $response->setRemediation("Enable page cache max-age on Drupal's Performance page");

    $output = (int) $this->context->drush->getVariable('page_cache_maximum_age', 0);
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
