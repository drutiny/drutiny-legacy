<?php

namespace SiteAudit\Check\Drush;

use SiteAudit\Check\Check;

class PageCacheMaximumAge extends Check {
  static public function getNamespace()
  {
    return 'variable/pagecache';
  }

  public function check() {
    $value = $this->context->drush->getVariable('page_cache_maximum_age', 0);
    if (is_int($value) || is_string($value)) {
      $this->setToken('value', $value);
      return ((int) $value) >= $this->getOption('cache', 300);
    }

    return FALSE;
  }
}
