<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\AuditResponse\AuditResponse;
use SiteAudit\Executor\DoesNotApplyException;

class Search404 extends Check {
  public function check() {
    $response = new AuditResponse('variable/search_404', $this);

    $response->test(function ($check) {
      $context = $check->context;

      // If the module is disabled, then no search404.
      if ($check->context->drush->moduleEnabled('search404')) {

        // There is a variable that can skip automatic searching, which is
        // desirable from a performance perspective.
        $skip_auto_search = (bool) $context->drush->getVariable('search404_skip_auto_search', FALSE);
        if (!$skip_auto_search) {
          return FALSE;
        }
      }
      // If the module is not enabled, then this check does not apply.
      else {
        throw new DoesNotApplyException();
      }

      return TRUE;
    });

    return $response;
  }
}
