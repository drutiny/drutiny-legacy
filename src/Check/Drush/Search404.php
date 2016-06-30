<?php

namespace SiteAudit\Check\Drush;

use SiteAudit\Check\Check;
use SiteAudit\Executor\DoesNotApplyException;
use SiteAudit\Annotation\CheckInfo;

/**
 * @CheckInfo(
 *  title = "Search 404",
 *  description = "Search 404 can cause performance impacts to your site if it is enabled and set to automatically search upon encountering a 404.",
 *  remediation = "Set the variable <code>search404_skip_auto_search</code> to be <code>TRUE</code>.",
 *  not_available = "Search 404 module is disabled.",
 *  success = "Search 404 is set to not auto search.",
 *  failure = "Search 404 is set to auto search.",
 *  exception = "Could not determine Search 404 setting.",
 * )
 */
class Search404 extends Check {
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
