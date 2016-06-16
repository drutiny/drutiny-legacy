<?php

namespace SiteAudit\Check\Drush;

use SiteAudit\Check\Check;

class CronPoorMans extends Check {

  static public function getNamespace()
  {
    return 'system/cron_poor_mans';
  }

  public function check()
  {
    // @see DRUPAL_CRON_DEFAULT_THRESHOLD which defaults to 10800.
    $cron_safe_threshold = (int) $this->context->drush->getVariable('cron_safe_threshold', 10800);
    $this->setToken('cron_safe_threshold', $cron_safe_threshold);
    if ($cron_safe_threshold > 0) {
      return FALSE;
    }
    return TRUE;
  }
}
