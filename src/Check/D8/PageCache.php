<?php

namespace Drutiny\Check\D8;

use Drutiny\Check\Check;

/**
 * @Drutiny\Annotation\CheckInfo(
 *  title = "Page Cache",
 *  description = "Ensure you page cache expiry is set to an optimal level for best performance.",
 *  remediation = "Set the configuration object <code>system.performance</code> key <code>cache.page.max_age</code> to <code>:page_cache</code>.",
 *  success = "Page cache is set above <code>:page_cache</code> seconds. Currently set to <code>:current</code>.",
 *  failure = "Page cache is not set above <code>:page_cache</code> seconds. Currently set to <code>:current</code>.",
 *  exception = "Could not determine page cache settings.",
 *  supports_remediation = TRUE,
 * )
 */
class PageCache extends Check {

  /**
   * @inheritDoc
   */
  public function check() {
    $page_cache = (int) $this->getOption('page_cache', 3600);
    $current = (int) $this->context->drush->getConfig('system.performance', 'cache.page.max_age');
    $this->setToken('page_cache', $page_cache);
    $this->setToken('current', $current);
    return $current >= $page_cache;
  }

  /**
   * @inheritDoc
   */
  public function remediate() {
    $page_cache = (int) $this->getOption('page_cache', 3600);
    $res = $this->context->drush->configSet('system.performance', 'cache.page.max_age', $page_cache);
    return $res->isSuccessful();
  }

}
