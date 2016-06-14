<?php

namespace SiteAudit\Check\Drush;

use SiteAudit\Check\Check;
use SiteAudit\Executor\DoesNotApplyException;

class Search404 extends Check {
  static public function getNamespace()
  {
    return 'variable/search_404';
  }

  public function check()
  {
    // If the module is disabled, then no search404.
    if ($this->context->drush->moduleEnabled('search404')) {

      // There is a variable that can skip automatic searching, which is
      // desirable from a performance perspective.
      $skip_auto_search = (bool) $this->context->drush->getVariable('search404_skip_auto_search', FALSE);
      if (!$skip_auto_search) {
        return FALSE;
      }
    }
    // If the module is not enabled, then this check does not apply.
    else {
      throw new DoesNotApplyException();
    }

    return TRUE;
  }
}
