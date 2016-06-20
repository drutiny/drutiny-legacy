<?php

namespace SiteAudit\Check\Drush;

use SiteAudit\Check\Check;

class PageCacheMaximumAge extends Check {
  static public function getNamespace()
  {
    return 'variable/pagecache';
  }

  public function check() {
    $page_cache_maximum_age = (int) $this->context->drush->getVariable('page_cache_maximum_age', 0);
    $this->setToken('page_cache_maximum_age', $page_cache_maximum_age);
    return ($page_cache_maximum_age >= $this->getOption('cache', 300));
  }
}
