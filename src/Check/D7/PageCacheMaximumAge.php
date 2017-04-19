<?php

namespace Drutiny\Check\D7;

use Drutiny\Check\Check;

/**
 * @Drutiny\Annotation\CheckInfo(
 *  title = "Page cache",
 *  description = "Ensure you page cache expiry is set to an optimal level for best performance.",
 *  remediation = "Set the variable <code>page_cache_maximum_age</code> to be greater than <code>300</code>.",
 *  success = "Page cache max-age is set above <code>:cache</code> seconds. Currently set to <code>:page_cache_maximum_age</code>.",
 *  failure = "Page cache max-age is not set above <code>:cache</code> seconds. Currently set to <code>:page_cache_maximum_age</code>.",
 *  exception = "Could not determine page cache max-age.",
 * )
 */
class PageCacheMaximumAge extends Check {

  /**
   * @inheritDoc
   */
  public function check() {
    $cache = (int) $this->getOption('cache', 300);
    $page_cache_maximum_age = (int) $this->context->drush->getVariable('page_cache_maximum_age', 0);
    $this->setToken('cache', $cache);
    $this->setToken('page_cache_maximum_age', $page_cache_maximum_age);
    return $page_cache_maximum_age >= $cache;
  }

  /**
   * @inheritDoc
   */
  public function remediate() {
    $cache = (int) $this->getOption('cache', 300);
    $res = $this->context->drush->setVariable('page_cache_maximum_age', $cache);
    return $res->isSuccessful();
  }

}
