<?php

namespace SiteAudit\Check\Drush;

use SiteAudit\Check\Check;
use SiteAudit\Executor\DoesNotApplyException;

class SearchDB extends Check {
  static public function getNamespace()
  {
    return 'variable/search_db';
  }

  public function check() {
    if (!$this->context->drush->moduleEnabled('search')) {
      throw new DoesNotApplyException();
    }

    if ($this->context->drush->moduleEnabled('search_api_db')) {
      return FALSE;
    }

    return TRUE;

  }
}

