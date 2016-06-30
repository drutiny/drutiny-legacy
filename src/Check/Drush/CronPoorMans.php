<?php

namespace SiteAudit\Check\Drush;

use SiteAudit\Check\Check;
use SiteAudit\Annotation\CheckInfo;

/**
 * @CheckInfo(
 *  title = "Poor mans cron",
 *  description = "Checks that poor mans cron is disabled and will never run with a web thread.",
 *  remediation = "Set the variable <code>cron_safe_threshold</code> to <code>0</code>.",
 *  success = "Poor mans cron is disabled.",
 *  failure = "Poor mans cron is still enabled, current set to run every <code>:cron_safe_threshold</code> seconds.",
 *  exception = "Could not determine status of poor mans cron: :exception."
 * )
 */
class CronPoorMans extends Check {
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
